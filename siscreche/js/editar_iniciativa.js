  function abrirModal() {
    document.getElementById('modalConfirmacao').style.display = 'flex';
  }
  function fecharModal() {
    document.getElementById('modalConfirmacao').style.display = 'none';
  }
  function confirmarExclusao() {
    window.location.href = 'excluir_iniciativa.php?id=<?php echo $row["id"]; ?>';
  }