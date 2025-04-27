<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $verified = isset($data['verified']) ? $data['verified'] : '';

    if ($id > 0 && ($verified === 'YES' || $verified === 'NO')) {
        $sql = "UPDATE accounts SET verified = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $verified, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Account verified successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to verify account: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid ID or verified value"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>