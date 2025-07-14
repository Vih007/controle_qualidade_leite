
const botaoPerfil = document.getElementById('btnPerfil');
const menuPerfil = document.getElementById('menuPerfil');

botaoPerfil.addEventListener('click', function (e) {
  e.stopPropagation(); // impede que o clique feche a caixa imediatamente
  menuPerfil.style.display = (menuPerfil.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', function (e) {
  if (!menuPerfil.contains(e.target) && e.target !== botaoPerfil) {
    menuPerfil.style.display = 'none';
  }
});

function confirmarSaida() {
  return confirm("Deseja realmente sair?");
}

// Objeto global para armazenar o mapeamento de nome para ID
let vacaNomesParaIds = {};

// Função para mostrar seções
function mostrarSecao(secao) {
  // Esconder todas as seções
  document.querySelectorAll('.conteudo').forEach(function (sec) {
    sec.style.display = 'none';
  });

  // Mostrar apenas a seção desejada
  document.getElementById(secao).style.display = 'block';
}

// NOVA FUNÇÃO: Carregar os nomes e IDs das vacas para o datalist e mapeamento
function carregarNomesVacasDatalist() {
  const vacasNomesDatalist = document.getElementById('vacasNomesDatalist');
  vacasNomesDatalist.innerHTML = ''; // Limpa as opções existentes
  vacaNomesParaIds = {}; // Limpa o mapeamento existente

  // Alterar o endpoint para buscar ID e Nome
  fetch('../../BackEnd/get_vacas_json_com_id.php') // Novo endpoint PHP que retorna ID e Nome
    .then(response => {
      if (!response.ok) {
        throw new Error('Erro ao carregar os nomes das vacas: ' + response.statusText);
      }
      return response.json();
    })
    .then(vacas => {
      vacas.forEach(vaca => {
        const option = document.createElement('option');
        option.value = vaca.nome;
        vacasNomesDatalist.appendChild(option);
        vacaNomesParaIds[vaca.nome] = vaca.id_vaca; // Armazena o mapeamento
      });
    })
    .catch(error => {
      console.error('Erro na requisição para carregar datalist:', error);
    });
}

// Funções para lidar com os inputs de datalist nos formulários de cadastro
function setupDatalistInput(inputElementId, hiddenInputElementId) {
  const inputElement = document.getElementById(inputElementId);
  const hiddenInputElement = document.getElementById(hiddenInputElementId);

  if (inputElement && hiddenInputElement) {
    inputElement.addEventListener('input', function () {
      const selectedName = this.value;
      if (vacaNomesParaIds[selectedName]) {
        hiddenInputElement.value = vacaNomesParaIds[selectedName];
      } else {
        hiddenInputElement.value = ''; // Limpa se o nome não for válido
      }
    });

    // Limpar o input e hidden ao exibir a seção de cadastro
    // Isso pode ser ajustado dependendo de como você quer que os formulários se comportem ao serem mostrados
    inputElement.value = '';
    hiddenInputElement.value = '';
  }
}


// Função para filtrar a tabela de vacas
function filtrarVacasTabela() {
  const input = document.getElementById('buscaVaca');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-vacas');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) { // Começa do 1 para pular o cabeçalho
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome

    if (colunaNome) {
      const textoNome = colunaNome.textContent || colunaNome.innerText;

      if (textoNome.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarVaca(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');
  const nomeCell = row.querySelector('.nome-vaca');
  const nomeAtual = nomeCell.textContent;

  const input = document.createElement('input');
  input.type = 'text';
  input.value = nomeAtual;
  input.className = 'input-edicao';

  nomeCell.textContent = '';
  nomeCell.appendChild(input);
  input.focus();

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar');
  button.classList.add('btn-salvar');
  button.onclick = function () {
    salvarEdicaoVaca(id, input.value, nomeCell, button);
  };
}

function salvarEdicaoVaca(id, novoNome, nomeCell, button) {
  fetch('../../BackEnd/atualizar_vaca.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_vaca=${id}&novo_nome=${encodeURIComponent(novoNome)}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        nomeCell.textContent = novoNome;
        button.textContent = 'Editar';
        button.classList.remove('btn-salvar');
        button.classList.add('btn-editar');
        button.onclick = function () {
          editarVaca(button);
        };
        carregarNomesVacasDatalist(); // CHAMADA ADICIONADA AQUI
      } else {
        alert('Erro ao atualizar: ' + data.message);
        location.reload();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Erro ao atualizar a vaca');
      location.reload();
    });
}

function excluirVaca(id, button) {
  if (confirm('Tem certeza que deseja excluir esta vaca?')) {
    const row = button.closest('tr');

    fetch('../../BackEnd/deletar_vaca.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_vaca=${id}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          row.remove();
          const mensagem = document.createElement('div');
          mensagem.className = 'mensagem-sucesso';
          mensagem.textContent = 'Vaca excluída com sucesso!';
          document.body.appendChild(mensagem);
          setTimeout(() => mensagem.remove(), 3000);
          carregarNomesVacasDatalist(); // CHAMADA ADICIONADA AQUI
        } else {
          alert('Erro ao excluir: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erro ao excluir a vaca');
      });
  }
}

function filtrarProducao() {
  const input = document.getElementById('buscaProducao');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-producao');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) {
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome da vaca

    if (colunaNome) {
      const texto = colunaNome.textContent || colunaNome.innerText;

      if (texto.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarProducao(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');
  // CORREÇÃO: Usar a classe correta para a célula de quantidade
  const quantidadeCell = row.querySelector('.quantidade-producao');
  const dataCell = row.querySelector('.data-producao');

  const qtdAtual = quantidadeCell.textContent;
  const dataAtual = dataCell.textContent;

  quantidadeCell.innerHTML = `<input type="number" class="input-edicao" value="${qtdAtual}" step="0.1">`;
  dataCell.innerHTML = `<input type="date" class="input-edicao" value="${dataAtual}">`;

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar');
  button.classList.add('btn-salvar');
  button.onclick = function () {
    const novaQtd = quantidadeCell.querySelector('input').value;
    const novaData = dataCell.querySelector('input').value;
    salvarEdicaoProducao(id, novaQtd, novaData, row, button);
  };
}

function salvarEdicaoProducao(id, novaQtd, novaData, row, button) {
  const idVaca = row.getAttribute('data-id-vaca'); // pegar id da vaca da linha

  fetch('../../BackEnd/atualizar_producao_leite.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_producao=${id}&id_vaca=${idVaca}&quantidade=${encodeURIComponent(novaQtd)}&data=${encodeURIComponent(novaData)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        row.querySelector('.quantidade-producao').textContent = novaQtd + ' L';
        row.querySelector('.data-producao').textContent = novaData;
        button.textContent = 'Editar';
        button.classList.remove('btn-salvar');
        button.classList.add('btn-editar');
        button.onclick = function () {
          editarProducao(button);
        };
      } else {
        alert('Erro ao atualizar: ' + data.message);
      }
    })
    .catch(error => {
      alert('Erro na requisição: ' + error);
    });
}


function excluirProducao(id, button) {
  if (confirm('Tem certeza que deseja excluir essa produção?')) {
    const row = button.closest('tr');
    if (!row) {
      alert('Erro: não foi possível encontrar a linha da produção para excluir.');
      return;
    }

    fetch('../../BackEnd/excluir_producao_leite.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_producao=${id}`
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          row.remove();

          // Mostra uma mensagem de sucesso, igual na exclusão de vaca
          const mensagem = document.createElement('div');
          mensagem.className = 'mensagem-sucesso'; // Use uma classe de CSS para estilizar
          mensagem.textContent = 'Produção excluída com sucesso!';
          document.body.appendChild(mensagem);
          setTimeout(() => mensagem.remove(), 3000); // Remove a mensagem após 3 segundos

        } else {
          // Exibe a mensagem de erro vinda do PHP
          alert('Erro ao excluir: ' + (data.message || 'Ocorreu um erro desconhecido.'));
        }
      })
      .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro ao conectar com o servidor para excluir a produção.');
      });
  }
}

function filtrarTeste() {
  const input = document.getElementById('buscaTeste');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-teste');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) {
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome da vaca

    if (colunaNome) {
      const texto = colunaNome.textContent || colunaNome.innerText;

      if (texto.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarTeste(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');
  const resultadoCell = row.querySelector('.resultado-teste');
  const cruzesCell = row.querySelector('.cruzes-teste');
  const tratamentoCell = row.querySelector('.tratamento-teste');

  const resultadoAtual = resultadoCell.textContent.trim() === 'Positivo' ? 1 : 0;
  const cruzesAtual = cruzesCell.textContent.trim();
  const tratamentoAtual = tratamentoCell.textContent.trim();

  resultadoCell.innerHTML = `
        <select class="input-edicao">
            <option value="1" ${resultadoAtual == 1 ? 'selected' : ''}>Positivo</option>
            <option value="0" ${resultadoAtual == 0 ? 'selected' : ''}>Negativo</option>
        </select>`;
  cruzesCell.innerHTML = `<input type="number" class="input-edicao" value="${cruzesAtual}" min="0" max="4">`;
  tratamentoCell.innerHTML = `<input type="text" class="input-edicao" value="${tratamentoAtual}">`;

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar');
  button.classList.add('btn-salvar');
  button.onclick = function () {
    const novoResultado = resultadoCell.querySelector('select').value;
    const novasCruzes = cruzesCell.querySelector('input').value;
    const novoTratamento = tratamentoCell.querySelector('input').value;
    salvarEdicaoTeste(id, novoResultado, novasCruzes, novoTratamento, row, button);
  };
}

function salvarEdicaoTeste(id, resultado, cruzes, tratamento, row, button) {
  fetch('../../BackEnd/atualizar_teste_mastite.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_teste=${id}&resultado=${resultado}&quantas_cruzes=${cruzes}&tratamento=${encodeURIComponent(tratamento)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        row.querySelector('.resultado-teste').textContent = resultado == 1 ? 'Positivo' : 'Negativo';
        row.querySelector('.cruzes-teste').textContent = cruzes;
        row.querySelector('.tratamento-teste').textContent = tratamento;
        button.textContent = 'Editar';
        button.classList.remove('btn-salvar');
        button.classList.add('btn-editar');
        button.onclick = function () {
          editarTeste(button);
        };
      } else {
        alert('Erro ao atualizar: ' + data.message);
      }
    });
}

function excluirTeste(id, button) {
  if (confirm('Tem certeza que deseja excluir este teste?')) {
    const row = button.closest('tr');
    fetch('../../BackEnd/excluir_teste_mastite.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_teste=${id}`
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          row.remove();
        } else {
          alert('Erro ao excluir: ' + data.message);
        }
      });
  }
}

function mostrarSecao(secao) {
  // Esconde todas as seções
  document.querySelectorAll('.conteudo').forEach(function (sec) {
    sec.style.display = 'none';
  });

  // Mostra a seção desejada
  const secaoAlvo = document.getElementById(secao);
  secaoAlvo.style.display = 'block';

  // Se for a seção de cadastro de produção ou teste, configure o datalist
  if (secao === 'cadastro_producao') {
    setupDatalistInput('vaca_producao_nome', 'id_vaca_producao_hidden');
  } else if (secao === 'cadastro_teste') {
    setupDatalistInput('vaca_teste_nome', 'id_vaca_teste_hidden');
  }

  // Se houver uma mensagem global, move ela para o topo da seção ativa
  const mensagem = document.getElementById('mensagem-global');
  if (mensagem && secaoAlvo) {
    secaoAlvo.prepend(mensagem);
  }

  // Remove a mensagem se ainda existir após 4 segundos
  if (mensagem) {
    setTimeout(() => {
      mensagem.remove();
    }, 5000);
  }
}

// Garante que a seção correta está visível e carrega o datalist
window.onload = function () {
  const params = new URLSearchParams(window.location.search);
  const secao = params.get('secao');
  if (secao) {
    mostrarSecao(secao);
  } else {
    mostrarSecao('lista'); // Default para a lista de vacas
  }
  carregarNomesVacasDatalist(); // CHAMADA ADICIONADA AQUI PARA GARANTIR O CARREGAMENTO INICIAL
};
