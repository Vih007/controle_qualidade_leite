<?php
session_start();
require('conexao.php'); 
require_once __DIR__ . '/repository/ProducaoLeiteRepository.php';
require_once __DIR__ . '/service/ProducaoLeiteService.php';
require_once __DIR__ . '/dto/ProducaoLeiteDTO.php';
include('criar_alerta.php');

if (isset($_POST['id_vaca'], $_POST['quantidade'], $_POST['data'], $_POST['id_tanque'])) {
    
    $id_vaca = (int)$_POST['id_vaca'];
    $quantidade = (float)$_POST['quantidade'];
    $data = $_POST['data'];
    $id_tanque = (int)$_POST['id_tanque'];
    
    try {
        // Aqui garantimos que batched = 0 ao criar
        $producaoDto = new ProducaoLeiteDTO($id_vaca, $quantidade, $data, $id_tanque, 0); // 0 = não loteado
        
        $repository = new ProducaoLeiteRepository($banco); 
        $service = new ProducaoLeiteService($repository);
        
        if ($service->cadastrarProducao($producaoDto)) {
            criar_alerta("Produção de leite inserida com sucesso!", "sucesso");
            header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=producao");
            exit;
        }
        
    } catch (PDOException $e) {
        criar_alerta("Erro no banco de dados: Verifique se a Vaca e o Tanque existem.", "erro");
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_producao");
        exit;
    } catch (Exception $e) {
        criar_alerta("Erro de validação: " . $e->getMessage(), "erro");
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_producao");
        exit;
    }
} else {
    criar_alerta("Erro: Dados incompletos na requisição de produção de leite.", "erro");
    header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro_producao");
    exit;
}
?>
