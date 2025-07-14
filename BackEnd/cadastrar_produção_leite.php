<?php
require('conexao.php'); // Inclui a conexão com o banco de dados

// Se o formulário foi enviado com os campos necessários
if (isset($_POST['id_vaca'], $_POST['quantidade'], $_POST['data'])) {
    $id_vaca = $_POST['id_vaca']; // ID da vaca
    $quantidade = $_POST['quantidade']; // Quantidade de leite
    $data = $_POST['data']; // Data da produção

    // Prepara a query de inserção
    $stmt = $banco->prepare("INSERT INTO producao_leite (id_vaca, quantidade, data) VALUES (:id_vaca, :quantidade, :data)");
    $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
    $stmt->bindValue(':quantidade', $quantidade);
    $stmt->bindValue(':data', $data);

    // Executa a query e trata o resultado
    if ($stmt->execute()) {
        echo "<p>Dados da produção de leite inseridos com sucesso!</p>";
    } else {
        echo "<p>Erro ao inserir os dados.</p>";
        print_r($stmt->errorInfo()); // Mostra detalhes do erro
    }
}
?>
