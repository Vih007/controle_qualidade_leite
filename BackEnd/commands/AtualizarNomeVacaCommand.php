<?php
// Arquivo: BackEnd/commands/AtualizarNomeVacaCommand.php

// Inclui os arquivos necessários
require_once __DIR__ . '/../patterns/Command.php';
require_once __DIR__ . '/../repository/VacaRepository.php';
require_once __DIR__ . '/../registrar_auditoria.php';

// ATENÇÃO: conexao.php é incluído no getPdo()
class AtualizarNomeVacaCommand implements Command, Serializable {
    private $idVaca;
    private $novoNome;
    private $nomeAntigo; // Memento: Armazena o estado anterior para o Undo
    private $repository;
    // O PDO foi removido para que o objeto seja serializável!
    
    // O construtor agora só recebe objetos serializáveis e os dados
    public function __construct(int $idVaca, string $novoNome, VacaRepository $repository) {
        $this->idVaca = $idVaca;
        $this->novoNome = $novoNome;
        $this->repository = $repository;
    }

    /**
     * Obtém a conexão PDO a partir do escopo global.
     */
    private function getPdo(): PDO {
        global $banco; // Tenta acessar a conexão global $banco
        if (is_null($banco)) {
            // Se a conexão não existir (porque foi desserializada), a criamos
            require_once __DIR__ . '/../conexao.php';
        }
        return $banco;
    }

    /**
     * Busca o nome antigo para armazenar no memento.
     */
    private function fetchOldState(): bool {
        $pdo = $this->getPdo(); 
        
        $stmt = $pdo->prepare("SELECT nome FROM vacas WHERE id_vaca = :id");
        $stmt->bindParam(':id', $this->idVaca);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return false;
        }
        $this->nomeAntigo = $dados['nome'];
        return true;
    }

    public function execute(): bool {
        // 1. Captura o estado antes da alteração (Crucial para Undo e Log)
        if (!$this->fetchOldState()) {
            return false; 
        }
        
        // 2. Executa a Ação principal (chama o Receiver)
        $success = $this->repository->updateNome($this->idVaca, $this->novoNome); 

        if ($success) {
            // 3. Loga a ação (Chama a Auditoria)
            $this->log();
        }
        return $success;
    }

    public function undo(): bool {
        // 1. Reverte a ação usando o estado antigo
        if (empty($this->nomeAntigo)) {
            return false;
        }
        
        // Reverte a ação (chama o Receiver)
        $success = $this->repository->updateNome($this->idVaca, $this->nomeAntigo);

        if ($success) {
            // 2. Loga a ação de UNDO como um evento de auditoria separado
            $this->logUndo(); 
        }
        return $success;
    }
    
    public function log(): bool {
        $pdo = $this->getPdo(); // Obtém a conexão PDO
        
        // Prepara os detalhes de auditoria para a ação de EXECUTE
        $detalhesLog = [
            'id_registro_afetado' => $this->idVaca,
            'campo_afetado' => 'nome',
            'valor_antigo' => $this->nomeAntigo,
            'valor_novo' => $this->novoNome
        ];
        
        // Chama a função de auditoria global existente no seu projeto
        return registrar_auditoria(
            $pdo, 
            'vacas', 
            'UPDATE_NOME', 
            $detalhesLog
        );
    }
    
    private function logUndo(): bool {
        $pdo = $this->getPdo(); // Obtém a conexão PDO
        
        // Prepara os detalhes de auditoria para a ação de UNDO
        $detalhesUndo = [
            'id_registro_afetado' => $this->idVaca,
            'campo_afetado' => 'nome',
            'valor_antigo' => $this->novoNome, // O valor que estava antes do undo
            'valor_novo' => $this->nomeAntigo // O valor para o qual retornou
        ];
        
        // Chama a função de auditoria global, com um tipo de ação diferente
        return registrar_auditoria(
            $pdo, 
            'vacas', 
            'UNDO_UPDATE_NOME', 
            $detalhesUndo
        );
    }
    
    // Implementação da interface Serializable para serialização (necessária para a sessão)
    public function serialize() {
        // Apenas armazena dados simples. O Repository e o PDO serão recriados/obtidos.
        return serialize([
            $this->idVaca,
            $this->novoNome,
            $this->nomeAntigo
        ]);
    }

    public function unserialize($data) {
        // A ordem de unserialize deve ser a mesma do serialize
        list(
            $this->idVaca,
            $this->novoNome,
            $this->nomeAntigo
        ) = unserialize($data);
        
        // O Repository deve ser reinicializado/reconstruído se for usado o método updateNome
        require_once __DIR__ . '/../repository/VacaRepository.php';
        $this->repository = new VacaRepository($this->getPdo());
    }
}