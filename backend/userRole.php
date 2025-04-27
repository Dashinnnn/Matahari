<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

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

$headers = getallHeaders();
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);

$sql = "SELECT level FROM accounts WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $role = $user['level'];

    echo json_encode([
        "status" => "success",
        "data" => [
            "role" => $role
        ]
        ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid token"]);
}

$stmt->close();
$conn->close();
?>
