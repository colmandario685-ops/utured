<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
    $fileName = $_FILES['foto_perfil']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $uploadFileDir = __DIR__ . '/uploads/';

        if (!file_exists($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $newFileName = 'user_' . $id_usuario . '_' . time() . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Eliminar la foto anterior si existe
            $stmtOld = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuario = ?");
            $stmtOld->execute([$id_usuario]);
            $oldFoto = $stmtOld->fetchColumn();

            if ($oldFoto && file_exists($uploadFileDir . $oldFoto)) {
                unlink($uploadFileDir . $oldFoto);
            }

            // Actualizar en base de datos
            $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?");
            $stmt->execute([$newFileName, $id_usuario]);
        }
    }
}

header("Location: perfil.php");
exit();