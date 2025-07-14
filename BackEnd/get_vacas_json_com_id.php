<?php
// inclui o arquivo que faz a conexão com o banco de dados
// esse caminho assume que 'conexao.php' tá na mesma pasta que esse arquivo
require("conexao.php");

// fala pro navegador que a resposta que a gente vai mandar é um json
header('Content-Type: application/json');

$vacasData = []; // cria uma lista vazia pra guardar os dados das vacas (id e nome)

try {
    // tenta pegar o id e o nome de todas as vacas da tabela 'vacas'
    // e organiza elas em ordem alfabética pelo nome
    $stmt = $banco->query("SELECT id_vaca, nome FROM vacas ORDER BY nome ASC");
    $vacas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // pra cada vaca que a gente achou no banco
    foreach ($vacas as $vaca) {
        // a gente adiciona o id e o nome dela na nossa lista 'vacasData'
        $vacasData[] = [
            'id_vaca' => $vaca['id_vaca'],
            'nome' => $vaca['nome']
        ];
    }

    // transforma a lista de dados das vacas em um texto json e mostra na tela
    echo json_encode($vacasData);

} catch (PDOException $e) { // se der algum problema na conexão ou na busca do banco
    // a gente anota o erro nos registros do servidor (pra gente ver depois)
    error_log("Erro ao buscar IDs e nomes de vacas para datalist: " . $e->getMessage());
    // e manda uma lista vazia pro navegador, pra não quebrar a página
    echo json_encode([]);
}
?>