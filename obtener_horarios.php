<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

$curso = $_GET['curso'] ?? '';

if (empty($curso)) {
    echo json_encode([]);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT turno, num_modulo, dia_semana, materia FROM horarios WHERE curso_code = ?");
    $stmt->execute([$curso]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}