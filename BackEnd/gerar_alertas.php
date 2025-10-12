<?php
require('conexao.php');
require_once __DIR__ . '/repository/VacaRepository.php';
require_once __DIR__ . '/service/VacaService.php';

// Tática de Detecção de Falhas: Verifica se a conexão com o banco é válida antes de executar queries.
if (!is_null($banco)) {

    // INÍCIO DO NOVO BLOCO: CALCULAR E ATUALIZAR SCORES
    $vacaRepo = new VacaRepository($banco);
    $vacaService = new VacaService($vacaRepo);

    // Envolve a lógica principal em try-catch para capturar PDOExceptions
    try {
        // Busca o ID de todas as vacas para calcular o score
        $todas_vacas = $banco->query("SELECT id_vaca FROM vacas")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($todas_vacas as $id_vaca) {
            try {
                $vacaService->calcularEAtualizarScore((int)$id_vaca);
            } catch (Exception $e) {
                // Logar erro, mas não interromper o script
                error_log("Erro ao calcular score para Vaca ID {$id_vaca}: " . $e->getMessage());
            }
        }

        // Função auxiliar para evitar alertas duplicados
        function alertaExiste($banco, $id_vaca, $mensagem) {
            $check = $banco->prepare("SELECT COUNT(*) FROM alertas WHERE id_vaca = :id_vaca AND mensagem = :mensagem");
            $check->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
            $check->bindValue(':mensagem', $mensagem, PDO::PARAM_STR);
            $check->execute();
            return $check->fetchColumn() > 0;
        }

        // 1. PRODUÇÃO DIARIA = 0 (somente a mais recente de cada vaca)
        $prod = $banco->query("
            SELECT pl.id_vaca, pl.quantidade 
            FROM producao_leite pl
            INNER JOIN (
                SELECT id_vaca, MAX(data) AS data_mais_recente
                FROM producao_leite
                GROUP BY id_vaca
            ) ult
            ON pl.id_vaca = ult.id_vaca AND pl.data = ult.data_mais_recente
            WHERE pl.quantidade = 0
        ");

        foreach ($prod as $row) {
            $id_vaca = $row['id_vaca'];
            $mensagem = "Alerta: Produção de leite semanal igual a 0.";
            if (!alertaExiste($banco, $id_vaca, $mensagem)) {
                $stmt = $banco->prepare("INSERT INTO alertas (id_vaca, mensagem) VALUES (:id_vaca, :mensagem)");
                $stmt->execute([
                    ':id_vaca' => $id_vaca,
                    ':mensagem' => $mensagem
                ]);
            }
        }

        // 2. MASTITE: teste positivo após um anterior negativo
        $positivos = $banco->query("SELECT id_vaca, data FROM teste_mastite WHERE resultado = 'positivo'");
        foreach ($positivos as $row) {
            $id_vaca = $row['id_vaca'];
            $data_positivo = $row['data'];

            $stmt = $banco->prepare("
                SELECT * FROM teste_mastite 
                WHERE id_vaca = :id_vaca 
                  AND data < :data 
                  AND resultado = 'negativo' 
                ORDER BY data DESC 
                LIMIT 1
            ");
            $stmt->execute([
                ':id_vaca' => $id_vaca,
                ':data' => $data_positivo
            ]);

            $anterior_negativo = $stmt->fetch();

            if ($anterior_negativo) {
                $mensagem = "Alerta: Teste de mastite POSITIVO após um NEGATIVO.";
                if (!alertaExiste($banco, $id_vaca, $mensagem)) {
                    $stmt = $banco->prepare("INSERT INTO alertas (id_vaca, mensagem) VALUES (:id_vaca, :mensagem)");
                    $stmt->execute([
                        ':id_vaca' => $id_vaca,
                        ':mensagem' => $mensagem
                    ]);
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Falha ao tentar gerar alertas (DB offline?): " . $e->getMessage());
    }

} 
