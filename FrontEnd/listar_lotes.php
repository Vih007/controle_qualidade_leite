<?php
require("conexao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Lotes</title>
</head>
<body>
    <h2>Lista de Lotes</h2>
    <a href="cadastro_lote.php">Cadastrar Novo Lote</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Quantidade Total</th>
            <th>Tanque</th>
            <th>Ações</th>
        </tr>
        <?php
        try {
            $limite = 10;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($pagina < 1) $pagina = 1;
            $offset = ($pagina - 1) * $limite;

            $stmt = $banco->prepare("SELECT * FROM lote_leite LIMIT :limite OFFSET :offset");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $banco->query("SELECT COUNT(*) FROM lote_leite")->fetchColumn();
            $totalPaginas = ceil($total / $limite);

            if ($lotes) {
                foreach ($lotes as $lote) {
                    echo "<tr>";
                    echo "<td>{$lote['id']}</td>";
                    echo "<td>{$lote['data']}</td>";
                    echo "<td>{$lote['quantidade_total']} L</td>";
                    echo "<td>{$lote['tanque']}</td>";
                    echo "<td>
                            <a href='editar_lote.php?id={$lote['id']}'>Editar</a> | 
                            <a href='excluir_lote.php?id={$lote['id']}' onclick=\"return confirm('Deseja excluir este lote?');\">Excluir</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'><strong>Nenhum lote encontrado</strong></td></tr>";
            }

            echo "<tr><td colspan='5'>";
            if ($pagina > 1) {
                echo "<a href='listar_lotes.php?pagina=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
            }
            echo "Página $pagina de $totalPaginas";
            if ($pagina < $totalPaginas) {
                echo " <a href='listar_lotes.php?pagina=" . ($pagina + 1) . "'>Próxima ➡</a>";
            }
            echo "</td></tr>";

        } catch (PDOException $e) {
            echo "<tr><td colspan='5'><strong>Erro: " . $e->getMessage() . "</strong></td></tr>";
        }
        ?>
    </table>
</body>
</html>
