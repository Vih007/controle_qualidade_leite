<?php
require_once 'conexao.php';
require_once 'ordenar.php';

try {
    $stmt = $banco->query("SELECT tm.id_teste, tm.id_vaca, v.nome AS nome_vaca, tm.data, tm.resultado, tm.quantas_cruzes, tm.ubere, tm.tratamento, tm.observacoes FROM teste_mastite tm JOIN vacas v ON tm.id_vaca = v.id_vaca");//removido order by para desordenar
    $testes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //chama o algoritmo de ordenação
    $testes = quicksortPorData($testes);

    foreach ($testes as $teste) {
        echo "<tr data-id=\"{$teste['id_teste']}\">";
        echo "<td>" . htmlspecialchars($teste['id_teste']) . "</td>";
        echo "<td>" . htmlspecialchars($teste['nome_vaca']) . " (ID: " . htmlspecialchars($teste['id_vaca']) . ")</td>";
        echo "<td>" . date("d/m/Y", strtotime($teste['data'])) . "</td>";
        echo "<td class=\"resultado-teste\">" . htmlspecialchars(ucfirst($teste['resultado'])) . "</td>";
        echo "<td class=\"cruzes-teste\">" . htmlspecialchars($teste['quantas_cruzes']) . "</td>";
        echo "<td class=\"uberes-teste\">" . htmlspecialchars($teste['ubere']) . "</td>";
        echo "<td class=\"tratamento-teste\">" . htmlspecialchars($teste['tratamento']) . "</td>";
        echo "<td class=\"observacoes-teste\">" . htmlspecialchars($teste['observacoes']) . "</td>";
        echo "<td>";
        echo "<button onclick=\"editarTeste(this)\" class=\"btn-editar\">Editar</button>";
        echo "<button onclick=\"excluirTeste({$teste['id_teste']}, this)\" class=\"btn-excluir\">Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='9'>Erro ao carregar testes de mastite: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>