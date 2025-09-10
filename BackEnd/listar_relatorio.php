<?php
require_once __DIR__ . "/conexao.php"; // Inclui o arquivo que define a conexão $banco

$uploadBaseUrl = '../../uploads/'; // Caminho base para arquivos enviados

try {
    // Prepara consulta para buscar relatórios e nome do aluno que enviou, ordenados por data de upload decrescente
    $stmt = $banco->prepare("
        SELECT
            r.id_relatorio,
            r.nome_arquivo,
            r.caminho_arquivo,
            r.data_upload,
            u.nome AS nome_aluno
        FROM
            relatorios r
        JOIN
            usuarios u ON r.id_aluno = u.id_usuario
        ORDER BY
            r.data_upload DESC
    ");
    $stmt->execute();
    // Obtém todos os resultados como array associativo
    $relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se há relatórios
    if (empty($relatorios)) {
        echo "<tr><td colspan='4'>Nenhum relatório enviado ainda.</td></tr>";
    } else {
        // Para cada relatório, exibe uma linha da tabela com os dados formatados
        foreach ($relatorios as $relatorio) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($relatorio['nome_aluno']) . "</td>"; // Nome do aluno
            echo "<td>" . htmlspecialchars($relatorio['nome_arquivo']) . "</td>"; // Nome do arquivo
            echo "<td>" . date("d/m/Y H:i", strtotime($relatorio['data_upload'])) . "</td>"; // Data formatada
            // Link para baixar o arquivo
            echo "<td>";
            echo "<a href='" . htmlspecialchars($uploadBaseUrl . $relatorio['caminho_arquivo']) . "' download class='btn-baixar'>Baixar</a>";
            echo "</td>";
            echo "</tr>";
        }
    }

} catch (PDOException $e) {
    // Loga o erro no servidor e mostra mensagem genérica para o usuário
    error_log("Erro ao listar relatórios no DB: " . $e->getMessage());
    echo "<tr><td colspan='4'>Erro ao carregar relatórios. Tente novamente mais tarde.</td></tr>";
}
?>
