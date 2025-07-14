<?php
require_once 'conexao.php'; // Inclui a conexão com o banco
require_once 'ordenar.php'; // Inclui o arquivo com a função de ordenação

try {
    // Executa a query para obter dados da produção de leite junto com o nome da vaca
    $stmt = $banco->query("SELECT pl.id_producao, pl.id_vaca, v.nome AS nome_vaca, pl.quantidade, pl.data FROM producao_leite pl JOIN vacas v ON pl.id_vaca = v.id_vaca");
    
    // Busca todos os resultados como array associativo
    $producoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Aplica a ordenação rápida (quicksort) por data nas produções
    $producoes = quicksortPorData($producoes);
    
    // Loop para exibir cada produção na tabela HTML
    foreach ($producoes as $producao) {
        echo "<tr data-id=\"{$producao['id_producao']}\" data-id-vaca=\"{$producao['id_vaca']}\">";
        echo "<td>" . htmlspecialchars($producao['id_producao']) . "</td>";
        echo "<td>" . htmlspecialchars($producao['nome_vaca']) . " (ID: " . htmlspecialchars($producao['id_vaca']) . ")</td>";
        echo "<td class=\"quantidade-producao\">" . htmlspecialchars($producao['quantidade']) . " L</td>";
        echo "<td class=\"data-producao\">" . htmlspecialchars($producao['data']) . "</td>";
        echo "<td>";
        echo "<button onclick=\"editarProducao(this)\" class=\"btn-editar\">Editar</button>";
        echo "<button onclick=\"excluirProducao({$producao['id_producao']}, this)\" class=\"btn-excluir\">Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    // Caso ocorra erro na query, exibe mensagem na tabela
    echo "<tr><td colspan='5'>Erro ao carregar produções de leite: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}


?>
