<?php
require("conexao.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_lote'], $_POST['data'], $_POST['quantidade_total'], $_POST['tanque'])) {
        throw new Exception("Dados incompletos.");
    }

    $id = (int) $_POST['id_lote'];
    $data = $_POST['data'];
    $quantidade = $_POST['quantidade_total'];
    $tanque = $_POST['tanque'];

    $stmt = $banco->prepare("UPDATE lote_leite SET data_lote = :data, quantidade_total = :quantidade, id_tanque = (SELECT id_tanque FROM tanque WHERE localizacao = :tanque) WHERE id_lote = :id");
    $stmt->bindValue(':data', $data);
    $stmt->bindValue(':quantidade', $quantidade);
    $stmt->bindValue(':tanque', $tanque);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao atualizar lote.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
