<?php
require_once __DIR__ . '/../repository/VacaRepository.php';
require_once __DIR__ . '/../dto/VacaDTO.php'; 

// **IMPORTANTE**: Certifique-se de que estes arquivos existam na pasta 'strategy'
// Conforme a implementação do Padrão Strategy.
require_once __DIR__ . '/../strategy/ScoringStrategy.php';
require_once __DIR__ . '/../strategy/MastitePenalizationStrategy.php';
require_once __DIR__ . '/../strategy/InactivityPenalizationStrategy.php';

/**
 * Camada de Serviço para a entidade Vaca.
 */
class VacaService {
    private $repository;

    public function __construct(VacaRepository $repository) {
        $this->repository = $repository;
    }

    public function cadastrarVaca(VacaDTO $vacaDto): bool {
        // Regra de Negócio: Nome não pode ser muito curto
        if (strlen(trim($vacaDto->nome)) < 3) {
            throw new Exception("O nome da vaca deve ter pelo menos 3 caracteres.");
        }
        
        // Regra de Negócio: Lote de Manejo deve ser selecionado.
        if ($vacaDto->idLoteManejo <= 0) {
            throw new Exception("É obrigatório selecionar um Lote de Manejo.");
        }

        return $this->repository->insert($vacaDto);
    }
    
    public function listarVacas(int $limit, int $offset): array {
        return $this->repository->findAllPaginated($limit, $offset);
    }

    public function calcularEAtualizarScore(int $id_vaca): bool {
        // 1. Inicialização
        $score_inicial = 100.00;
        $score_final = $score_inicial;
        
        // 2. Busca dados (usando o método existente no Repository)
        $historico = $this->repository->findMastiteHistory($id_vaca);

        // 3. Define a lista de Estratégias (O Contexto do Padrão Strategy)
        // O Contexto agora apenas gerencia e executa as regras.
        $strategies = [
            new MastitePenalizationStrategy(),
            new InactivityPenalizationStrategy(),
        ];
        
        // 4. Executa a lista de Estratégias
        foreach ($strategies as $strategy) {
            // Delega o cálculo para a Estratégia Concreta
            $score_final = $strategy->calculateAdjustment($score_final, $historico);
        }
        
        // 5. Limita e Arredonda o score final (Lógica comum final)
        $score_final = max(0.00, $score_final);
        $score_final = round($score_final, 2);

        // 6. Persiste o Score Atualizado
        return $this->repository->updateHealthScore($id_vaca, $score_final);
    }

    /**
     * Método restaurado para ser usado pelo listar_scores_saude.php.
     * Deve estar DENTRO da classe VacaService.
     */
    public function listarScores(int $limit, int $offset): array {
        return $this->repository->findAllScoresPaginated($limit, $offset);
    }
    
    // Método de contagem para uso na paginação (restaurado)
    public function contarTodasVacas(): int {
        return $this->repository->countAll();
    }
}
// O erro foi corrigido movendo a chave de fechamento da classe para o final.
?>