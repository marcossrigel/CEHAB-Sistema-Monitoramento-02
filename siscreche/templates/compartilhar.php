<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include("config.php");

$id_usuario = $_SESSION['id_usuario'];

$sql_iniciativas = "SELECT id, iniciativa FROM iniciativas WHERE id_usuario = $id_usuario";

$res_iniciativas = $conexao->query($sql_iniciativas);

$sql_compartilhados = "
    SELECT DISTINCT u.nome_usuario 
    FROM compartilhamentos c 
    JOIN usuarios u ON c.id_compartilhado = u.id_usuario 
    WHERE c.id_dono = $id_usuario
";
$res_compartilhados = $conexao->query($sql_compartilhados);
?>

<style>
.icone-usuario {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  vertical-align: middle;
  margin-right: 8px;
}
.lista-compartilhados li {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}
</style>

<div class="container">
    <h2>Compartilhar Iniciativas</h2>

    <form action="siscreche/processa_compartilhamento.php" method="post">
        <label for="usuario">Nome do Usuario (REDE):</label>
        <input type="text" name="usuario" id="usuario" placeholder="Digite o nome do usuário da rede" required>

        <h3>Selecione as iniciativas a compartilhar:</h3>
        <?php if ($res_iniciativas->num_rows > 0): ?>
            <?php while ($linha = $res_iniciativas->fetch_assoc()): ?>
                <div>
                    <input type="checkbox" name="iniciativas[]" value="<?php echo $linha['id']; ?>" id="inic<?php echo $linha['id']; ?>">
                    <label for="inic<?php echo $linha['id']; ?>"><?php echo htmlspecialchars($linha['iniciativa']); ?></label>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Você não possui iniciativas para compartilhar.</p>
        <?php endif; ?>

        <br>
        <button type="submit" class="btn-verde">Confirmar Compartilhamento</button>
        <a href="index.php?page=visualizar" class="btn-vermelho">Cancelar</a>
    </form>

    <hr>

    <h3>Já Compartilhado com:</h3>
    <ul class="lista-compartilhados">
        <?php if ($res_compartilhados->num_rows > 0): ?>
            <?php while ($linha = $res_compartilhados->fetch_assoc()): ?>
                <li>
                    <img src="perfil.png" alt="Foto de perfil" class="icone-usuario">
                    <?php echo htmlspecialchars($linha['nome_usuario']); ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum usuário ainda.</p>
        <?php endif; ?>
    </ul>
</div>
