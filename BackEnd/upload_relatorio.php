<?php
session_start();
include __DIR__ . "/criar_alerta.php";
require_once __DIR__ . "/conexao.php";

header('Content-Type: application/json');

// 1. Verifique a requisição para ver se ela é um bloco de upload
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file_chunk']) || !isset($_POST['file_id'])) {
    // A requisição inicial do JavaScript precisa ser via POST e ter os dados de chunk.
    criar_alerta("Requisição inválida.", "erro");
    echo json_encode(['success' => false, 'message' => 'Requisição inválida. Nenhum bloco de arquivo recebido.']);
    exit();
}

// Defina os diretórios de upload e temporário
$uploadDir = __DIR__ . '/../../uploads/';
$tempDir = $uploadDir . 'temp/';

// Crie os diretórios se não existirem
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0775, true);
}

// Recebe os dados do JavaScript
$fileId = $_POST['file_id'];
$fileName = $_POST['file_name'];
$chunkIndex = (int)$_POST['chunk_index'];
$totalChunks = (int)$_POST['total_chunks'];

$fileTmpPath = $_FILES['file_chunk']['tmp_name'];
$tempFilePath = $tempDir . $fileId;

// 3. Salve o bloco: anexe o bloco ao arquivo temporário
$currentFile = fopen($tempFilePath, 'a');
$chunk = file_get_contents($fileTmpPath);
fwrite($currentFile, $chunk);
fclose($currentFile);

// 4. Verifique se é o último bloco
if ($chunkIndex == $totalChunks - 1) {
    // 5. O arquivo está completo, processe-o
    
    // Validações de tipo de arquivo
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($fileType, $allowedTypes)) {
        unlink($tempFilePath); // Deleta o arquivo temporário
        // Cria o alerta para ser exibido após o recarregamento
        criar_alerta("Erro: Somente arquivos PDF, DOC, DOCX, TXT, JPG, JPEG, PNG são permitidos.", "erro");
        echo json_encode(['success' => false, 'message' => 'Erro: tipo de arquivo não permitido.']);
        exit();
    }

    // Move o arquivo completo para o diretório final
    $finalFileName = uniqid($fileName . '_') . '.' . $fileType;
    $finalFilePath = $uploadDir . $finalFileName;
    rename($tempFilePath, $finalFilePath);
    
    // 6. Lógica para SALVAR INFORMAÇÕES no Banco de Dados
    if (isset($banco) && isset($_SESSION['id_usuario'])) {
        try {
            $stmt = $banco->prepare("INSERT INTO relatorios (id_aluno, nome_arquivo, caminho_arquivo, data_upload) VALUES (:id_aluno, :nome_original, :nome_salvo, NOW())");
            $stmt->bindValue(':id_aluno', $_SESSION['id_usuario'], PDO::PARAM_INT);
            $stmt->bindValue(':nome_original', $fileName, PDO::PARAM_STR);
            $stmt->bindValue(':nome_salvo', $finalFileName, PDO::PARAM_STR);
            $stmt->execute();
            
            // Define o alerta de sucesso na sessão para ser exibido após o recarregamento
            criar_alerta("Relatório '{$fileName}' enviado com sucesso!", "sucesso");
            
            // Retorne sucesso ao JavaScript para que ele recarregue a página
            echo json_encode(['success' => true]);
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao salvar relatório no DB: " . $e->getMessage());
            unlink($finalFilePath); // Deleta o arquivo final em caso de erro no DB
            criar_alerta("Relatório enviado, mas houve um erro ao registrar no sistema. Contate o suporte.", "atencao");
            echo json_encode(['success' => false, 'message' => 'Erro ao registrar no banco de dados.']);
            exit();
        }
    } else {
        unlink($finalFilePath);
        criar_alerta("Relatório enviado, mas o registro no banco de dados não pôde ser feito. Usuário não logado ou erro de conexão.", "atencao");
        echo json_encode(['success' => false, 'message' => 'Usuário não logado ou erro de conexão.']);
        exit();
    }
} else {
    // Se não é o último bloco, retorne sucesso para o JavaScript continuar
    echo json_encode(['success' => true]);
}
?>