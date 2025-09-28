<?php
require("conexao.php");

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int) $_POST['id'];
        $data = $_POST['data'];
        $quantidade_total = $_POST['quantidade_total'];
        $tanque = $_POST['tanque'];

        $stmt = $banco->prepare("UPDATE lote_leite 
                                 SET data = :data, quantidade_total = :quantidade_total, tanque = :tanque 
                                 WHERE id = :id");
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':quantidade_total', $quantidade_total);
        $stmt->bindValue(':tanque', $tanque);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: area_comum_aluno.php?secao=lotes&msg=Lote atualizado com sucesso!");
        exit;
    } else {
        throw new Exception("Requisição inválida.");
    }
} catch (Exception $e) {
    echo "<p><strong>Erro: " . $e->getMessage() . "</strong></p>";
}
?>
