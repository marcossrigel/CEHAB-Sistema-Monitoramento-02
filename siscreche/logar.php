<?php
session_start();
require_once('templates/config.php');

// Validação básica do POST
if (!isset($_POST['nome'], $_POST['senha'])) {
    die("Requisição inválida.");
}

$login = trim($_POST['nome']);
$senha = trim($_POST['senha']);

// 1. Verifica o usuário na base local
$query = "SELECT * FROM usuarios WHERE nome = ? AND senha = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "ss", $login, $senha);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    echo "<script>alert('Erro: Usuário ou senha inválidos'); window.location.href = 'login.php';</script>";
    exit;
}

$usuario = mysqli_fetch_assoc($result);
$g_id = $usuario['id_usuario_cehab_online'];

// 2. Se o usuário tiver um ID CEHAB Online, tenta login por token
if (!empty($g_id)) {
    $queryToken = "SELECT token FROM token_sessao WHERE g_id = ? ORDER BY data_hora DESC LIMIT 1";
    $stmt2 = mysqli_prepare($conexao2, $queryToken);
    mysqli_stmt_bind_param($stmt2, "i", $g_id);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    $tokenData = mysqli_fetch_assoc($result2);

    if ($tokenData && isset($tokenData['token'])) {
        $token = $tokenData['token'];
        $page = ($usuario['tipo'] === 'admin') ? 'diretorias' : 'home';
        header("Location: index.php?access_dinamic={$token}&page={$page}");
        exit;
    } else {
        echo "<script>alert('Token não encontrado para este usuário.'); window.location.href = 'login.php';</script>";
        exit;
    }
}

// 3. Login tradicional (sem token)
$_SESSION['id_usuario']   = $usuario['id_usuario'];
$_SESSION['nome']         = $usuario['nome'];
$_SESSION['tipo_usuario'] = $usuario['tipo'];
$_SESSION['diretoria']    = $usuario['diretoria'];

// Redireciona com base no tipo
if ($usuario['tipo'] === 'admin') {
    header("Location: index.php?page=diretorias");
} else {
    header("Location: index.php?page=home");
}
exit;
?>
