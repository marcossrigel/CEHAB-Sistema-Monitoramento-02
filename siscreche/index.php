<?php
session_start();
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
    } elseif ($page === 'visualizar') {
      echo '<link rel="stylesheet" href="assets/css/visualizar.css">';
    } elseif ($page === 'formulario') {
      echo '<link rel="stylesheet" href="assets/css/formulario.css">';
    } elseif ($page === 'editar_iniciativa') {
      echo '<link rel="stylesheet" href="assets/css/editar_iniciativa.css">';
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
      elseif ($page === 'editar_iniciativa') {
        include_once 'templates/editar_iniciativa.php';
      }
      else {
        echo "<p style='text-align:center;'>Página não encontrada.</p>";
      }
    ?>
  </main>
</body>
</html>
