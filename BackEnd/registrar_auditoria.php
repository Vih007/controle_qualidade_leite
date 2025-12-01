<?php
// Arquivo: ../BackEnd/registrar_auditoria.php

/**
 * Registra uma entrada de auditoria no log_auditoria.
 *
 * @param PDO $pdo Objeto de conexão com o banco de dados.
 * @param string $tabela_afetada Nome da tabela onde a acao ocorreu.
 * @param string $tipo_acao Tipo de acao (Ex: 'INSERT', 'UPDATE', 'DELETE').
 * @param array $detalhes Array associativo com os dados afetados (será JSON-encoded).
 * @return bool True se o log foi inserido com sucesso.
 */
function registrar_auditoria(PDO $pdo, string $tabela_afetada, string $tipo_acao, array $detalhes): bool {
    // 1. Obter o ID do Usuário Logado (Quem realizou a ação)
    // ATENÇÃO: Verifique se sua SESSION está inicializada!
    $id_usuario = $_SESSION['id_usuario'] ?? null; 
    
    // 2. Obter o IP de Origem
    $ip_origem = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // 3. Serializar os Detalhes
    $detalhes_json = json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    // 4. Inserção no Banco de Dados
    $sql = "
        INSERT INTO log_auditoria 
        (data_hora, id_usuario, ip_origem, tabela_afetada, tipo_acao, detalhes)
        VALUES (NOW(), :id_usuario, :ip_origem, :tabela, :acao, :detalhes_json)
    ";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':ip_origem', $ip_origem);
        $stmt->bindParam(':tabela', $tabela_afetada);
        $stmt->bindParam(':acao', $tipo_acao);
        $stmt->bindParam(':detalhes_json', $detalhes_json);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        // Em um ambiente de produção, logar o erro em um arquivo, e não exibir
        error_log("ERRO AO REGISTRAR AUDITORIA: " . $e->getMessage());
        return false;
    }
}