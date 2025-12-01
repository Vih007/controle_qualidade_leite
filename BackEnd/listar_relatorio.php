<?php
require_once __DIR__ . "/conexao.php"; 

// Caminho físico e URL pública
$uploadDir = __DIR__ . '/../uploads/';
$uploadBaseUrl = '/projetoWeb/uploads/';

try {
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
    $relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($relatorios)) {
        echo "<tr><td colspan='4'>Nenhum relatório enviado ainda.</td></tr>";
    } else {
        foreach ($relatorios as $relatorio) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($relatorio['nome_aluno']) . "</td>";
            echo "<td>" . htmlspecialchars($relatorio['nome_arquivo']) . "</td>";
            echo "<td>" . date("d/m/Y H:i", strtotime($relatorio['data_upload'])) . "</td>";
            echo "<td><a href='" . htmlspecialchars($uploadBaseUrl . $relatorio['caminho_arquivo']) . "' download class='btn-baixar'>Baixar</a></td>";
            echo "</tr>";
        }
    }

} catch (PDOException $e) {
    error_log("Erro ao listar relatórios no DB: " . $e->getMessage());
    echo "<tr><td colspan='4'>Erro ao carregar relatórios. Tente novamente mais tarde.</td></tr>";
}
?>
