<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);

    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $name = $_PUT['name'] ?? '';
    $barangay = $_PUT['barangay'] ?? '';
    $designation = $_PUT['designation'] ?? '';
    $payroll = $_PUT['payroll'] ?? '';
    $aor = $_PUT['aor'] ?? '';
    $precinct = $_PUT['precinct'] ?? '';
    $geopoint = $_PUT['geopoint'] ?? '';
    $sp_family = $_PUT['sp_family'] ?? '';
    $sp_affiliate = $_PUT['sp_affiliate'] ?? '';
    $pt = $_PUT['pt'] ?? '';

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        exit;
    }

    $sql = "UPDATE calaca SET 
            name = ?, 
            barangay = ?, 
            designation = ?, 
            payroll = ?, 
            aor = ?, 
            precinct = ?, 
            geopoint = ?, 
            sp_family = ?, 
            sp_affiliate = ?, 
            pt = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        'ssssssssss',
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
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update data: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();

?>
