<?php
require_once __DIR__ . '/../repository/VacaRepository.php';
require_once __DIR__ . '/../dto/VacaDTO.php'; 

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
        
        // 2. Busca dados de múltiplas fontes (via Repository)
        $historico = $this->repository->findMastiteHistory($id_vaca);
        
        // 3. Aplica Regras de Negócio (Lógica Complexa)
        
        // REGRA A: Penalidade por Teste de Mastite Positivo
        $positivos = $historico['mastite'] ?? 0;
        $penalidade_mastite = $positivos * 5.00; // -5.00 pontos por teste positivo
        $score_final -= $penalidade_mastite;

        // REGRA B: Penalidade por Inatividade de Produção
        $ultima_producao = $historico['ultima_producao'];
        if ($ultima_producao) {
            $data_hoje = new DateTime();
            $data_ultima = new DateTime($ultima_producao);
            $dias_inativos = $data_hoje->diff($data_ultima)->days;
            
            // Penaliza por inatividade acima de 30 dias (pode indicar animal seco ou doente)
            if ($dias_inativos > 30) {
                $penalidade_inativade = ($dias_inativos - 30) * 0.5; // -0.5 ponto por dia após 30 dias
                $score_final -= $penalidade_inativade;
            }
        }
        
        // 4. Limita e Arredonda o score final
        $score_final = max(0.00, $score_final);
        $score_final = round($score_final, 2);

        // 5. Persiste o Score Atualizado (Delega ao Repositório)
        return $this->repository->updateHealthScore($id_vaca, $score_final);
    }

    public function listarScores(int $limit, int $offset): array {
        return $this->repository->findAllScoresPaginated($limit, $offset);
    }
}
?>