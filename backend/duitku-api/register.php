<?php
// register.php — Registrasi User Baru

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$nama = trim($data['nama'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validasi input
if (empty($nama) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Semua field wajib diisi"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format email tidak valid"]);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Password minimal 6 karakter"]);
    exit();
}

try {
    // Cek email sudah terdaftar atau belum
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar"]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)");
    $stmt->bindParam(":nama", $nama);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashedPassword);
    $stmt->execute();

    $newId = $conn->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Registrasi berhasil",
        "data" => [
            "id" => (int)$newId,
            "nama" => $nama,
            "email" => $email
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server: " . $e->getMessage()]);
}
