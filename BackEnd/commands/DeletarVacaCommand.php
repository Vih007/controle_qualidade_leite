<?php
// Arquivo: BackEnd/commands/DeletarVacaCommand.php

require_once __DIR__ . '/../patterns/Command.php';
require_once __DIR__ . '/../registrar_auditoria.php';

class DeletarVacaCommand implements Command, Serializable {
    private $idVaca;
    private $dadosVacaSalvos; // Memento: Vai guardar TUDO para o undo

    // O construtor remove o objeto PDO
    public function __construct(int $idVaca) {
        $this->idVaca = $idVaca;
    }

    /**
     * Obtém a conexão PDO a partir do escopo global.
     */
    private function getPdo(): PDO {
        global $banco;
        if (is_null($banco)) {
            require_once __DIR__ . '/../conexao.php';
        }
        return $banco;
    }
    
    /**
     * Busca os dados antigos para armazenar no memento antes de deletar.
     */
    private function fetchOldState(): bool {
        $pdo = $this->getPdo();
        
        // Busca todos os dados da vaca (incluindo descarte e lote manejo)
        $stmt = $pdo->prepare("SELECT id_vaca, nome, descarte, id_lote_manejo FROM vacas WHERE id_vaca = :id");
        $stmt->bindParam(':id', $this->idVaca);
        $stmt->execute();
        $this->dadosVacaSalvos = $stmt->fetch(PDO::FETCH_ASSOC);

        return (bool)$this->dadosVacaSalvos;
    }

    public function execute(): bool {
        if (!$this->fetchOldState()) {
            return false; // Vaca não encontrada para deletar
        }
        
        $pdo = $this->getPdo();
        
        // Exclui a vaca (Ação principal)
        $stmt = $pdo->prepare("DELETE FROM vacas WHERE id_vaca = :id");
        $stmt->bindParam(':id', $this->idVaca);
        $success = $stmt->execute();

        if ($success) {
            $this->log();
        }
        return $success;
    }

    public function undo(): bool {
        if (empty($this->dadosVacaSalvos)) {
            return false;
        }
        
        $pdo = $this->getPdo();

        // Reverte a exclusão (UNDO: Re-insere os dados salvos)
        $sql = "INSERT INTO vacas (id_vaca, nome, descarte, id_lote_manejo) 
                VALUES (:id_vaca, :nome, :descarte, :id_lote_manejo)";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':id_vaca' => $this->dadosVacaSalvos['id_vaca'],
            ':nome' => $this->dadosVacaSalvos['nome'],
            ':descarte' => $this->dadosVacaSalvos['descarte'],
            // Se id_lote_manejo for NULL, PDO cuida disso
            ':id_lote_manejo' => $this->dadosVacaSalvos['id_lote_manejo']
        ]);

        if ($success) {
            $this->logUndo();
            return true;
        }
        return false;
    }
    
    public function log(): bool {
        $pdo = $this->getPdo();
        
        // Loga a exclusão da vaca
        return registrar_auditoria(
            $pdo, 
            'vacas', 
            'DELETE', 
            $this->dadosVacaSalvos
        );
    }
    
    private function logUndo(): bool {
        $pdo = $this->getPdo();
        
        // Loga a reversão da exclusão
        return registrar_auditoria(
            $pdo, 
            'vacas', 
            'UNDO_DELETE', 
            $this->dadosVacaSalvos
        );
    }

    // Implementação da interface Serializable para serialização
    public function serialize() {
        return serialize([
            $this->idVaca,
            $this->dadosVacaSalvos,
        ]);
    }

    public function unserialize($data) {
        list(
            $this->idVaca,
            $this->dadosVacaSalvos
        ) = unserialize($data);
        
        // Não precisamos do Repository aqui, pois o comando usa PDO diretamente.
    }
}