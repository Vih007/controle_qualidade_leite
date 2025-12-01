<?php
require_once __DIR__ . '/../dto/VacaDTO.php';

/**
 * Repository (DAO) para a entidade Vaca.
 * Responsável pela persistência e busca de dados (CRUD).
 */
class VacaRepository {
    private $banco;

    public function __construct($pdoConnection) {
        $this->banco = $pdoConnection;
    }

    public function insert(VacaDTO $vacaDto): bool {
        $stmt = $this->banco->prepare("
            INSERT INTO vacas (nome, descarte, id_lote_manejo) 
            VALUES (:nome, :descarte, :id_lote_manejo)
        ");
        
        $stmt->bindValue(':nome', $vacaDto->nome);
        $stmt->bindValue(':descarte', $vacaDto->descarte, PDO::PARAM_INT);
        $stmt->bindValue(':id_lote_manejo', $vacaDto->idLoteManejo, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    public function findAllPaginated(int $limit, int $offset): array {
        // Score removido. Agora, este método só busca dados coesos para a lista básica.
        $sql = "SELECT v.id_vaca, v.nome, v.descarte, v.id_lote_manejo, l.nome_lote 
                FROM vacas v
                LEFT JOIN lote_manejo l ON v.id_lote_manejo = l.id_lote_manejo
                LIMIT :limite OFFSET :offset";
                
        $stmt = $this->banco->prepare($sql);
        $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(): int {
        return (int) $this->banco->query("SELECT COUNT(*) FROM vacas")->fetchColumn();
    }

    public function findAllScoresPaginated(int $limit, int $offset): array {
        $sql = "SELECT v.id_vaca, v.nome, v.health_score, l.nome_lote 
                FROM vacas v
                LEFT JOIN lote_manejo l ON v.id_lote_manejo = l.id_lote_manejo
                LIMIT :limite OFFSET :offset";
        
        $stmt = $this->banco->prepare($sql);
        $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        // NOVO MÉTODO 1: Atualiza APENAS o score, demonstrando encapsulamento do UPDATE
    public function updateHealthScore(int $id_vaca, float $score): bool {
        $stmt = $this->banco->prepare("UPDATE vacas SET health_score = :score WHERE id_vaca = :id_vaca");
        $stmt->bindValue(':score', $score);
        $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // NOVO MÉTODO 2: Busca dados de múltiplas tabelas para o cálculo complexo
    public function findMastiteHistory(int $id_vaca): array {
        // Busca o total de testes positivos (tabela teste_mastite)
        $stmt = $this->banco->prepare("SELECT COUNT(*) AS total_positivos FROM teste_mastite WHERE id_vaca = :id_vaca AND resultado = 'positivo'");
        $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->execute();
        $data['mastite'] = $stmt->fetchColumn();

        // Busca a data da última produção (tabela producao_leite)
        $stmt = $this->banco->prepare("SELECT MAX(data) AS ultima_producao FROM producao_leite WHERE id_vaca = :id_vaca");
        $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->execute();
        $data['ultima_producao'] = $stmt->fetchColumn();
        
        return $data;
    }

    // NOVO MÉTODO REQUERIDO PELO COMMAND
    public function updateNome(int $id_vaca, string $nome): bool {
        $stmt = $this->banco->prepare("UPDATE vacas SET nome = :nome WHERE id_vaca = :id_vaca");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>