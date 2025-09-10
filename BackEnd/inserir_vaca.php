<?php
session_start(); // Inicia a sessão para acessar $_SESSION
require('conexao.php'); // Conexão com o banco de dados

// Verifica se o campo 'nome' foi enviado via POST
if (isset($_POST['nome'])) {
    // Pega os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $descarte = 0; // Valor padrão para descarte, conforme a lógica original

    // Prepara a instrução SQL para inserir a nova vaca
    $stmt = $banco->prepare("INSERT INTO vacas (nome, descarte) VALUES (:nome, :descarte)");
    // Vincula os valores aos parâmetros da instrução preparada
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':descarte', $descarte, PDO::PARAM_INT);

    // Tenta executar a instrução preparada
    if ($stmt->execute()) {
        // Se a inserção foi bem-sucedida, define a mensagem de sucesso na sessão
        $_SESSION['mensagem'] = "Animal adicionado com Sucesso";
        session_write_close(); // IMPORTANTE: Força o salvamento dos dados da sessão
        // Redireciona para a página de cadastro com a mensagem de sucesso
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
        exit; // Termina a execução do script
    } else {
        // Se a inserção falhou, define a mensagem de erro na sessão
        $_SESSION['mensagem'] = "Erro ao adicionar animal";
        session_write_close(); // IMPORTANTE: Força o salvamento dos dados da sessão em caso de erro também
        // Redireciona para a página de cadastro com a mensagem de erro
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
        exit; // Termina a execução do script
    }
}
?>