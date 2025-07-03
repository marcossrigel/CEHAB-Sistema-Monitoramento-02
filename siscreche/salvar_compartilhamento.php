<?php
session_start();
include("templates/config.php");

$id_dono = $_SESSION['id_usuario'];
$nome_usuario = $_POST['nome_usuario'];

// Buscar id do usuário pelo nome
$stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE nome = ?");
$stmt->bind_param("s", $nome_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_compartilhado = $row['id_usuario'];

    // Verifica se já está compartilhado
    $verifica = $conexao->prepare("SELECT * FROM compartilhamentos WHERE id_dono = ? AND id_compartilhado = ?");
    $verifica->bind_param("ii", $id_dono, $id_compartilhado);
    $verifica->execute();
    $jaExiste = $verifica->get_result()->num_rows > 0;

    if (!$jaExiste) {
        // Insere compartilhamento
        $inserir = $conexao->prepare("INSERT INTO compartilhamentos (id_dono, id_compartilhado) VALUES (?, ?)");
        $inserir->bind_param("ii", $id_dono, $id_compartilhado);
        $inserir->execute();
    }

    // Redireciona com mensagem
    header("Location: index.php?page=home&compartilhado=" . urlencode($nome_usuario));
    exit;
}

// Caso não encontre o usuário
header("Location: index.php?page=home&erro=usuario");
exit;
?>
