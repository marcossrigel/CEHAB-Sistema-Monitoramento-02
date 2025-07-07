<?php
session_start();
require_once('templates/config.php');

if (!isset($_POST['nome'], $_POST['senha'])) {
    die("Requisição inválida.");
}

$login = trim($_POST['nome']);
$senha = trim($_POST['senha']);

// Consulta só pelo login
$query = "SELECT * FROM users WHERE u_rede = ?";
$stmt = mysqli_prepare($conexao2, $query);
mysqli_stmt_bind_param($stmt, "s", $login);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$usuarioOnline = mysqli_fetch_assoc($result);

// Verifica se encontrou e se a senha está correta
if (!$usuarioOnline || !password_verify($senha, $usuarioOnline['u_password'])) {
    echo "<script>alert('Usuário ou senha inválidos'); window.location.href = 'login.php';</script>";
    exit;
}

$g_id = $usuarioOnline['g_id'];

// Busca o usuário local pelo g_id
$queryLocal = "SELECT * FROM usuarios WHERE id_usuario_cehab_online = ?";
$stmtLocal = mysqli_prepare($conexao, $queryLocal);
mysqli_stmt_bind_param($stmtLocal, "i", $g_id);
mysqli_stmt_execute($stmtLocal);
$resultLocal = mysqli_stmt_get_result($stmtLocal);
$usuarioLocal = mysqli_fetch_assoc($resultLocal);

if (!$usuarioLocal) {
    echo "<script>alert('Usuário não encontrado no sistema de monitoramento.'); window.location.href = 'login.php';</script>";
    exit;
}

// Inicia sessão
$_SESSION['id_usuario']   = $usuarioLocal['id_usuario'];
$_SESSION['g_id']         = $g_id;
$_SESSION['tipo_usuario'] = $usuarioLocal['tipo'];
$_SESSION['diretoria']    = $usuarioLocal['diretoria'];

$page = ($usuarioLocal['tipo'] === 'admin') ? 'diretorias' : 'home';
header("Location: index.php?page=$page");
exit;
?>
