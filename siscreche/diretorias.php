<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secretarias</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #e9eef1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .cards-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 40px;
    }

    .card-link {
      text-decoration: none;
    }

    .card-titulo {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      line-height: 1.4;
    }

    .card-conteudo {
      width: 200px;
      height: 200px;
      background-color: #ffffff;
      border-radius: 12px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 22px;
      font-weight: bold;
      color: #1d2129;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
      cursor: pointer;
    }

    .card-conteudo:hover {
      transform: scale(1.02);
    }

    .botao-sair {
      text-align: center;
    }

    .botao-sair a {
      background-color: #ff4d4d;
      color: white;
      padding: 12px 25px;
      text-decoration: none;
      font-weight: bold;
      font-size: 16px;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .botao-sair a:hover {
      background-color: #e60000;
    }

    @media (max-width: 600px) {
      .card-conteudo {
        width: 160px;
        height: 160px;
        font-size: 18px;
      }
    }
  </style>
</head>

<body>

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

</body>
</html>
