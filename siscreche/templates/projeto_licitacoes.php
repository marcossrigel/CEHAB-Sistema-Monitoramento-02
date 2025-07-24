<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('config.php');
mysqli_set_charset($conexao, "utf8mb4");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_iniciativa = $_GET['id_iniciativa'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa'])) {
    $ids = $_POST['ids'] ?? [];
    $ordens = $_POST['ordem'] ?? [];
    $etapas = $_POST['etapa'] ?? [];
    $responsaveis = $_POST['responsavel'] ?? [];
    $inicio_previsto = $_POST['inicio_previsto'] ?? [];
    $termino_previsto = $_POST['termino_previsto'] ?? [];
    $inicio_real = $_POST['inicio_real'] ?? [];
    $termino_real = $_POST['termino_real'] ?? [];
    $status = $_POST['status'] ?? [];
    $observacoes = $_POST['observacao'] ?? [];

    for ($i = 0; $i < count($etapas); $i++) {
        $id = $ids[$i] ?? null;

        $ordem = !empty($ordens[$i]) ? "'" . mysqli_real_escape_string($conexao, $ordens[$i]) . "'" : "NULL";
        $etapa = !empty($etapas[$i]) ? "'" . mysqli_real_escape_string($conexao, $etapas[$i]) . "'" : "NULL";
        $responsavel = !empty($responsaveis[$i]) ? "'" . mysqli_real_escape_string($conexao, $responsaveis[$i]) . "'" : "NULL";
        $status_val = !empty($status[$i]) ? "'" . mysqli_real_escape_string($conexao, $status[$i]) . "'" : "NULL";
        $obs = !empty($observacoes[$i]) ? "'" . mysqli_real_escape_string($conexao, $observacoes[$i]) . "'" : "NULL";

        $prev_inicio = !empty($inicio_previsto[$i]) ? "'" . mysqli_real_escape_string($conexao, $inicio_previsto[$i]) . "'" : "NULL";
        $prev_fim = !empty($termino_previsto[$i]) ? "'" . mysqli_real_escape_string($conexao, $termino_previsto[$i]) . "'" : "NULL";
        $real_inicio = !empty($inicio_real[$i]) ? "'" . mysqli_real_escape_string($conexao, $inicio_real[$i]) . "'" : "NULL";
        $real_fim = !empty($termino_real[$i]) ? "'" . mysqli_real_escape_string($conexao, $termino_real[$i]) . "'" : "NULL";


        if ($id) {
            $sql = "UPDATE projeto_licitacoes SET 
            ordem = $ordem,
            etapa = $etapa,
            responsavel = $responsavel,
            inicio_previsto = $prev_inicio,
            termino_previsto = $prev_fim,
            inicio_real = $real_inicio,
            termino_real = $real_fim,
            status = $status_val,
            observacao = $obs
            WHERE id = $id";
        } else {
            $sql = "INSERT INTO projeto_licitacoes (
            id_iniciativa, ordem, etapa, responsavel,
            inicio_previsto, termino_previsto,
            inicio_real, termino_real, status, observacao
        ) VALUES (
            $id_iniciativa, $ordem, $etapa, $responsavel,
            $prev_inicio, $prev_fim,
            $real_inicio, $real_fim, $status_val, $obs
        )";

        }

        mysqli_query($conexao, $sql);
    }

    header("Location: index.php?page=projeto_licitacoes&id_iniciativa=$id_iniciativa");
    exit;
}

$sql_iniciativa = "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa";
$res_iniciativa = mysqli_query($conexao, $sql_iniciativa);
$nome_iniciativa = '';

if ($res_iniciativa && $row = mysqli_fetch_assoc($res_iniciativa)) {
    $nome_iniciativa = $row['iniciativa'];
} else {
    $nome_iniciativa = "Iniciativa Desconhecida";
}

$sql = "SELECT * FROM projeto_licitacoes WHERE id_iniciativa = $id_iniciativa";
$dados = mysqli_query($conexao, $sql);
?>

<div class="container">
    <h2>Projeto <?php echo htmlspecialchars($nome_iniciativa); ?></h2>

    <form method="post" action="index.php?page=projeto_licitacoes&id_iniciativa=<?php echo $id_iniciativa; ?>">
        <div class="table-wrapper">
        <table id="medicoes">
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Etapa</th>
                    <th>Responsável</th>
                    <th>Início Previsto</th>
                    <th>Término Previsto</th>
                    <th>Início Real</th>
                    <th>Término Real</th>
                    <th>Status</th>
                    <th>Observação</th>
                </tr>
            </thead>
            
            <tbody>
            <?php while ($linha = mysqli_fetch_assoc($dados)) { ?>
                <tr data-id="<?php echo $linha['id']; ?>">
                    <td>
                        <input type="hidden" name="ids[]" value="<?= htmlspecialchars($linha['id']) ?>">
                        <input type="text" name="ordem[]" value="<?= htmlspecialchars($linha['ordem'] ?? '') ?>">
                    </td>
                    <td><input type="text" name="etapa[]" value="<?= htmlspecialchars($linha['etapa'] ?? '') ?>"></td>
                    <td><input type="text" name="responsavel[]" value="<?= htmlspecialchars($linha['responsavel'] ?? '') ?>"></td>
                    <td><input type="date" name="inicio_previsto[]" value="<?= htmlspecialchars($linha['inicio_previsto'] ?? '') ?>"></td>
                    <td><input type="date" name="termino_previsto[]" value="<?= htmlspecialchars($linha['termino_previsto'] ?? '') ?>"></td>
                    <td><input type="date" name="inicio_real[]" value="<?= htmlspecialchars($linha['inicio_real'] ?? '') ?>"></td>
                    <td><input type="date" name="termino_real[]" value="<?= htmlspecialchars($linha['termino_real'] ?? '') ?>"></td>
                    <td><input type="text" name="status[]" value="<?= htmlspecialchars($linha['status'] ?? '') ?>"></td>
                    <td><input type="text" name="observacao[]" value="<?= htmlspecialchars($linha['observacao'] ?? '') ?>"></td>
                </tr>
            <?php } ?>
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
<script src="js/projeto_licitacoes.js"></script>
