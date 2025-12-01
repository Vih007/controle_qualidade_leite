<?php
// Arquivo: BackEnd/atualizar_vaca.php - Invoker

require("conexao.php"); // Fornece $banco globalmente
require_once __DIR__ . '/repository/VacaRepository.php';
require_once __DIR__ . '/commands/AtualizarNomeVacaCommand.php';
require_once __DIR__ . '/utils/CommandHistoryManager.php'; // NOVO

header('Content-Type: application/json');

$id_vaca = $_POST['id_vaca'] ?? null;
$novo_nome = $_POST['novo_nome'] ?? null;

if (!$id_vaca || !$novo_nome) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    // 1. Inicializa o Receiver (VacaRepository)
    $repository = new VacaRepository($banco);

    // 2. Cria o Comando, SEM passar o objeto $pdo diretamente, pois ele não é serializável.
    $command = new AtualizarNomeVacaCommand(
        (int)$id_vaca, 
        $novo_nome, 
        $repository
    );

    // 3. Executa o Comando (O Comando gerencia o Memento e a Auditoria)
    if ($command->execute()) {
        
        // 4. Se SUCESSO: Armazena o objeto de comando completo na pilha
        CommandHistoryManager::push($command);
        
        echo json_encode(['success' => true, 'message' => 'Vaca atualizada e auditoria registrada.']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao executar a atualização, a vaca pode não existir.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro de sistema: ' . $e->getMessage()]);
}