<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

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

$data = json_decode(file_get_contents("php://input"), true);

// CHECKS IF THE FOLLOWING ARE SET
$fname = isset($data['fname']) ? $conn->real_escape_string($data['fname']) : '';
$lname = isset($data['lname']) ? $conn->real_escape_string($data['lname']) : '';
$username = isset($data['username']) ? $conn->real_escape_string($data['username']) : '';
$password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : '';

// CHECKS IF ANY FIELDS ARE LEFT EMPTY
if (empty($fname) || empty($lname) || empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit();
}

// PREP SQL QUERY FOR REGISTRATION
$query = "INSERT INTO accounts (fname, lname, username, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $fname, $lname, $username, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration Successful"]);
} else {
    echo json_encode(["success" => false, "message" => "Registration Failed"]);
}

$stmt->close();
$conn->close();
?>
