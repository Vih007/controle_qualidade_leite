<?php
// Arquivo: atualizar_vaca.php

// Inclui a conexão com o banco de dados ($banco é a variável PDO)
require("conexao.php");

// Inclui a função centralizada de auditoria
require("registrar_auditoria.php"); // Certifique-se de que este caminho está correto

header('Content-Type: application/json');

// Certifique-se de que a sessão está iniciada para capturar o ID do usuário
// session_start(); 

// Definindo variáveis e caso não as encontre define null por padrao
$id_vaca = $_POST['id_vaca'] ?? null;
$novo_nome = $_POST['novo_nome'] ?? null;

// --- 1. VERIFICAÇÃO DE DADOS ---
if (!$id_vaca || !$novo_nome) {
    // Falha rápida se os dados essenciais estiverem faltando
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    // --- 2. AUDITORIA: OBTEM DADOS ANTIGOS ANTES DA ATUALIZAÇÃO ---
    // Isso é crucial para a recuperação (Auditoria)
    $stmt_old = $banco->prepare("SELECT id_vaca, nome FROM vacas WHERE id_vaca = :id");
    $stmt_old->bindParam(':id', $id_vaca);
    $stmt_old->execute();
    $dados_antigos = $stmt_old->fetch(PDO::FETCH_ASSOC);

    // Verifica se a vaca existe
    if (!$dados_antigos) {
        echo json_encode(['success' => false, 'message' => 'Vaca não encontrada.']);
        exit;
    }

    $nome_antigo = $dados_antigos['nome'];

    // --- 3. EXECUÇÃO DO UPDATE ---
    $stmt = $banco->prepare("UPDATE vacas SET nome = :nome WHERE id_vaca = :id");
    $stmt->bindParam(':nome', $novo_nome);
    $stmt->bindParam(':id', $id_vaca);
    $stmt->execute();
    
    // Verifica se alguma linha foi realmente afetada
    if ($stmt->rowCount() > 0) {
        
        // --- 4. AUDITORIA: REGISTRO DA AÇÃO ---
        // Só registra se a operação foi bem-sucedida e houve alteração
        $detalhes_log = [
            'id_registro_afetado' => $id_vaca,
            'campo_afetado' => 'nome',
            'valor_antigo' => $nome_antigo,
            'valor_novo' => $novo_nome
        ];

        registrar_auditoria(
            $banco, 
            'vacas', 
            'UPDATE', 
            $detalhes_log
        );
        
        echo json_encode(['success' => true]);
        
    } else {
        // A query rodou, mas nenhum dado foi alterado (ex: nome antigo = nome novo)
        echo json_encode(['success' => true, 'message' => 'Nenhuma alteração detectada.']);
    }

} catch (PDOException $e) {
    // --- 5. TRATAMENTO DE ERRO ---
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}