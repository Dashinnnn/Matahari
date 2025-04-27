<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Add OPTIONS here
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

    $sql = "SELECT id, fname, lname, username, verified, level FROM accounts WHERE level IN ('CLIENT', 'ADMIN' , 'UNKNOWN')";

    if (!empty($searchQuery)) {
        $escapedQuery = $conn->real_escape_string($searchQuery);
        $sql .= " AND (fname LIKE '%$escapedQuery%' 
                  OR lname LIKE '%$escapedQuery%' 
                  OR username LIKE '%$escapedQuery%'
                  OR verified LIKE '%$escapedQuery%'
                  OR level LIKE '%$escapedQuery%')";
    }

    $result = $conn->query($sql);

    if ($result) {
        $accounts = [];

        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $accounts,
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Query failed: " . $conn->error,
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method",
    ]);
}

$conn->close();
?>