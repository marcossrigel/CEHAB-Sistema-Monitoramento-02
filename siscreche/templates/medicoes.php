<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

    $query_verificacao = "
    SELECT 1 FROM iniciativas 
    WHERE id = $id_iniciativa AND (
        id_usuario = $id_usuario OR 
        $id_usuario IN (
            SELECT id_compartilhado FROM compartilhamentos 
            WHERE id_iniciativa = $id_iniciativa
        )
    )";
    $result_verificacao = mysqli_query($conexao, $query_verificacao);
    if (mysqli_num_rows($result_verificacao) === 0) {
        echo "Você não tem permissão para acessar esta iniciativa.";
        exit;
    }

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
        $bm_ind = mysqli_real_escape_string($conexao, $bm[$i] ?? '');
        $sei = mysqli_real_escape_string($conexao, $numero_processo_sei[$i] ?? '');
        $inicio = !empty($data_inicio[$i]) ? "'{$data_inicio[$i]}'" : "NULL";
        $fim = !empty($data_fim[$i]) ? "'{$data_fim[$i]}'" : "NULL";
        $registro = date('Y-m-d H:i:s');

        if ($id_existente > 0) {
            $sql = "UPDATE medicoes 
        SET valor_orcamento='$orc', valor_bm='$bm_valor', saldo_obra='$saldo', bm='$bm_ind', 
            data_inicio=$inicio, data_fim=$fim, numero_processo_sei='$sei' 
       WHERE id=$id_existente AND id_iniciativa=$id_iniciativa";
        } else {
            $sql = "INSERT INTO medicoes (id_usuario, id_iniciativa, valor_orcamento, valor_bm, saldo_obra, bm, data_inicio, data_fim, data_registro, numero_processo_sei)
        VALUES ($id_usuario, $id_iniciativa, '$orc', '$bm_valor', '$saldo', '$bm_ind', $inicio, $fim, '$registro', '$sei')";
        }
        mysqli_query($conexao, $sql);
    }
}

if (!empty($_POST['excluir_ids'])) {
    foreach ($_POST['excluir_ids'] as $id_excluir) {
        $id_excluir = intval($id_excluir);
        $sql = "DELETE FROM medicoes WHERE id = $id_excluir AND id_iniciativa = $id_iniciativa";
        mysqli_query($conexao, $sql);
    }
}

$dados = mysqli_query($conexao, "SELECT * FROM medicoes WHERE id_iniciativa = $id_iniciativa ORDER BY data_inicio");

function formatarParaBrasileiro($valor) {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
?>

<div class="container">
    <h2><?php echo htmlspecialchars($nome_iniciativa); ?> - Acompanhamento de Medidas</h2>

    <form method="post" action="index.php?page=medicoes&id_iniciativa=<?php echo $id_iniciativa; ?>">
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
                
                <td><input type="text" name="bm[]" value="<?php echo htmlspecialchars($linha['bm']); ?>" /></td>
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
            <button type="button" onclick="window.location.href='index.php?page=visualizar';">&lt; Voltar</button>
        </div>
    </form>
</div>
</div>
<script src="js/medicoes.js"></script>
