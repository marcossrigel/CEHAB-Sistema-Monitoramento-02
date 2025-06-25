<?php

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<div class="cards-container">

  <a href="visualizar.php?diretoria=Educacao" class="card-link">
    <div class="card-conteudo">Educação</div>
  </a>

  <a href="visualizar.php?diretoria=Saude" class="card-link">
    <div class="card-conteudo">Saúde</div>
  </a>

  <a href="visualizar.php?diretoria=Infra Estratégicas" class="card-link">
    <div class="card-conteudo">
      <div class="card-titulo">
        <div>Infra</div>
        <div>Estratégicas</div>
      </div>
    </div>
  </a>

  <a href="visualizar.php?diretoria=Infra Grandes Obras" class="card-link">
    <div class="card-conteudo">
      <div class="card-titulo">
        <div>Infra</div>
        <div>Grandes Obras</div>
      </div>
    </div>
  </a>

  <a href="visualizar.php?diretoria=Seguranca" class="card-link">
    <div class="card-conteudo">Segurança</div>
  </a>

  <a href="visualizar.php?diretoria=Social" class="card-link">
    <div class="card-conteudo">Social</div>
  </a>

</div>

<div class="botao-sair">
  <a href="login.php">Sair</a>
</div>
