<?php
require_once("templates/config.php");
session_start();

// VERIFICAÇÃO POR SESSÃO (login tradicional)
if (isset($_SESSION['id_usuario'])) {
    // Sessão já existe, verifica se 'page' foi passada
    $page = $_GET['page'] ?? null;

    if (!$page) {
        if ($_SESSION["tipo_usuario"] === "admin") {
            header("Location: index.php?page=diretorias");
        } else {
            header("Location: index.php?page=home");
        }
        exit;
    }
} 
// VERIFICAÇÃO POR TOKEN NA URL
elseif (isset($_GET["access_dinamic"])) {
    $token = $_GET["access_dinamic"];

    // 1. Busca o token na tabela cehab_online.token_sessao
    $query = "SELECT * FROM token_sessao WHERE token = ?";
    $stmt = mysqli_prepare($conexao2, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Token inválido ou expirado.");
    }

    $g_id = $row["g_id"];

    // 2. Busca o usuário na tabela cehab_online.users
    $query2 = "SELECT * FROM users WHERE g_id = ?";
    $stmt2 = mysqli_prepare($conexao2, $query2);
    mysqli_stmt_bind_param($stmt2, "i", $g_id);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    $row2 = mysqli_fetch_assoc($result2);

    // 3. Busca o usuário correspondente na base local siscreche.usuarios
    $query_local = "SELECT * FROM usuarios WHERE id_usuario_cehab_online = ?";
    $stmt_local = mysqli_prepare($conexao, $query_local);
    mysqli_stmt_bind_param($stmt_local, "i", $g_id);
    mysqli_stmt_execute($stmt_local);
    $result_local = mysqli_stmt_get_result($stmt_local);
    $usuario_local = mysqli_fetch_assoc($result_local);

    if (!$usuario_local) {
        die("Usuário não encontrado na base local.");
    }

    // 4. Define sessão
    $_SESSION["id_usuario"]   = $usuario_local["id_usuario"];
    $_SESSION["nome"]         = $usuario_local["nome"];
    $_SESSION["tipo_usuario"] = $usuario_local["tipo"];
    $_SESSION["diretoria"]    = $usuario_local["diretoria"];

    // Redirecionamento após login com token
    if (!isset($_GET['page'])) {
        if ($_SESSION["tipo_usuario"] === "admin") {
            header("Location: index.php?page=diretorias");
        } else {
            header("Location: index.php?page=home");
        }
        exit;
    }

    $page = $_GET['page'];
} 
// SEM LOGIN NEM TOKEN
else {
    die("Token de acesso não fornecido e nenhuma sessão ativa.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Monitoramento</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <?php
    $cssMap = [
        'home' => 'home.css',
        'visualizar' => 'visualizar.css',
        'diretorias' => 'diretorias.css',
        'formulario' => 'formulario.css',
        'editar_iniciativa' => 'editar_iniciativa.css',
        'acompanhamento' => 'acompanhamento.css',
        'info_contratuais' => 'info_contratuais.css',
        'medicoes' => 'medicoes.css',
        'cronogramamarcos' => 'cronogramamarcos.css'
    ];
    if (isset($cssMap[$page])) {
        echo '<link rel="stylesheet" href="assets/css/' . $cssMap[$page] . '">';
    }
  ?>
</head>
<body style="height: 100vh; display: flex; flex-direction: column;">
  <main>
    <?php
      $allowedPages = array_keys($cssMap);
      if (in_array($page, $allowedPages)) {
        include_once 'templates/' . $page . '.php';
      } else {
        echo "<p style='text-align:center;'>Página não encontrada.</p>";
      }
    ?>
  </main>
</body>
</html>
