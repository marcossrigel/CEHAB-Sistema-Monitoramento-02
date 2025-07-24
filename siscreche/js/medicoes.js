document.addEventListener('input', function(e) {
    if (e.target.name === 'valor_bm[]') {
        recalcularSaldos();
    }
});

document.querySelector("form").addEventListener("submit", function(e) {
const orcamentos = document.querySelectorAll('input[name="valor_orcamento[]"]');
const bms = document.querySelectorAll('input[name="valor_bm[]"]');
let valido = true;

for (let i = 0; i < orcamentos.length; i++) {
    if (orcamentos[i].value.trim() === "" || bms[i].value.trim() === "") {
        valido = false;
        break;
    }
}

if (!valido) {
    alert("Os campos 'Valor Total da Obra' e 'Valor BM' são obrigatórios.");
    e.preventDefault();
}
});

function adicionarLinha() {
const table = document.getElementById('medicoes').getElementsByTagName('tbody')[0];
const newRow = table.insertRow();

const primeiraLinha = table.rows[0];
const valorOrcamentoOriginal = primeiraLinha?.cells[0]?.querySelector('input')?.value || '';
const campos = [
    { name: 'valor_orcamento[]', type: 'text', required: true, value: valorOrcamentoOriginal },
    { name: 'valor_bm[]', type: 'text', required: true },
    { name: 'saldo_obra[]', type: 'text' },
    { name: 'bm[]', type: 'text' },
    { name: 'numero_processo_sei[]', type: 'text' },
    { name: 'data_inicio[]', type: 'date' },
    { name: 'data_fim[]', type: 'date' }
];

campos.forEach(campo => {
    const cell = newRow.insertCell();
    const input = document.createElement('input');
    input.type = campo.type;
    input.name = campo.name;
    if (campo.required) input.required = true;
    if (campo.value !== undefined) input.value = campo.value;
    if (campo.step) input.step = campo.step;
    cell.appendChild(input);
});
}


function removerLinha() {
    const tabela = document.getElementById('medicoes').getElementsByTagName('tbody')[0];
    const ultimaLinha = tabela.rows[tabela.rows.length - 1];

    if (!ultimaLinha) return;

    const id = ultimaLinha.getAttribute('data-id');

    if (id) {
        fetch('excluir_linha_medicoes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(response => {
            if (response.ok) {
                tabela.deleteRow(-1);
            } else {
                alert('Erro ao excluir no banco de dados.');
            }
        })
        .catch(() => alert('Erro de conexão ao tentar excluir.'));
    } else {
        tabela.deleteRow(-1);
    }
}

function formatarDinheiroParaFloat(valor) {
    return parseFloat(valor.replace(/[R$\s.]/g, '').replace(',', '.')) || 0;
}

function formatarFloatParaDinheiro(valor) {
    return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function formatarFloatParaInteiro(valor) {
    return Math.round(valor).toString();
}

function recalcularSaldos() {
    const orcamentos = document.querySelectorAll('input[name="valor_orcamento[]"]');
    const bms = document.querySelectorAll('input[name="valor_bm[]"]');
    const saldos = document.querySelectorAll('input[name="saldo_obra[]"]');

    const totalObra = formatarDinheiroParaFloat(orcamentos[0].value);
    let acumuladoBM = 0;

    for (let i = 0; i < bms.length; i++) {
        const valorBM = formatarDinheiroParaFloat(bms[i].value);
        acumuladoBM += valorBM;
        const saldo = totalObra - acumuladoBM;
        saldos[i].value = formatarFloatParaDinheiro(saldo);
    }
}