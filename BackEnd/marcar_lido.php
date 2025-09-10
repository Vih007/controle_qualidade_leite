<?php
require("conexao.php");

// Aceita id tanto via POST quanto GET (para garantir)
$id = $_POST['id'] ?? $_GET['id'] ?? null;

header('Content-Type: application/json');

if ($id) {
    $stmt = $banco->prepare("UPDATE alertas SET lido = 1 WHERE id_alerta = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao atualizar no banco.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não recebido.']);
}