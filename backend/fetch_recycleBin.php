<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$url = parse_url(getenv("JAWSDB_URL"));
$servername = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$dbname = substr($url["path"], 1);
$port = isset($url["port"]) ? $url["port"] : 3306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Token Validation
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM calaca WHERE isDeleted = 'YES'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "data" => [],
            "message" => "No data found in the recycle bin"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}

$conn->close();
?>