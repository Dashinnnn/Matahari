<?php
file_put_contents('debug_log.txt', print_r($_POST, true));
file_put_contents('debug_log.txt', print_r($_FILES, true), FILE_APPEND);

error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("post_max_size: " . ini_get('post_max_size'));
error_log("max_input_vars: " . ini_get('max_input_vars'));

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); 
    exit();
}

// Log raw input data for debugging
error_log("Raw input: " . file_get_contents('php://input'));

// DATABASE CONFIG
$url = parse_url(getenv("JAWSDB_URL"));
$servername = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$dbname = substr($url["path"], 1);
$port = isset($url["port"]) ? $url["port"] : 3306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Token Validation (only for non-OPTIONS requests)
$headers = apache_request_headers();
$token = isset($headers['Authorization']) ? str_replace("Bearer ", "", $headers['Authorization']) : null;

if (!$token) {
    echo json_encode(["status" => "error", "message" => "No token provided"]);
    http_response_code(401);
    $conn->close();
    exit();
}

$tokenQuery = "SELECT * FROM accounts WHERE token = ?";
$stmt = $conn->prepare($tokenQuery);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
if (!$result->fetch_assoc()) {
    echo json_encode(["status" => "error", "message" => "Invalid token"]);
    http_response_code(401);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Proceed with request handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $barangay = isset($_POST['barangay']) ? $conn->real_escape_string($_POST['barangay']) : '';
    $designation = isset($_POST['designation']) ? $conn->real_escape_string($_POST['designation']) : '';
    $payroll = isset($_POST['payroll']) ? $conn->real_escape_string($_POST['payroll']) : '';
    $aor = isset($_POST['aor']) ? $conn->real_escape_string($_POST['aor']) : '';
    $precinct = isset($_POST['precinct']) ? $conn->real_escape_string($_POST['precinct']) : '';
    $geopoint = isset($_POST['geopoint']) ? $conn->real_escape_string($_POST['geopoint']) : '';
    $sp_family = isset($_POST['sp_family']) ? $conn->real_escape_string($_POST['sp_family']) : '';
    $sp_affiliate = isset($_POST['sp_affiliate']) ? $conn->real_escape_string($_POST['sp_affiliate']) : '';
    $pt = isset($_POST['pt']) ? $conn->real_escape_string($_POST['pt']) : '';
    $remarks = isset($_POST['remarks']) ? $conn->real_escape_string($_POST['remarks']) : '';
    $congressman = isset($_POST['congressman']) ? $conn->real_escape_string($_POST['congressman']) : '';
    $photo = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        $photo_path = "photo/$photo_name";

        if (move_uploaded_file($photo_tmp, $photo_path)) {
            $photo = $photo_name;
        }
    }

    if ($payroll !== "YES" && $payroll !== "NO") {
        $payroll = "NO";
    }

    $sql = "INSERT INTO calaca (name, barangay, designation, payroll, aor, precinct, geopoint, sp_family, sp_affiliate, pt, remarks, congressman, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssss",
        $name,
        $barangay,
        $designation,
        $payroll,
        $aor,
        $precinct,
        $geopoint,
        $sp_family,
        $sp_affiliate,
        $pt,
        $remarks,
        $congressman,
        $photo
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding data: " . $stmt->error]);
    }

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['validate_token'])) {
        $tokenQuery = "SELECT * FROM accounts WHERE token =?";
        $stmt = $conn->prepare($tokenQuery);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            echo json_encode(["status" => "success", "message" => "Token is valid"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid token"]);
            http_response_code(401);
        }
        $stmt->close();
        $conn->close();
        exit();
    }

    // HANDLE GET METHOD TO FETCH DATA
    $searchQuery = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Base SQL query to only select rows where isDeleted = 'NO'
    $sql = "SELECT id, name, barangay, designation, payroll, aor, precinct, geopoint, sp_family, sp_affiliate, pt, remarks, congressman, photo FROM calaca WHERE isDeleted = 'NO'";

    // Add conditions based on id or search query
    if ($id > 0) {
        $sql .= " AND id = $id"; 
    } elseif (!empty($searchQuery)) {
        $sql .= " AND name LIKE '%$searchQuery%'"; 
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Modify photo path if available
            if (!empty($row['photo'])) {
                $row['photo'] = "https://matahari-backend-e0beedf72643.herokuapp.com/photo/" . $row['photo'];
            } else {
                $row['photo'] = null;
            }
            $data[] = $row;
        }
    }

    echo json_encode(["status" => "success", "data" => $data]);

}  elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Parse multipart/form-data manually if needed
    $input = file_get_contents("php://input");
    $boundary = substr($input, 0, strpos($input, "\r\n"));
    $parts = array_slice(explode($boundary, $input), 1);
    $postData = [];
    $files = [];

    foreach ($parts as $part) {
        if (empty($part) || $part === "--\r\n") continue;
        $headers = [];
        $body = '';
        $lines = explode("\r\n", trim($part));
        $isFile = false;
        $name = '';

        foreach ($lines as $line) {
            if (empty($line)) {
                $body = implode("\r\n", array_slice($lines, array_search($line, $lines) + 1));
                break;
            }
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(': ', $line, 2);
                $headers[$key] = $value;
            }
        }

        if (isset($headers['Content-Disposition'])) {
            preg_match('/name="([^"]+)"/', $headers['Content-Disposition'], $matches);
            $name = $matches[1] ?? '';
            if (strpos($headers['Content-Disposition'], 'filename') !== false) {
                $isFile = true;
                preg_match('/filename="([^"]+)"/', $headers['Content-Disposition'], $fileMatches);
                $filename = $fileMatches[1] ?? '';
                $files[$name] = ['name' => $filename, 'content' => $body];
            } else {
                $postData[$name] = $body;
            }
        }
    }

    // Log parsed data
    error_log("PUT Request - Parsed POST: " . print_r($postData, true));
    error_log("PUT Request - Parsed FILES: " . print_r($files, true));

    $id = isset($postData['id']) ? intval($postData['id']) : 0;
    $action = isset($postData['action']) ? $postData['action'] : '';

    if ($action !== 'update' || $id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid action or ID: action=$action, id=$id"]);
        http_response_code(400);
        $conn->close();
        exit();
    }

    // Fetch current record
    $sql = "SELECT * FROM calaca WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentData = $result->fetch_assoc();
    $stmt->close();

    if (!$currentData) {
        echo json_encode(["status" => "error", "message" => "Record not found"]);
        http_response_code(404);
        $conn->close();
        exit();
    }

    // Use current data as defaults
    $name = isset($postData['name']) ? $conn->real_escape_string($postData['name']) : $currentData['name'];
    $barangay = isset($postData['barangay']) ? $conn->real_escape_string($postData['barangay']) : $currentData['barangay'];
    $designation = isset($postData['designation']) ? $conn->real_escape_string($postData['designation']) : $currentData['designation'];
    $payroll = isset($postData['payroll']) ? $conn->real_escape_string($postData['payroll']) : $currentData['payroll'];
    $aor = isset($postData['aor']) ? $conn->real_escape_string($postData['aor']) : $currentData['aor'];
    $precinct = isset($postData['precinct']) ? $conn->real_escape_string($postData['precinct']) : $currentData['precinct'];
    $geopoint = isset($postData['geopoint']) ? $conn->real_escape_string($postData['geopoint']) : $currentData['geopoint'];
    $sp_family = isset($postData['sp_family']) ? $conn->real_escape_string($postData['sp_family']) : $currentData['sp_family'];
    $sp_affiliate = isset($postData['sp_affiliate']) ? $conn->real_escape_string($postData['sp_affiliate']) : $currentData['sp_affiliate'];
    $pt = isset($postData['pt']) ? $conn->real_escape_string($postData['pt']) : $currentData['pt'];
    $remarks = isset($postData['remarks']) ? $conn->real_escape_string($postData['remarks']) : $currentData['remarks'];
    $congressman = isset($postData['congressman']) ? $conn->real_escape_string($postData['congressman']) : $currentData['remarks'];
    $photo = $currentData['photo'];

    // Handle photo upload
    if (isset($files['photo']) && !empty($files['photo']['content'])) {
        $photo_name = uniqid() . "_" . $files['photo']['name'];
        $photo_path = "photo/$photo_name";
        if (file_put_contents($photo_path, $files['photo']['content'])) {
            $photo = $photo_name;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload photo"]);
            http_response_code(500);
            $conn->close();
            exit();
        }
    }

    // Update the record
    $sql = "UPDATE calaca
            SET name = ?, barangay = ?, designation = ?, payroll = ?, aor = ?, precinct = ?, geopoint = ?, sp_family = ?, sp_affiliate = ?, pt = ?, remarks = ?, congressman = ?, photo = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssi",
        $name,
        $barangay,
        $designation,
        $payroll,
        $aor,
        $precinct,
        $geopoint,
        $sp_family,
        $sp_affiliate,
        $pt,
        $remarks,
        $congressman,
        $photo,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating data: " . $stmt->error]);
        http_response_code(500);
    }

    $stmt->close();
}

$conn->close();
?>