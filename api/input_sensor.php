<?php

include "../config/config.php";

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

// Ambil data dari JSON body
$data = json_decode(file_get_contents('php://input'), true);

// Validasi input
if (!isset($data['tds']) || !isset($data['suhu'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

$tds = $data['tds'];
$suhu   = $data['suhu'];

// Insert ke database
$query = "INSERT INTO sensor (tds_value, temperature_value) VALUES ($tds, $suhu)";
$result = mysqli_query($conn, $query);

if ($result) {
    echo json_encode(["status" => "success", "message" => "Data berhasil disimpan"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data"]);
}

?>
