<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    echo "Acesso negado";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "DELETE FROM marcos WHERE id = $id AND id_usuario = $id_usuario";
    if (mysqli_query($conexao, $query)) {
        echo "Linha excluída com sucesso.";
    } else {
        http_response_code(500);
        echo "Erro ao excluir linha: " . mysqli_error($conexao);
    }
} else {
    http_response_code(400);
    echo "ID inválido.";
}
