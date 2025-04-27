<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

$url = parse_url(getenv("JAWSDB_URL"));
$servername = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$dbname = substr($url["path"], 1);
$port = isset($url["port"]) ? $url["port"] : 3306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//CHECK IF REQUEST METHOD IS PUT

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    //GET RAW DATA FROM REQUEST
    $data = json_decode(file_get_contents("php://input"));

    // CHECK IF ID IS PROVIDED
    if (isset($data->id)) {
        $id = $conn->real_escape_string($data->id);

        $sql = "UPDATE calaca SET isDeleted = 'YES' WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt -> affected_rows > 0) {
                echo json_encode(["status" => "success", "message" => "Data soft deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "No data found with the ID provided"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error executing the query: ". $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "ID is required for deletion"]);
    }
}

$conn->close();
?>