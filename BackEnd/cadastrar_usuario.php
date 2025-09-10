<?php
session_start(); // Inicia a sessão
require('conexao.php'); // Inclui a conexão com o banco de dados

// Se o formulário foi enviado com todos os campos necessários
if (isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['tipo_usuario'])) {
    $nome = $_POST['nome']; // Nome do usuário
    $email = $_POST['email']; // Email do usuário
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Hash da senha para segurança
    $tipo_usuario = $_POST['tipo_usuario']; // Tipo do usuário (ex: aluno, professor)

    // Prepara a query para inserir o usuário
    $stmt = $banco->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, :tipo_usuario)");
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', $senha);
    $stmt->bindValue(':tipo_usuario', $tipo_usuario);

    // Executa a query e redireciona com mensagem de sucesso ou erro
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Aluno adicionado com Sucesso";
        session_write_close(); // Fecha a sessão para garantir salvamento
        header("Location:../FrontEnd/professor/area_professor.php?secao=alunos");
        exit;
    } else {
        $_SESSION['mensagem'] = "Erro ao adicionar aluno";
        session_write_close();
        header("Location:../FrontEnd/professor/area_professor.php?secao=alunos");
        exit; 
    }
}
?>