<?php
// transaksi.php — CRUD Transaksi

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];
$daftarKategoriValid = ["Makan", "Transport", "Kuliah", "Hiburan", "Lainnya"];

switch ($method) {

    // ================= GET =================
    // GET /transaksi.php?user_id=1        -> semua transaksi milik user
    // GET /transaksi.php?id=5             -> detail satu transaksi
    case 'GET':
        try {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM transaksi WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    http_response_code(404);
                    echo json_encode(["status" => "error", "message" => "Transaksi tidak ditemukan"]);
                    exit();
                }

                echo json_encode(["status" => "success", "data" => $row]);

            } elseif (isset($_GET['user_id'])) {
                $user_id = $_GET['user_id'];
                $stmt = $conn->prepare("SELECT * FROM transaksi WHERE user_id = :user_id ORDER BY tanggal DESC, id DESC");
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(["status" => "success", "data" => $rows]);

            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Parameter user_id atau id wajib disertakan"]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server: " . $e->getMessage()]);
        }
        break;

    // ================= POST =================
    // POST /transaksi.php -> tambah transaksi baru
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $user_id = $data['user_id'] ?? null;
        $nominal = $data['nominal'] ?? null;
        $kategori = trim($data['kategori'] ?? '');
        $tanggal = trim($data['tanggal'] ?? '');
        $catatan = trim($data['catatan'] ?? '');

        // Validasi input
        if (empty($user_id) || $nominal === null || empty($kategori) || empty($tanggal)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "user_id, nominal, kategori, dan tanggal wajib diisi"]);
            exit();
        }

        if (!is_numeric($nominal) || $nominal <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Nominal harus berupa angka lebih besar dari 0"]);
            exit();
        }

        if (!in_array($kategori, $daftarKategoriValid)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Kategori tidak valid"]);
            exit();
        }

        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Format tanggal harus YYYY-MM-DD"]);
            exit();
        }

        try {
            $stmt = $conn->prepare("INSERT INTO transaksi (user_id, nominal, kategori, tanggal, catatan)
                                     VALUES (:user_id, :nominal, :kategori, :tanggal, :catatan)");
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":nominal", $nominal);
            $stmt->bindParam(":kategori", $kategori);
            $stmt->bindParam(":tanggal", $tanggal);
            $stmt->bindParam(":catatan", $catatan);
            $stmt->execute();

            $newId = $conn->lastInsertId();

            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Transaksi berhasil ditambahkan",
                "data" => [
                    "id" => (int)$newId,
                    "user_id" => (int)$user_id,
                    "nominal" => (float)$nominal,
                    "kategori" => $kategori,
                    "tanggal" => $tanggal,
                    "catatan" => $catatan
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server: " . $e->getMessage()]);
        }
        break;

    // ================= PUT =================
    // PUT /transaksi.php -> update transaksi
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $nominal = $data['nominal'] ?? null;
        $kategori = trim($data['kategori'] ?? '');
        $tanggal = trim($data['tanggal'] ?? '');
        $catatan = trim($data['catatan'] ?? '');

        if (empty($id) || $nominal === null || empty($kategori) || empty($tanggal)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id, nominal, kategori, dan tanggal wajib diisi"]);
            exit();
        }

        if (!is_numeric($nominal) || $nominal <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Nominal harus berupa angka lebih besar dari 0"]);
            exit();
        }

        if (!in_array($kategori, $daftarKategoriValid)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Kategori tidak valid"]);
            exit();
        }

        try {
            $stmt = $conn->prepare("SELECT id FROM transaksi WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Transaksi tidak ditemukan"]);
                exit();
            }

            $stmt = $conn->prepare("UPDATE transaksi
                                     SET nominal = :nominal, kategori = :kategori, tanggal = :tanggal, catatan = :catatan
                                     WHERE id = :id");
            $stmt->bindParam(":nominal", $nominal);
            $stmt->bindParam(":kategori", $kategori);
            $stmt->bindParam(":tanggal", $tanggal);
            $stmt->bindParam(":catatan", $catatan);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            echo json_encode([
                "status" => "success",
                "message" => "Transaksi berhasil diperbarui",
                "data" => [
                    "id" => (int)$id,
                    "nominal" => (float)$nominal,
                    "kategori" => $kategori,
                    "tanggal" => $tanggal,
                    "catatan" => $catatan
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server: " . $e->getMessage()]);
        }
        break;

    // ================= DELETE =================
    // DELETE /transaksi.php?id=5
    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Parameter id wajib disertakan"]);
            exit();
        }

        try {
            $stmt = $conn->prepare("SELECT id FROM transaksi WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Transaksi tidak ditemukan"]);
                exit();
            }

            $stmt = $conn->prepare("DELETE FROM transaksi WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            echo json_encode(["status" => "success", "message" => "Transaksi berhasil dihapus"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan server: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
        break;
}
