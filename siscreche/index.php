<?php
require_once("templates/config.php");

# $token = $_GET["access_dinamic"];

/* $query = "SELECT * FROM token_sessao WHERE token = ?";
$stmt = mysqli_prepare($conexao2, $query);
mysqli_stmt_bind_param($stmt, "ss", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$id_user = $result["id_user"];

$query = "SELECT * FROM users WHERE g_id = ?";
$stmt = mysqli_prepare($conexao2, $query);
mysqli_stmt_bind_param($stmt, "ss", $id_user);
mysqli_stmt_execute($stmt);
$result2 = mysqli_stmt_get_result($stmt); */

session_start();

/* $_SESSION["id_user"] = $result2["g_id"];
$_SESSION["username"] = $result2["u_nome_completo"];
$_SESSION["setor"] = $result2["setor_local"]; */

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$page = $_GET['page'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Monitoramento</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <?php
    if ($page === 'home') {
      echo '<link rel="stylesheet" href="assets/css/home.css">';
    }
    elseif ($page === 'visualizar') {
      echo '<link rel="stylesheet" href="assets/css/visualizar.css">';
    }
    elseif ($page === 'diretorias') {
      echo '<link rel="stylesheet" href="assets/css/diretorias.css">';
    } 
    elseif ($page === 'formulario') {
      echo '<link rel="stylesheet" href="assets/css/formulario.css">';
    } 
    elseif ($page === 'editar_iniciativa') {
      echo '<link rel="stylesheet" href="assets/css/editar_iniciativa.css">';
    } 
    elseif ($page === 'acompanhamento') {
      echo '<link rel="stylesheet" href="assets/css/acompanhamento.css">';
    } 
    elseif ($page === 'info_contratuais') {
      echo '<link rel="stylesheet" href="assets/css/info_contratuais.css">';
    }
    elseif ($page === 'medicoes') {
      echo '<link rel="stylesheet" href="assets/css/medicoes.css">';
    }
  ?>

</head>
<body style="height: 100vh; display: flex; flex-direction: column;">
  <main>
    <?php
      if ($page === 'home') {
        include_once 'templates/home.php';
      } 
      elseif ($page === 'formulario') {
        include_once 'templates/formulario.php';
      }
      elseif ($page === 'visualizar') {
        include_once 'templates/visualizar.php';
      }
      elseif ($page === 'diretorias') {
        include_once 'templates/diretorias.php';
      }
      elseif ($page === 'editar_iniciativa') {
        include_once 'templates/editar_iniciativa.php';
      }
      elseif ($page === 'acompanhamento') {
        include_once 'templates/acompanhamento.php';
      }
      elseif ($page === 'info_contratuais') {
        include_once 'templates/info_contratuais.php';
      }
      elseif ($page === 'medicoes') {
        include_once 'templates/medicoes.php';
      }
      elseif ($page === 'cronogramamarcos') {
        include_once 'templates/cronogramamarcos.php';
      }
      else {
        echo "<p style='text-align:center;'>Página não encontrada.</p>";
      }
    ?>
  </main>
  
</body>
</html>
