<?php
session_start();
include("config.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_foto = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id_usuario'];

// Verifica se a foto pertence ao usuário logado
$busca = $conexao->query("SELECT caminho FROM fotos WHERE id = $id_foto AND id_usuario = $id_usuario");
if ($busca->num_rows === 0) {
    echo "Foto não encontrada ou sem permissão.";
    exit;
}

$foto = $busca->fetch_assoc();
$caminho = "uploads/" . $foto['caminho'];

// Exclui do banco
if ($conexao->query("DELETE FROM fotos WHERE id = $id_foto")) {
    // Exclui arquivo do servidor
    if (file_exists($caminho)) {
        unlink($caminho);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "Erro ao excluir: " . $conexao->error;
}
?>
