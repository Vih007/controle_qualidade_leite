<?php
require_once __DIR__ . '/../repository/ProducaoLeiteRepository.php';
require_once __DIR__ . '/../dto/ProducaoLeiteDTO.php';

/**
 * Camada de Serviço para a entidade ProducaoLeite.
 */
class ProducaoLeiteService {
    private $repository;

    public function __construct(ProducaoLeiteRepository $repository) {
        $this->repository = $repository;
    }

    public function cadastrarProducao(ProducaoLeiteDTO $dto): bool {
        // Regra de Negócio: Quantidade deve ser positiva.
        if ($dto->quantidade <= 0) {
            throw new Exception("A quantidade de leite deve ser um valor positivo.");
        }
        
        // Regra de Negócio: Tanque deve ser válido.
        if ($dto->idTanque <= 0) {
            throw new Exception("É obrigatório selecionar o Tanque de Destino.");
        }

        return $this->repository->insert($dto);
    }
    
    public function listarProducoes(int $limit, int $offset): array {
        return $this->repository->findAllPaginated($limit, $offset);
    }
    
    public function contarTodasProducoes(): int {
        return $this->repository->countAll();
    }
}
?>