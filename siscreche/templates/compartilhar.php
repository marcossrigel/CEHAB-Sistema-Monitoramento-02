<form method="POST" action="salvar_compartilhamento.php">
  <label>Compartilhar com:</label>
  <select name="id_compartilhado">
    <?php
    $usuarios = $conexao->query("SELECT id_usuario, nome FROM usuarios WHERE id_usuario != $id_usuario");
    while ($u = $usuarios->fetch_assoc()) {
        echo "<option value='{$u['id_usuario']}'>{$u['nome']}</option>";
    }
    ?>
  </select>
  <button type="submit">Compartilhar</button>
</form>
