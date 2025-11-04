<?php
session_start();
require_once("templates/config.php");

if (isset($_SESSION['id_usuario'])) {
    $page = isset($_GET['page']) ? $_GET['page'] : null;

    if (!$page) {
        if ($_SESSION["tipo_usuario"] === "admin") {
            header("Location: index.php?page=diretorias");
        } else {
            header("Location: index.php?page=home");
        }
        exit;
    }
}

elseif (isset($_GET["access_dinamic"])) {
    session_unset();
    session_destroy();
    session_start();

    $token = $_GET["access_dinamic"];

    $query = "SELECT g_id FROM token_sessao WHERE token = ?";
    $stmt = mysqli_prepare($conexao2, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Token inválido ou expirado.");
    }

    $g_id = $row["g_id"];

    $queryUser = "SELECT u_nome_completo FROM users WHERE g_id = ?";
    $stmtUser = mysqli_prepare($conexao2, $queryUser);
    mysqli_stmt_bind_param($stmtUser, "i", $g_id);
    mysqli_stmt_execute($stmtUser);
    $resultUser = mysqli_stmt_get_result($stmtUser);
    $userData = mysqli_fetch_assoc($resultUser);

    $queryLocal = "SELECT * FROM usuarios WHERE id_usuario_cehab_online = ?";
    $stmtLocal = mysqli_prepare($conexao, $queryLocal);
    mysqli_stmt_bind_param($stmtLocal, "i", $g_id);
    mysqli_stmt_execute($stmtLocal);
    $resultLocal = mysqli_stmt_get_result($stmtLocal);
    $usuarioLocal = mysqli_fetch_assoc($resultLocal);

    if (!$usuarioLocal || empty($usuarioLocal['id_usuario_cehab_online'])) {
      header("Location: templates/solicitar_usuario.php?nome=" . urlencode($userData['u_nome_completo']) . "&g_id=" . $g_id);
      exit;
    }

    $_SESSION["id_usuario"]   = $usuarioLocal["id_usuario"];
    $_SESSION["nome"]         = $userData["u_nome_completo"];
    $_SESSION["tipo_usuario"] = $usuarioLocal["tipo"];
    $_SESSION["diretoria"]    = $usuarioLocal["diretoria"];

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
        'cronogramamarcos' => 'formulario.css',
        'solicitar_usuario' => 'formulario.css',
        'compartilhar' => 'formulario.css',
        'remover_compartilhamento' => 'formulario.css',
        'salvar_compartilhamento' => 'formulario.css',
        'projeto_licitacoes' => 'medicoes.css',
        'deletar_linha' => null,
        'excluir_linha' => null,
        'marcos_excluir_linha' => null,
        'excluir_linha_medicoes' => null,
        'excluir_pendencia' => null,
        'deletar_linha' => null,
        'marcar_concluida' => null
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

