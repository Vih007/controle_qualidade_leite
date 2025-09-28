<?php
require("conexao.php"); // conexão com o banco

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Coleta e limpa os dados do formulário
        $data = trim($_POST['data'] ?? '');
        $quantidade_total = trim($_POST['quantidade_total'] ?? '');
        $id_tanque = trim($_POST['id_tanque'] ?? '');

        // Validação básica
        if ($data === '' || $quantidade_total === '' || $id_tanque === '') {
            throw new Exception("Preencha todos os campos obrigatórios.");
        }

        // Validação da quantidade
        if (!is_numeric($quantidade_total) || $quantidade_total <= 0) {
            throw new Exception("Informe uma quantidade válida de litros.");
        }

        // Insere no banco (colunas corretas)
        $stmt = $banco->prepare("
            INSERT INTO lote_leite (data_lote, quantidade_total, id_tanque)
            VALUES (:data, :quantidade_total, :id_tanque)
        ");
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':quantidade_total', $quantidade_total);
        $stmt->bindValue(':id_tanque', $id_tanque, PDO::PARAM_INT);
        $stmt->execute();

        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=lotes");
        exit;
    } else {
        throw new Exception("Método inválido.");
    }

} catch (Exception $e) {
    echo "<tr><td colspan='5'><strong>Erro: " . $e->getMessage() . "</strong></td></tr>";
} catch (PDOException $e) {
    echo "<tr><td colspan='5'><strong>Erro no banco: " . $e->getMessage() . "</strong></td></tr>";
}
?>
