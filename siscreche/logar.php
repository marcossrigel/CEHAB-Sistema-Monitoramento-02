<?php
session_start();
include_once('templates/config.php');

$login = $_POST['nome'];
$senha = $_POST['senha'];

$query = "SELECT * FROM usuarios WHERE nome = ? AND senha = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "ss", $login, $senha);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $usuario = mysqli_fetch_assoc($result);

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo_usuario'] = $usuario['tipo'];

    $baseURL = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ? '' : 'https://creches.getic.pe.gov.br/';

    if ($usuario['tipo'] === 'admin') {
        header("Location: {$baseURL}index.php?page=diretorias");
    } else {
        header("Location: {$baseURL}index.php?page=home");
    }
    exit;
} else {
    echo "<script>alert('Erro: Usuário ou Senha inválidos'); window.location.href = 'login.php';</script>";
}
