<?php
session_start();
include __DIR__ . "/criar_alerta.php";
require_once __DIR__ . "/conexao.php"; // Este arquivo define $banco

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    // 1. Definição do diretório de upload
    $uploadDir = __DIR__ . '../../uploads/';

    // 2. Criação do diretório de upload se não existir
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true)) {
            criar_alerta("Erro interno do servidor: Não foi possível criar o diretório de uploads. Verifique as permissões da pasta pai.", "erro");
            header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
            exit();
        }
    }

    // 3. Obtenção das informações do arquivo enviado (DEFINIÇÃO AGORA DENTRO DO IF)
    $fileName = basename($_FILES['arquivo']['name']);
    $fileTmpName = $_FILES['arquivo']['tmp_name'];
    $fileSize = $_FILES['arquivo']['size'];
    $fileError = $_FILES['arquivo']['error'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // 4. Validações de Segurança e Tipo/Tamanho de Arquivo
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    $maxFileSize = 10 * 1024 * 1024; // 10 MB

    if ($fileError !== UPLOAD_ERR_OK) {
        $errorMessage = "Erro no upload do arquivo. ";
        switch ($fileError) {
            case UPLOAD_ERR_INI_SIZE: $errorMessage .= "O arquivo excede o tamanho máximo permitido no php.ini."; break;
            case UPLOAD_ERR_FORM_SIZE: $errorMessage .= "O arquivo excede o tamanho máximo especificado no formulário."; break;
            case UPLOAD_ERR_PARTIAL: $errorMessage .= "O upload foi feito apenas parcialmente."; break;
            case UPLOAD_ERR_NO_FILE: $errorMessage .= "Nenhum arquivo foi enviado."; break;
            case UPLOAD_ERR_NO_TMP_DIR: $errorMessage .= "Faltando uma pasta temporária no servidor."; break;
            case UPLOAD_ERR_CANT_WRITE: $errorMessage .= "Falha ao gravar o arquivo em disco."; break;
            case UPLOAD_ERR_EXTENSION: $errorMessage .= "Uma extensão do PHP impediu o upload."; break;
            default: $errorMessage .= "Código de erro desconhecido: " . $fileError; break;
        }
        criar_alerta($errorMessage, "erro");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
        exit();
    }

    if (!in_array($fileType, $allowedTypes)) {
        criar_alerta("Erro: Somente arquivos PDF, DOC, DOCX, TXT, JPG, JPEG, PNG são permitidos.", "erro");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
        exit();
    }

    if ($fileSize > $maxFileSize) {
        criar_alerta("Erro: O arquivo é muito grande. Tamanho máximo permitido é " . ($maxFileSize / (1024 * 1024)) . "MB.", "erro");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
        exit();
    }

    // 5. Gerar um nome de arquivo único e definir o caminho completo para salvar
    $originalFileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
    $uniqueFileName = uniqid($originalFileNameWithoutExt . '_') . '.' . $fileType;
    $uploadFilePath = $uploadDir . $uniqueFileName;

    // 6. Tentar mover o arquivo enviado para o destino final
    if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
        criar_alerta("Relatório '{$fileName}' enviado com sucesso!", "sucesso");

        // 7. Lógica para SALVAR INFORMAÇÕES no Banco de Dados
        if (isset($banco) && isset($_SESSION['id_usuario'])) {
            try {
                $stmt = $banco->prepare("INSERT INTO relatorios (id_aluno, nome_arquivo, caminho_arquivo, data_upload) VALUES (:id_aluno, :nome_original, :nome_salvo, NOW())");
                $stmt->bindValue(':id_aluno', $_SESSION['id_usuario'], PDO::PARAM_INT);
                $stmt->bindValue(':nome_original', $fileName, PDO::PARAM_STR);
                $stmt->bindValue(':nome_salvo', $uniqueFileName, PDO::PARAM_STR);
                $stmt->execute();

                criar_alerta("Relatório '{$fileName}' enviado e registrado no banco de dados!", "sucesso");

            } catch (PDOException $e) {
                error_log("Erro ao salvar relatório no DB: " . $e->getMessage());
                criar_alerta("Relatório enviado, mas houve um erro ao registrar no sistema. Contate o suporte.", "atencao");
            }
        } else {
            $debug_msg = "";
            if (!isset($banco)) {
                $debug_msg .= "Conexão DB ausente. ";
            }
            if (!isset($_SESSION['id_usuario'])) {
                $debug_msg .= "Usuário não logado (id_usuario na sessão ausente). ";
            }
            criar_alerta("Relatório enviado, mas o registro no banco de dados não pôde ser feito. " . $debug_msg, "atencao");
        }

        // 8. Redirecionamento após sucesso
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
        exit();

    } else {
        // 9. Lógica para erro ao mover o arquivo
        criar_alerta("Erro ao salvar o arquivo no servidor. Permissões ou caminho inválido.", "erro");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
        exit();
    }

} else {
    // Este bloco é executado se a requisição NÃO for POST ou se $_FILES['arquivo'] não estiver setado.
    // Isso acontece quando alguém tenta acessar upload_relatorio.php diretamente sem um formulário.
    criar_alerta("Nenhum arquivo de relatório foi selecionado para envio ou o formulário não foi enviado via POST.", "erro");
    header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=relatorios");
    exit();
}
?>