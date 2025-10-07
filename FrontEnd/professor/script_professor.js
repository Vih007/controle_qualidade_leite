console.log("JS foi carregado!");
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM carregado!");
});

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

// --- 3. Função para abrir seções ---
function mostrarSecao(secao) {
  // Esconde tudo
  document.querySelectorAll('.aba-conteudo, .conteudo').forEach(sec => sec.style.display = 'none');
  document.querySelectorAll('nav button.btn').forEach(btn => btn.classList.remove('ativo'));

  const elemento = document.getElementById(secao);
  if (elemento) {
    elemento.style.display = 'block';
    // Salvar a seção ativa no localStorage
    localStorage.setItem("secaoAtiva", secao);
  }
}

// --- 4. Funções de edição e exclusão de alunos ---
function editarAluno(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');

  const nomeCell = row.querySelector('.nome-aluno');
  const emailCell = row.querySelector('.email-aluno');

  const nomeAtual = nomeCell.textContent.trim();
  const emailAtual = emailCell.textContent.trim();

  nomeCell.innerHTML = `<input type="text" class="input-edicao" value="${nomeAtual}">`;
  emailCell.innerHTML = `<input type="email" class="input-edicao" value="${emailAtual}">`;

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar-aluno');
  button.classList.add('btn-salvar-aluno');
  button.onclick = function () {
    const novoNome = nomeCell.querySelector('input').value;
    const novoEmail = emailCell.querySelector('input').value;
    salvarEdicaoAluno(id, novoNome, novoEmail, row, button);
  };
}

function salvarEdicaoAluno(id, nome, email, row, button) {
  fetch('../../BackEnd/editar_aluno.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      row.querySelector('.nome-aluno').textContent = nome;
      row.querySelector('.email-aluno').textContent = email;
      button.textContent = 'Editar';
      button.classList.remove('btn-salvar-aluno');
      button.classList.add('btn-editar-aluno');
      button.onclick = function () { editarAluno(button); };
    } else {
      alert('Erro ao atualizar aluno: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    alert('Erro na requisição: ' + error);
  });
}

function excluirAluno(id, button) {
  if (confirm('Tem certeza que deseja excluir este aluno?')) {
    const row = button.closest('tr');
    fetch('../../BackEnd/excluir_aluno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        row.remove();
      } else {
        alert('Erro ao excluir aluno: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Erro ao excluir:', error);
      alert('Erro ao excluir aluno.');
    });
  }
}

// --- 5. Marcar alerta como lido ---
function marcarComoLido(id, btn) {
  const formData = new FormData();
  formData.append('id', id);

  fetch('../../BackEnd/marcar_lido.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      const tr = btn.closest('tr');
      const statusCell = tr.querySelector('.status-celula');
      
      if (statusCell) {
        statusCell.innerHTML = '<span class="icon-visualizado">&#x2714;&#x2714;</span>';
      }
      setTimeout(() => { tr.remove(); }, 2 * 24 * 60 * 60 * 1000);
    } else {
      alert("Erro ao marcar como lido: " + response.message);
    }
  })
  .catch(error => {
    console.error("Erro na requisição:", error);
    alert("Erro na requisição: " + error);
  });
}

// --- 6. Carregar alertas (chama listar_alertas.php) ---
function carregarAlertas() {
  fetch('../../BackEnd/listar_alertas.php')
    .then(res => res.text())
    .then(html => {
      const tbody = document.getElementById('conteudo-alertas');
      if (tbody) {
        tbody.innerHTML = html;
        adicionarListenersAlerta();
      }
    })
    .catch(error => console.error('Erro ao carregar alertas:', error));
}

function adicionarListenersAlerta() {
  document.querySelectorAll('.btn-marcar-lido').forEach(btn => {
    btn.addEventListener('click', function() {
      const tr = this.closest('tr');
      const id = tr.getAttribute('data-id');
      if (id) {
        marcarComoLido(id, this);
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', function () {
  const formRelatorio = document.getElementById('form-relatorio-lote');
  const container = document.getElementById('relatorio-unico-container');

  if (formRelatorio) {
    formRelatorio.addEventListener('submit', function (e) {
      e.preventDefault();
      const id = document.getElementById('id_lote').value.trim();
      if (!id) return;

      fetch('../../BackEnd/relatorio_lote_card.php?id_lote=' + encodeURIComponent(id))
        .then(resp => resp.text())
        .then(html => {
          container.innerHTML = html;
        })
        .catch(err => {
          console.error(err);
          container.innerHTML = "<p><strong>Erro ao gerar relatório.</strong></p>";
        });
    });
  }
});

// Em FrontEnd/aluno/script_Aluno.js

function filtrarScores() {
  const input = document.getElementById('buscaScore');
  const filtro = input.value.toUpperCase();
  const container = document.querySelector('.cards-scores-container');
  
  // Seleciona todos os cards de score
  const cards = container.getElementsByClassName('score-card'); 

  for (let i = 0; i < cards.length; i++) { 
    // Busca o nome da vaca dentro do card (tag <h4> com a classe card-nome)
    const nomeVacaElement = cards[i].querySelector('.card-nome'); 

    if (nomeVacaElement) {
      const textoNome = nomeVacaElement.textContent || nomeVacaElement.innerText;

      if (textoNome.toUpperCase().indexOf(filtro) > -1) {
        // Se o nome corresponder, exibe o card (usando 'flex' pois é o display natural dos cards)
        cards[i].style.display = 'flex'; 
      } else {
        // Caso contrário, esconde
        cards[i].style.display = 'none';
      }
    }
  }
  
  // Lógica para esconder a Paginação durante a filtragem
  const paginacao = document.querySelector('.paginacao-scores');
  if (paginacao) {
      if (filtro.length > 0) {
          paginacao.style.display = 'none';
      } else {
          paginacao.style.display = 'block';
      }
  }
}


// --- 7. Inicialização ---
document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM carregado e script_professor.js ativo!");

  // **NOVO:** Configura a navegação
  configurarMenuNavegacao();

  // Recupera a última seção ativa
  const ultimaSecao = localStorage.getItem("secaoAtiva") || "alertas";
  mostrarSecao(ultimaSecao);

  // Se for alertas, carrega os dados
  if (ultimaSecao === "alertas") {
    carregarAlertas();
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

function configurarMenuNavegacao() {
    // Adiciona o listener de clique a todos os links do menu lateral
    document.querySelectorAll('.menu-lateral a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const secaoAlvo = this.getAttribute('data-secao');
            if (secaoAlvo) {
                mostrarSecao(secaoAlvo);
            }
        });
    });
}

    document.getElementById("form-gerar-lote").addEventListener("submit", async function(e){
          e.preventDefault();
          const form = e.target;
          const dados = new FormData(form);

          const container = document.getElementById("relatorio-unico-container");
          container.innerHTML = "Gerando lote...";

          try {
              const resp = await fetch("../../BackEnd/salvar_lotes.php", {method:"POST", body:dados});
              const resultado = await resp.json();

              if(resultado.status === "ok") {
                  container.innerHTML = `
                    <div class="relatorio-lote">
                        <h3>Lote Gerado com Sucesso!</h3>
                        <p><strong>ID do Lote:</strong> ${resultado.lote.id}</p>
                        <p><strong>Data:</strong> ${resultado.lote.data}</p>
                        <p><strong>Quantidade Total:</strong> ${resultado.lote.quantidade_total} L</p>
                        <p><strong>Tanque:</strong> ${resultado.lote.tanque}</p>
                        <p><strong>Vacas:</strong> ${resultado.lote.vacas}</p>
                    </div>
                `;
                container.style.display = 'block';
              } else {
                  container.innerHTML = `<p style="color:red;">Erro: ${resultado.mensagem}</p>`;
              }
          } catch(err) {
              container.innerHTML = `<p style="color:red;">Erro ao gerar lote: ${err.message}</p>`;
          }
      });
