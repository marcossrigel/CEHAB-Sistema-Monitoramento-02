<?php

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include_once('config.php');

$id_iniciativa = isset($_GET['id_iniciativa']) ? intval($_GET['id_iniciativa']) : 0;

$query_nome = "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa";
$result_nome = mysqli_query($conexao, $query_nome);
$linha_nome = mysqli_fetch_assoc($result_nome);
$nome_iniciativa = $linha_nome['iniciativa'] ?? 'Iniciativa Desconhecida';

if (isset($_POST['salvar'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $valor_orcamento = $_POST['valor_orcamento'] ?? [];
    $valor_bm = $_POST['valor_bm'] ?? [];
    $saldo_obra = $_POST['saldo_obra'] ?? [];
    $bm = $_POST['bm'] ?? [];
    $numero_processo_sei = $_POST['numero_processo_sei'] ?? [];
    $data_inicio = $_POST['data_inicio'] ?? [];
    $data_fim = $_POST['data_fim'] ?? [];
    $ids = $_POST['ids'] ?? [];

    function limparDinheiro($valor) {
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return is_numeric($valor) ? $valor : 0;
    }

    for ($i = 0; $i < count($valor_orcamento); $i++) {
        $id_existente = intval($ids[$i] ?? 0);
        $orc = limparDinheiro($valor_orcamento[$i]);
        $bm_valor = limparDinheiro($valor_bm[$i]);
        $saldo = limparDinheiro($saldo_obra[$i]);
        $bm_ind = intval(limparDinheiro($bm[$i]));
        $sei = mysqli_real_escape_string($conexao, $numero_processo_sei[$i] ?? '');
        $inicio = $data_inicio[$i] ?? null;
        $fim = $data_fim[$i] ?? null;
        $registro = date('Y-m-d H:i:s');

        if ($id_existente > 0) {
            $sql = "UPDATE medicoes 
        SET valor_orcamento='$orc', valor_bm='$bm_valor', saldo_obra='$saldo', bm='$bm_ind', 
            data_inicio='$inicio', data_fim='$fim', numero_processo_sei='$sei' 
        WHERE id=$id_existente AND id_usuario=$id_usuario";
        } else {
            $sql = "INSERT INTO medicoes (id_usuario, id_iniciativa, valor_orcamento, valor_bm, saldo_obra, bm, data_inicio, data_fim, data_registro, numero_processo_sei)
        VALUES ($id_usuario, $id_iniciativa, '$orc', '$bm_valor', '$saldo', '$bm_ind', '$inicio', '$fim', '$registro', '$sei')";
        }
        mysqli_query($conexao, $sql);
    }
}

if (!empty($_POST['excluir_ids'])) {
    foreach ($_POST['excluir_ids'] as $id_excluir) {
        $id_excluir = intval($id_excluir);
        $sql = "DELETE FROM medicoes WHERE id = $id_excluir AND id_usuario = {$_SESSION['id_usuario']}";
        mysqli_query($conexao, $sql);
    }
}

$dados = mysqli_query($conexao, "SELECT * FROM medicoes WHERE id_usuario = {$_SESSION['id_usuario']} AND id_iniciativa = $id_iniciativa");
function formatarParaBrasileiro($valor) {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
?>

<div class="container">
    <h2><?php echo htmlspecialchars($nome_iniciativa); ?> - Acompanhamento de Medidas</h2>

    <form method="post" action="medicoes.php?id_iniciativa=<?php echo $id_iniciativa; ?>">
        <div class="table-wrapper">
        <table id="medicoes">
            <thead>
                <tr>
                    <th>Valor Total da Obra</th>
                    <th>Valor BM</th>
                    <th>Saldo da Obra</th>
                    <th>BM</th>
                    <th>Nº Processo SEI</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                </tr>
            </thead>
            
            <tbody>
            <?php $linha_index = 0; while ($linha = mysqli_fetch_assoc($dados)) { ?>
                <tr data-id="<?php echo $linha['id']; ?>">
                <input type="hidden" name="ids[]" value="<?php echo $linha['id']; ?>">
                <td>
                    <input
                    type="text"
                    name="valor_orcamento[]"
                    value="<?php echo formatarParaBrasileiro($linha['valor_orcamento']); ?>"
                    <?php echo $linha_index === 0 ? 'required' : ''; ?>
                    />

                </td>
                <td><input type="text" name="valor_bm[]" value="<?php echo formatarParaBrasileiro($linha['valor_bm']); ?>" required /></td>
                <td><input type="text" name="saldo_obra[]" value="<?php echo formatarParaBrasileiro($linha['saldo_obra']); ?>" /></td>
                
                <td><input type="text" name="bm[]" value="<?php echo intval($linha['bm']); ?>" /></td> 
                <td><input type="text" name="numero_processo_sei[]" value="<?php echo htmlspecialchars($linha['numero_processo_sei'] ?? ''); ?>" /></td>

                <td><input type="date" name="data_inicio[]" value="<?php echo htmlspecialchars($linha['data_inicio']); ?>" /></td>
                <td><input type="date" name="data_fim[]" value="<?php echo htmlspecialchars($linha['data_fim']); ?>" /></td>
                </tr>
            <?php $linha_index++; } ?>
            </tbody>

        </table>
        <div class="buttons">
            <button type="button" onclick="adicionarLinha()">Adicionar Linha</button>
            <button type="button" onclick="removerLinha()">Excluir Linha</button>
            <button type="submit" name="salvar">Salvar</button>
            <button type="button" onclick="window.location.href='index.php?page=visualizar';">Voltar</button>

        </div>
    </form>
</div>
</div>
<script>

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
    { name: 'bm[]', type: 'number', step: '1' },
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

</script>
