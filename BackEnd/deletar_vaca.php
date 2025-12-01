<?php
// Arquivo: BackEnd/deletar_vaca.php - Invoker

require("conexao.php"); // Fornece $banco globalmente
require_once __DIR__ . '/commands/DeletarVacaCommand.php';
require_once __DIR__ . '/utils/CommandHistoryManager.php'; // NOVO

header('Content-Type: application/json');

$id_vaca = $_POST['id_vaca'] ?? null; 

if (!$id_vaca) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

try {
    // 1. Cria o Comando, SEM precisar de um Repository ou PDO no construtor.
    $command = new DeletarVacaCommand(
        (int)$id_vaca 
    );

    // 2. Executa o Comando (O comando gerencia o Memento, a exclusão e a Auditoria)
    if ($command->execute()) {
        
        // 3. Se SUCESSO: Armazena o objeto de comando completo na pilha
        CommandHistoryManager::push($command);
        
        echo json_encode(['success' => true, 'message' => 'Vaca excluída e auditoria registrada.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum registro encontrado com este ID ou falha ao executar.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}