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

$data = json_decode(file_get_contents("php://input"), true);

//CHECK IF THE USERNAME AND PASS ARE SET
$username = isset($data['username']) ? $conn->real_escape_string(trim($data['username'])) : '';
$password = isset($data['password']) ? $conn->real_escape_string(trim($data['password'])) : '';

//CONDITION IF USERNAME OR PASSWORD IS EMPTY
if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Username or password cannot be empty"]);
    exit();
}

// SQL QUERY TO FIND THE USER
$query = "SELECT * FROM accounts WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// CHECKS IF THE USER EXIST AND THE PASS MATCHES
if ($user && password_verify($password, $user['password'])) {
    if ($user['verified'] === 'YES') {

        $token = bin2hex(random_bytes(16));
        $updateQuery = "UPDATE accounts SET token = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $token, $username);
        $updateStmt->execute();
        $updateStmt->close();

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "token" => $token,
            "data" => [
                "username" => $user['username'],
                "role" => trim($user['level'])
            ]
            ]);
    } else {
        echo json_encode(["success" => false, "message" => "Account not verified"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid Credentials"]);
}

$stmt->close();
$conn->close();
?>
