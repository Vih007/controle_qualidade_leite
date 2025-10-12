<?php
session_start();
require('conexao.php'); 
require_once __DIR__ . '/repository/ProducaoLeiteRepository.php';
require_once __DIR__ . '/service/ProducaoLeiteService.php';
require_once __DIR__ . '/dto/ProducaoLeiteDTO.php';
include('criar_alerta.php');

// --- VERIFICAÇÃO DE CONEXÃO COM O BANCO ---

if (isset($_POST['id_vaca'], $_POST['quantidade'], $_POST['data'], $_POST['id_tanque'])) {
    
    $id_vaca = (int)$_POST['id_vaca'];
    $quantidade = (float)$_POST['quantidade'];
    $data = $_POST['data'];
    $id_tanque = (int)$_POST['id_tanque'];
    
    $producaoDto = new ProducaoLeiteDTO($id_vaca, $quantidade, $data, $id_tanque, 0);

    $max_retries = 3;
    $retries = 0;
    $success = false;
    $last_exception = null;

    while ($retries < $max_retries && !$success) {
        try {
            if (is_null($banco)) {
                throw new PDOException("Falha na conexão com o banco de dados.");
            }
            
            $repository = new ProducaoLeiteRepository($banco); 
            $service = new ProducaoLeiteService($repository);
            
            if ($service->cadastrarProducao($producaoDto)) {
                $success = true;
                break;
            }
            
        } catch (\PDOException $e) {
            $last_exception = $e;
            $retries++;
            error_log("Tentativa de INSERT falhou. Retry #{$retries}/{$max_retries}: " . $e->getMessage());
            if ($retries < $max_retries) sleep(1);
        } catch (\Exception $e) {
            criar_alerta("Erro de validação: " . $e->getMessage(), "erro");
            header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_producao");
            exit;
        }
    }

    if ($success) {
        criar_alerta("Produção de leite inserida com sucesso!", "sucesso");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=producao");
        exit;
    } else {
        criar_alerta("Erro no banco de dados. Tente novamente mais tarde.", "erro");
        header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=producao");
        exit;
    }

} else {
    criar_alerta("Erro: Dados incompletos na requisição de produção de leite.", "erro");
    header("Location: ../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_producao");
    exit;
}
