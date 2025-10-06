<?php
session_start();
include("../../BackEnd/gerar_alertas.php")
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="../imgs/vacaFavicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="../style/usuarios.css" />
  <link rel="shortcut icon" href="../imgs/IconeProjeto.png" type="image/x-icon" />
  <title>Página do Aluno</title>
</head>
<style>
  .conteudo {
    display: none;
  }

  /* Opcional: para garantir que a seção inicial seja exibida sem "flash" */
  #lista {
      display: block;
  }
</style>

<body>
  <!--barra superior-->
  <div class="faixa-decorada"></div>
  <header id="menu">
    <div class="logo-container">
      <img src="../imgs/iconePagPreto.png" alt="Logo do projeto" class="logo" />
      <div class="texto-logo">
        <h1 class="barlow-regular">CQL</h1>
        <h2 class="barlow-regular">Controle de Qualidade do Leite</h2>
      </div>
    </div>
    <!--botões de logout e informações do perfil-->
    <div class="caixa-perfil">
      <button id="btnPerfil" title="<?php echo htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>">
        <img src="../imgs/perfil.png" alt="Perfil de <?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?>">
      </button>
      <!-- caixa flutuando com informações do usuario logado-->
      <section id="menuPerfil" class="info-do-usuario" style="display: none;">
        <p><strong><?php echo htmlspecialchars($_SESSION['nome'] ?? 'Sem nome'); ?></strong></p>
        <p><?php echo htmlspecialchars($_SESSION['email'] ?? 'Sem email'); ?></p>
      </section>

      <button title="Sair" onclick="if(confirmarSaida()) location.href='../../FrontEnd/logout.php'">
        <img src="../imgs/sair.png" alt="Botão sair">
      </button>
    </div>
  </header>
  <div class="faixa-decorada"></div>

  <!--Menu de comandos-->
  <main class="painel">
    <nav class="menu-lateral">
      <h2>Comandos</h2>
      <ul>
        <li><a href="#" onclick="mostrarSecao('lista')">Vacas</a></li>
        <li><a href="#" onclick="mostrarSecao('producao')">Produção de leite</a></li>
        <li><a href="#" onclick="mostrarSecao('teste_mastite')">Teste de Mastite</a></li>
        <li><a href="#" onclick="mostrarSecao('relatorios')">Relatórios</a></li>
      </ul>
    </nav>

    <section id="lista" class="conteudo">
      <!--Mensagem de bem vindo com o nome do usuario logado-->
      <?php if (isset($_SESSION['mensagem'])): ?>
        <p class="mensagem" role="alert" id="mensagem-global"><?= $_SESSION['mensagem']; ?></p>
        <?php unset($_SESSION['mensagem']);
      endif; ?>
      <h3>Lista de vacas</h3>
       <!--sistema de busca na lista de vacas-->
      <input type="text" id="buscaVaca" list="vacasNomesDatalist" placeholder="Buscar por nome..."
        oninput="filtrarVacasTabela()" class="input-busca">
      <datalist id="vacasNomesDatalist"></datalist>
      <button onclick="mostrarSecao('cadastro')" class="btn">Cadastrar Nova Vaca</button>
      <table class="tabela" id="id-tabela-vacas">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Lote Manejo</th>
            <th>Descarte</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php require("../../BackEnd/listar_vacas.php") ?>
        </tbody>
      </table>
    </section>
    
    <section id="cadastro" class="conteudo bloco-pagina">
      <h3>Cadastro de vaca</h3>
      <!--formulario de cadastro de vacas-->
      <?php include('../mensagem.php') ?> <!-- inclue mensagem de vaca cadastrada com sucesso ou não-->
      <form class="form-padrao" method="POST" action="../../BackEnd/inserir_vaca.php">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" placeholder="Digite o nome da vaca" required>
        
        <label for="id_lote_manejo">Lote de Manejo</label>
        <select id="id_lote_manejo" name="id_lote_manejo" required>
            <option value="">Selecione o Lote</option>
            <?php
            // Inclui a conexão para buscar os lotes de manejo
            require_once("../../BackEnd/conexao.php");
            try {
                $stmt_lotes = $banco->query("SELECT id_lote_manejo, nome_lote FROM lote_manejo ORDER BY nome_lote");
                $lotes_manejo = $stmt_lotes->fetchAll(PDO::FETCH_ASSOC);
                foreach ($lotes_manejo as $lote) {
                    echo "<option value='{$lote['id_lote_manejo']}'>" . htmlspecialchars($lote['nome_lote']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value='' disabled>Erro ao carregar lotes: " . htmlspecialchars($e->getMessage()) . "</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn">Cadastrar</button>
      </form>
    </section>

    <section id="producao" class="conteudo">
      <h3>Lista de Produção de leite</h3>
      <!--sistema de busca na produção de leite-->
      <input type="text" id="buscaProducao" list="vacasNomesDatalist" placeholder="Buscar por nome..."
        onkeyup="filtrarProducao()" class="input-busca">
      <button onclick="mostrarSecao('cadastro_producao')" class="btn">Cadastrar Produção de Leite</button>
      <table class="tabela" id="id-tabela-producao">
        <thead>
          <tr>
            <th>ID</th>
            <th>ID Vaca</th>
            <th>Quantidade</th>
            <th>Tanque</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php require("../../BackEnd/listar_producao_leite.php") ?>
        </tbody>
      </table>
    </section>

    <section id="cadastro_producao" class="conteudo bloco-pagina">
      <h3>Produção de leite</h3>
      <?php include('../mensagem.php') ?>
       <!--formulario de cadastro de producao de leite-->
      <form class="form-padrao" method="POST" action="../../BackEnd/cadastrar_producao_leite.php">
        <label for="vaca_producao_nome">Vaca:</label>
        <input type="text" id="vaca_producao_nome" name="vaca_producao_nome" list="vacasNomesDatalist"
          placeholder="Digite ou selecione a vaca" required>
        <input type="hidden" id="id_vaca_producao_hidden" name="id_vaca">

        <label for="data_producao">Data:</label>
        <input type="date" id="data_producao" name="data" required>
        <label for="quantidade_producao">Quantidade (litros):</label>
        <input type="number" id="quantidade_producao" name="quantidade" step="0.1" required>
        
        <label for="id_tanque_producao">Tanque de Destino</label>
        <select id="id_tanque_producao" name="id_tanque" required>
            <option value="">Selecione o Tanque</option>
            <?php
            // Inclui a conexão para buscar os tanques
            require_once("../../BackEnd/conexao.php");
            try {
                // Assume-se que existe uma tabela 'tanque' com colunas 'id_tanque' e 'localizacao'
                $stmt_tanques = $banco->query("SELECT id_tanque, localizacao FROM tanque ORDER BY localizacao");
                $tanques = $stmt_tanques->fetchAll(PDO::FETCH_ASSOC);
                foreach ($tanques as $tanque) {
                    echo "<option value='{$tanque['id_tanque']}'>" . htmlspecialchars($tanque['localizacao']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value='' disabled>Erro ao carregar tanques: " . htmlspecialchars($e->getMessage()) . "</option>";
            }
            ?>
        </select>
        
        <button type="submit" class="btn">Cadastrar</button>
      </form>
    </section>


    <section id="teste_mastite" class="conteudo">
      <h3>Lista de Teste de Mastite</h3>
       <!--Busca na area do teste de mastite-->
      <input type="text" id="buscaTeste" list="vacasNomesDatalist" placeholder="Buscar por nome..."
        onkeyup="filtrarTeste()" class="input-busca">
      <button onclick="mostrarSecao('cadastro_teste')" class="btn">Cadastrar Teste de Mastite</button>
      <table class="tabela" id="id-tabela-teste">
        <thead>
          <tr>
            <th>ID</th>
            <th>ID Vaca</th>
            <th>Data</th>
            <th>Resultado</th>
            <th>Cruzes</th>
            <th>Úbere</th>
            <th>Tratamento</th>
            <th>Observações</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php require("../../BackEnd/listar_teste_mastite.php") ?>
        </tbody>
      </table>
    </section>

    <section id="cadastro_teste" class="conteudo bloco-pagina">
      <h3>Cadastro de Teste de Mastite</h3>
      <?php include('../mensagem.php') ?>
      <!--formulario do casdastro da mastite-->
      <form class="form-padrao" method="POST" action="../../BackEnd/cadastrar_teste_mastite.php">
        <label for="vaca_teste_nome">Vaca:</label>
        <input type="text" id="vaca_teste_nome" name="vaca_teste_nome" list="vacasNomesDatalist"
          placeholder="Digite ou selecione a vaca" required>
        <input type="hidden" id="id_vaca_teste_hidden" name="id_vaca">

        <label for="data_teste">Data do Teste:</label>
        <input type="date" id="data_teste" name="data" required>

        <label for="resultado">Resultado:</label>
        <select id="resultado" name="resultado" required>
          <option value="1">Positivo</option>
          <option value="0">Negativo</option>
        </select>

        <label for="cruzes">Quantidade de Cruzes:</label>
        <input type="number" id="cruzes" name="quantas_cruzes" min="0" max="4" required>

        <fieldset class="uberes-teste">
          <legend>Úbere Afetado:</legend>
          <label class="opcao-ubre">
            <input type="checkbox" name="ubere[]" value="D.E">
            <span>Dianteiro Esquerdo</span>
          </label>
          <label class="opcao-ubre">
            <input type="checkbox" name="ubere[]" value="D.D">
            <span>Dianteiro Direito</span>
          </label>
          <label class="opcao-ubre">
            <input type="checkbox" name="ubere[]" value="T.E">
            <span>Traseiro Esquerdo</span>
          </label>
          <label class="opcao-ubre">
            <input type="checkbox" name="ubere[]" value="T.D">
            <span>Traseiro Direito</span>
          </label>
        </fieldset>

        <label for="tratamento">Tratamento:</label>
        <textarea id="tratamento" name="tratamento" rows="3"></textarea>

        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"></textarea>
        <button type="submit" class="btn">Cadastrar</button>
      </form>
    </section>

    <section id="relatorios" class="conteudo bloco-pagina">
      <h3>Envio de Relatórios</h3>
      <!--formulario de envio de arquivos para relatório-->
      <form id="form-upload-relatorio" action="../../BackEnd/upload_relatorio.php" method="POST" enctype="multipart/form-data">
          <label for="arquivo_relatorio" class="btn-escolher-arquivo">Escolher arquivo</label>
          <span id="nome-arquivo-selecionado">Nenhum arquivo escolhido</span>
          <input type="file" name="arquivo" id="arquivo_relatorio" required>
          <button type="submit" class="btn" id="btn-enviar-relatorio" disabled>Enviar Relatório</button>
          
      </form>
    </section>

  </main>
  <script src="script_Aluno.js"></script>
</body>

</html>