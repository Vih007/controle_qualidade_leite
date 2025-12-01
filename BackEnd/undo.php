<?php
session_start();

// Dependências
require __DIR__ . '/conexao.php'; // Carrega o $banco globalmente
require_once __DIR__ . '/utils/CommandHistoryManager.php';

// ATENÇÃO: As classes de comandos precisam ser incluídas para a desserialização funcionar
require_once __DIR__ . '/repository/VacaRepository.php'; 
require_once __DIR__ . '/commands/AtualizarNomeVacaCommand.php';
require_once __DIR__ . '/commands/DeletarVacaCommand.php'; 

header('Content-Type: application/json');

if (CommandHistoryManager::count() === 0) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma operação recente para desfazer.']);
    exit;
}

try {
    // 1. Pega o último comando da pilha (POP)
    $lastCommand = CommandHistoryManager::pop();

    if ($lastCommand === null) {
        throw new Exception("Falha ao recuperar o último comando do histórico.");
    }
    
    // 2. Chama o método undo() do objeto.
    if ($lastCommand->undo()) {
        // O método undo() já gerou a auditoria de UNDO
        echo json_encode([
            'success' => true, 
            'message' => 'Última operação desfeita com sucesso: ' . get_class($lastCommand),
        ]);
    } else {
        // Se o undo falhar, empurra o comando de volta para a pilha para não perdê-lo
        CommandHistoryManager::push($lastCommand); 
        echo json_encode(['success' => false, 'message' => 'Falha ao reverter a operação.']);
    }

} catch (Exception $e) {
    error_log("ERRO FATAL NO UNDO: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro de sistema ao desfazer: ' . $e->getMessage()]);
}
?>