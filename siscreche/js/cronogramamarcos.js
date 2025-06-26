let currentTitleIndex = 0;
let subIndex = 1;

document.querySelector('form').addEventListener('submit', function(event) {
  const form = this;
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const linhas = table.rows;
  let temLinhaValida = false;

  let tituloIndex = -1;
  let datasInicio = [];
  let datasTermino = [];


  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];
    const id = linha.getAttribute('data-id');
    const cells = linha.cells;

    const etapaField = cells[0].querySelector('textarea, input');
    const tipo = etapaField?.placeholder === 'Título' ? 'subtitulo' : 'linha';

    const campos = [
      etapaField?.value.trim() || '',
      cells[1].querySelector('input')?.value.trim() || '',
      cells[2].querySelector('input')?.value.trim() || '',
      cells[3].querySelector('input')?.value.trim() || '',
      cells[4].querySelector('input')?.value.trim() || '',
      cells[5].querySelector('input')?.value.trim() || ''
    ];

    const linhaEstaVazia = campos.every(c => c === '');
    if (linhaEstaVazia) continue;

    temLinhaValida = true;

    if (!id) {
      const inputTipo = document.createElement('input');
      inputTipo.type = 'hidden';
      inputTipo.name = 'tipo_etapa[]';
      inputTipo.value = tipo;
      form.appendChild(inputTipo);

      const inputId = document.createElement('input');
      inputId.type = 'hidden';
      inputId.name = 'ids[]';
      inputId.value = '';
      form.appendChild(inputId);
    }

    if (tipo === 'subtitulo') {
      if (tituloIndex !== -1 && datasInicio.length > 0 && datasTermino.length > 0) {
        preencherDatas(linhas[tituloIndex], datasInicio, datasTermino);
      }
      tituloIndex = i;
      datasInicio = [];
      datasTermino = [];
    } else if (tipo === 'linha') {
      const dtInicio = cells[1].querySelector('input')?.value;
      const dtFim = cells[2].querySelector('input')?.value;
      if (dtInicio) datasInicio.push(dtInicio);
      if (dtFim) datasTermino.push(dtFim);
    }
  }

  if (tituloIndex !== -1 && datasInicio.length > 0 && datasTermino.length > 0) {
    preencherDatas(linhas[tituloIndex], datasInicio, datasTermino);
  }

  function preencherDatas(tituloRow, inicios, fins) {
    const campoInicio = tituloRow.querySelector('input[name="inicio_previsto[]"]');
    const campoFim = tituloRow.querySelector('input[name="termino_previsto[]"]');

    const menorData = inicios.sort()[0];
    const maiorData = fins.sort().reverse()[0];

    if (campoInicio) campoInicio.value = menorData;
    if (campoFim) campoFim.value = maiorData;
  }

  if (!temLinhaValida) {
    event.preventDefault();
    alert('Nenhuma medição válida para salvar!');
  } else {
    const inputs = form.querySelectorAll('textarea, input[type="text"], input[type="number"], input[type="date"]');
    inputs.forEach(input => {
      input.style.backgroundColor = '#e0ffe0';
      setTimeout(() => input.style.backgroundColor = '', 1000);
    });
  }
});

function addTitleRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();

  currentTitleIndex++;
  subIndex = 1;
  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'number';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = currentTitleIndex;
  idInput.style.width = '100%';
  idInput.style.fontSize = '13px';
  idInput.style.padding = '4px 8px';
  idInput.style.border = '1px solid #ccc';
  idInput.style.borderRadius = '6px';
  idInput.style.boxSizing = 'border-box';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    if (index === 0) {
      const input = document.createElement('input');
      input.type = 'text';
      input.name = campo + '[]';
      input.placeholder = 'Título';
      input.style.width = '100%';
      input.style.fontFamily = "'Poppins', sans-serif";
      input.style.fontSize = '13px';
      input.style.padding = '4px 8px';
      input.style.border = '1px solid #ccc';
      input.style.borderRadius = '6px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
    } else {
      const input = document.createElement('input');
      input.name = campo + '[]';
      input.type = campo === 'evolutivo' ? 'number' : 'date';
      if (campo === 'evolutivo') {
        input.min = 0;
        input.max = 100;
        input.step = 0.1;
        input.placeholder = '0 a 100%';
      }
      input.style.width = '100%';
      input.style.border = 'none';
      input.style.borderRadius = '6px';
      input.style.height = '20px';
      input.style.padding = '4px 8px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
    }
  });
}

function addRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();

  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'text';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = `${currentTitleIndex}.${subIndex++}`;
  idInput.style.width = '100%';
  idInput.style.fontSize = '13px';
  idInput.style.padding = '4px 8px';
  idInput.style.border = '1px solid #ccc';
  idInput.style.borderRadius = '6px';
  idInput.style.boxSizing = 'border-box';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    if (index === 0) {
      const textarea = document.createElement('textarea');
      textarea.name = campo + '[]';
      textarea.rows = 2;
      textarea.className = 'campo-etapa';
      textarea.style.width = '100%';
      textarea.style.fontFamily = "'Poppins', sans-serif";
      textarea.style.fontSize = '13px';
      textarea.style.padding = '4px 8px';
      textarea.style.border = '1px solid #ccc';
      textarea.style.borderRadius = '6px';
      textarea.style.boxSizing = 'border-box';
      textarea.style.resize = 'vertical';
      cell.appendChild(textarea);
    } else {
      const input = document.createElement('input');
      input.name = campo + '[]';
      input.type = campo === 'evolutivo' ? 'number' : 'date';
      if (campo === 'evolutivo') {
        input.min = 0;
        input.max = 100;
        input.step = 0.1;
        input.placeholder = '0 a 100%';
      }
      input.style.width = '100%';
      input.style.border = '1px solid #ccc';
      input.style.borderRadius = '6px';
      input.style.height = '20px';
      input.style.padding = '4px 8px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
    }
  });
}

function deleteRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const lastRow = table.rows[table.rows.length - 1];


  if (!lastRow) return;

  const id = lastRow.getAttribute('data-id');

  if (id) {
    fetch(`marcos_excluir_linha.php?id=${id}`, { method: 'GET' })
      .then(response => {
        if (!response.ok) throw new Error("Erro ao excluir do banco");
        return response.text();
      })
      .then(data => {
        console.log(data);
        table.deleteRow(-1);
      })
      .catch(error => {
        alert("Erro ao excluir no servidor.");
        console.error(error);
      });
  } 
  else {
    table.deleteRow(-1);
  }
}

function converterParaFloatBrasileiro(valor) {
  return valor.replace(/\./g, '').replace(',', '.');
}

function converterParaDataISO(dataBR) {
  if (!dataBR.includes('/')) return dataBR;
  const partes = dataBR.split('/');
  if (partes.length === 3) {
    return `${partes[2]}-${partes[1]}-${partes[0]}`;
  }
  return dataBR;
}