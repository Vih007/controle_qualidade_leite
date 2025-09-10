<?php
session_start(); // Inicia a sessão para uso de variáveis de sessão
require('conexao.php');  // Inclui a conexão com o banco de dados

// Verifica se os dados do formulário foram enviados
if (isset($_POST['id_vaca'], $_POST['data'], $_POST['resultado'], $_POST['quantas_cruzes'])) {
    $id_vaca = $_POST['id_vaca']; // ID da vaca
    $data = $_POST['data']; // Data do teste
    // Converte o resultado para 'positivo' ou 'negativo'
    $resultado = ($_POST['resultado'] == '1') ? 'positivo' : 'negativo';
    $quantas_cruzes = $_POST['quantas_cruzes']; // Quantidade de cruzes no teste

    // Se houve seleção de ubere, junta os valores em uma string separada por vírgulas
    if (isset($_POST['ubere']) && is_array($_POST['ubere'])) {
        $ubere = implode(", ", $_POST['ubere']);
    } else {
        $ubere = "";
    }

    $tratamento = $_POST['tratamento'] ?? ""; // Tratamento informado ou vazio
    $observacoes = $_POST['observacoes'] ?? ""; // Observações ou vazio

    // Prepara a query de inserção no banco de dados
    $stmt = $banco->prepare("INSERT INTO teste_mastite (id_vaca, data, resultado, quantas_cruzes, ubere, tratamento, observacoes) VALUES (:id_vaca, :data, :resultado, :quantas_cruzes, :ubere, :tratamento, :observacoes)");

    // Bind dos parâmetros
    $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
    $stmt->bindValue(':data', $data);
    $stmt->bindValue(':resultado', $resultado);
    $stmt->bindValue(':quantas_cruzes', $quantas_cruzes, PDO::PARAM_INT);
    $stmt->bindValue(':ubere', $ubere);
    $stmt->bindValue(':tratamento', $tratamento);
    $stmt->bindValue(':observacoes', $observacoes);

    // Executa a query e seta mensagem na sessão conforme resultado
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Teste adicionado com Sucesso!";
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_teste");
        exit; 
    } else {
        $_SESSION['mensagem'] = "Erro ao adicionar Teste de Mastite! Verifique os detalhes acima."; 
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_teste");
        exit;
    }
}
?>
