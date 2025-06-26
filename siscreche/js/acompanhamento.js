document.querySelector('form').addEventListener('submit', function(event) {
  const form = this;
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const linhas = table.rows;
  let temLinhaValida = false;

  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];
    const id = linha.getAttribute('data-id');
    const cells = linha.cells;

    let problema = cells[0].innerText.trim();
    let contramedida = cells[1].innerText.trim();
    let prazo = cells[2].innerText.trim();
    let responsavel = cells[3].innerText.trim();

    if (prazo.includes('/')) {
      const partes = prazo.split('/');
      if (partes.length === 3) {
        prazo = `${partes[2]}-${partes[1]}-${partes[0]}`;
      }
    }

    const campos = [problema, contramedida, prazo, responsavel];
    const linhaVazia = campos.every(c => c === '');

    if (linhaVazia) continue;
    temLinhaValida = true;

    ['problema', 'contramedida', 'prazo', 'responsavel'].forEach((campo, idx) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = campo + '[]';
      input.value = campos[idx];
      form.appendChild(input);
    });

    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'ids[]';
    inputId.value = id ? id : '';
    form.appendChild(inputId);
  }

  if (!temLinhaValida) {
    event.preventDefault();
    alert('Nenhuma pendência válida para salvar!');
  }
});

function addRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();
  const placeholders = ['Problema', 'Contramedida', 'dd/mm/aaaa', 'Responsável'];

  for (let i = 0; i < 4; i++) {
    const newCell = newRow.insertCell();
    newCell.contentEditable = "true";
    newCell.innerText = '';
    newCell.setAttribute('data-placeholder', placeholders[i]);
  }
}

function deleteRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  if (table.rows.length > 0) {
    const lastRow = table.rows[table.rows.length - 1];
    const id = lastRow.getAttribute('data-id');

    if (id) {
      fetch('excluir_pendencia.php?id=' + id, { method: 'GET' })
        .then(response => response.text())
        .then(data => {
          table.deleteRow(-1);
        });
    } else {
      table.deleteRow(-1);
    }
  }
}