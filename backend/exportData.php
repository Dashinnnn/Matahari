<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\xlsx;

// DATABASE CONFIG
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

// FETCH DATA FROM 'calaca' TABLE
$sql = "SELECT id, name, barangay, designation, payroll, sp_family, sp_affiliate, aor, pt, geopoint, photo, precinct FROM calaca WHERE isDeleted = 'NO'";
$result = $conn->query($sql);

// CREATE A NEW SPREADSHEET OBJECT
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// SET HEADER ROW VALUES
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Name');
$sheet->setCellValue('C1', 'Barangay');
$sheet->setCellValue('D1', 'Designation');
$sheet->setCellValue('E1', 'Payroll');
$sheet->setCellValue('F1', 'SP Family');
$sheet->setCellValue('G1', 'SP Affiliate');
$sheet->setCellValue('H1', 'Area of Responsibility');
$sheet->setCellValue('I1', 'Political Tendency');
$sheet->setCellValue('J1', 'Geopoint');
$sheet->setCellValue('K1', 'Precinct');
$sheet->setCellValue('L1', 'Photo (Link)');

// FILL DATA ROWS WITH TEXT DATA AND PHOTO LINKS
if ($result->num_rows > 0) {
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['id']);
        $sheet->setCellValue('B' . $rowIndex, $row['name']);
        $sheet->setCellValue('C' . $rowIndex, $row['barangay']);
        $sheet->setCellValue('D' . $rowIndex, $row['designation']);
        $sheet->setCellValue('E' . $rowIndex, $row['payroll']);
        $sheet->setCellValue('F' . $rowIndex, $row['sp_family']);
        $sheet->setCellValue('G' . $rowIndex, $row['sp_affiliate']);
        $sheet->setCellValue('H' . $rowIndex, $row['aor']);
        $sheet->setCellValue('I' . $rowIndex, $row['pt']);
        $sheet->setCellValue('K' . $rowIndex, $row['precinct']);

        // Check if 'photo' column has image file name
        if (!empty($row['photo'])) {
            $photoLink = 'https://matahari-backend-e0beedf72643.herokuapp.com/photo/' . $row['photo'];

            // Add a hyperlink to the cell
            $sheet->setCellValue('L' . $rowIndex, 'View Photo');
            $sheet->getCell('L' . $rowIndex)->getHyperlink()->setUrl($photoLink); 
        } else {
            $sheet->setCellValue('L' . $rowIndex, 'No Image'); 
        }

        $rowIndex++;
    }
} else {
    die("No records found in the table.");
}

// WRITE SPREADSHEET TO FILE
$writer = new xlsx($spreadsheet);
$filename = 'calaca_data_with_photo_links.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');

$conn->close();
?>
