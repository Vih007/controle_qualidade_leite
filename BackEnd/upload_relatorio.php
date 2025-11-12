<?php
require_once __DIR__ . "/conexao.php";
session_start();

// Verifica se o aluno está logado
if (!isset($_SESSION['id_usuario'])) {
    die("Acesso negado. Faça login para enviar relatórios.");
}

$idAluno = $_SESSION['id_usuario'];

// Caminho físico para salvar os arquivos (dentro de /BackEnd/uploads/)
$uploadDir = realpath(__DIR__ . '/../uploads') . DIRECTORY_SEPARATOR;

// Cria o diretório caso não exista
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Verifica se algum arquivo foi enviado
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    die("Erro no envio do arquivo.");
}

// Garante um nome único para evitar sobrescrita
$nomeOriginal = basename($_FILES['arquivo']['name']);
$extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
$nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
$nomeUnico = $nomeBase . "_" . uniqid() . "." . $extensao;

// Caminho completo do arquivo (físico)
$caminhoFinal = $uploadDir . $nomeUnico;

// Move o arquivo para a pasta uploads
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoFinal)) {
    try {
        // Salva no banco de dados: nome original, nome salvo e data
        $stmt = $banco->prepare("
            INSERT INTO relatorios (nome_arquivo, caminho_arquivo, data_upload, id_aluno)
            VALUES (?, ?, NOW(), ?)
        ");
        $stmt->execute([$nomeOriginal, $nomeUnico, $idAluno]);

        echo "Upload realizado com sucesso!";
    } catch (PDOException $e) {
        error_log("Erro ao salvar no banco: " . $e->getMessage());
        echo "Erro ao registrar o relatório no banco.";
    }
} else {
    echo "Erro ao mover o arquivo para a pasta de uploads.";
}
?>
