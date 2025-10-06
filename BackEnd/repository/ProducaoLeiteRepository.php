<?php
require_once __DIR__ . '/../dto/ProducaoLeiteDTO.php';

/**
 * Repository (DAO) para a entidade ProducaoLeite.
 */
class ProducaoLeiteRepository {
    private $banco;

    public function __construct($pdoConnection) {
        $this->banco = $pdoConnection;
    }

    public function insert(ProducaoLeiteDTO $dto): bool {
        $stmt = $this->banco->prepare("
            INSERT INTO producao_leite (id_vaca, quantidade, data, id_tanque) 
            VALUES (:id_vaca, :quantidade, :data, :id_tanque)
        ");
        
        $stmt->bindValue(':id_vaca', $dto->idVaca, PDO::PARAM_INT);
        $stmt->bindValue(':quantidade', $dto->quantidade);
        $stmt->bindValue(':data', $dto->data);
        $stmt->bindValue(':id_tanque', $dto->idTanque, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findAllPaginated(int $limit, int $offset): array {
        $sql = "SELECT pl.id_producao, pl.id_vaca, v.nome AS nome_vaca, 
                       pl.quantidade, pl.data, t.localizacao AS nome_tanque
                FROM producao_leite pl
                JOIN vacas v ON pl.id_vaca = v.id_vaca
                LEFT JOIN tanque t ON pl.id_tanque = t.id_tanque
                LIMIT :limite OFFSET :offset";
        
        $stmt = $this->banco->prepare($sql);
        $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countAll(): int {
        return (int) $this->banco->query("SELECT COUNT(*) FROM producao_leite")->fetchColumn();
    }
}
?>