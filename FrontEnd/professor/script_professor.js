// --- 1. Controle do menu de perfil (dropdown) ---
const botaoPerfilProf = document.getElementById('btnPerfil');
const menuPerfilProf = document.getElementById('menuPerfil');

if (botaoPerfilProf && menuPerfilProf) {
  botaoPerfilProf.addEventListener('click', function (e) {
    e.stopPropagation();
    menuPerfilProf.style.display = (menuPerfilProf.style.display === 'block') ? 'none' : 'block';
  });

  document.addEventListener('click', function (e) {
    if (!menuPerfilProf.contains(e.target) && e.target !== botaoPerfilProf) {
      menuPerfilProf.style.display = 'none';
    }
  });
}

// --- 2. Função de confirmação de saída ---
function confirmarSaida() {
  return confirm("Deseja realmente sair?");
}

// --- 3. Função para abrir abas (usada via onclick inline) ---
function abrirAba(evt, nomeAba) {
  const secoes = document.querySelectorAll("main .aba-conteudo");
  const botoes = document.querySelectorAll("nav button.btn"); // Botões que ativam abas

  // Oculta todas as seções
  secoes.forEach(secao => secao.style.display = "none");

  // Remove classe 'ativo' de todos os botões
  botoes.forEach(botao => botao.classList.remove("ativo"));

  // Exibe a aba selecionada
  const abaParaAbrir = document.getElementById(nomeAba);
  if (abaParaAbrir) {
    abaParaAbrir.style.display = "block";
  } else {
    console.error(`Erro: seção com id '${nomeAba}' não encontrada.`);
  }

  // Marca o botão clicado como ativo
  if (evt && evt.currentTarget) {
    evt.currentTarget.classList.add("ativo");
  }
}

// --- 4. Função para abrir formulário de cadastro ---
function mostrarSecao(secao) {
  document.querySelectorAll('.aba-conteudo, .conteudo').forEach(function(sec) {
    sec.style.display = 'none';
  });

  document.querySelectorAll('nav button.btn').forEach(btn => btn.classList.remove('ativo'));

  const elemento = document.getElementById(secao);
  if (elemento) {
    elemento.style.display = 'block';
  } else {
    console.error(`Seção ${secao} não encontrada.`);
  }
}


// --- 5. Inicialização ao carregar o DOM ---
document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM carregado e script_professor.js ativo!");

  // Define a aba padrão (primeiro botão)
  const primeiroBotao = document.querySelector('nav button.btn');
  if (primeiroBotao) {
    primeiroBotao.click();
  }

  // Botão sair
  const btnSairProf = document.querySelector('button[title="Sair"]');
  if (btnSairProf) {
    btnSairProf.addEventListener('click', function () {
      if (confirmarSaida()) {
        window.location.href = '../../FrontEnd/logout.php';
      }
    });
  }
});
