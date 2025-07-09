<?php
session_start();
include("templates/config.php");

$id_dono = $_SESSION['id_usuario'];
$id_iniciativa = $_POST['id_iniciativa'];
$id_compartilhado = $_POST['id_compartilhado'];

// Verifica se já está compartilhado com essa iniciativa
$verifica = $conexao->prepare("SELECT * FROM compartilhamentos WHERE id_dono = ? AND id_compartilhado = ? AND id_iniciativa = ?");
$verifica->bind_param("iii", $id_dono, $id_compartilhado, $id_iniciativa);
$verifica->execute();
$jaExiste = $verifica->get_result()->num_rows > 0;

if (!$jaExiste) {
    // Inserir o compartilhamento específico
    $inserir = $conexao->prepare("INSERT INTO compartilhamentos (id_dono, id_compartilhado, id_iniciativa) VALUES (?, ?, ?)");
    $inserir->bind_param("iii", $id_dono, $id_compartilhado, $id_iniciativa);
    $inserir->execute();
}

// Redirecionar de volta com sucesso
header("Location: index.php?page=visualizar&compartilhado=ok");
exit;
?>
