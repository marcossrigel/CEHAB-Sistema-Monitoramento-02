<?php
session_start();
include("templates/config.php");

$id_dono = $_SESSION['id_usuario'];
$usuario_destino = $_POST['usuario'] ?? '';
$iniciativas = $_POST['iniciativas'] ?? [];

// Buscar o ID do usuário destino a partir do nome de rede
$sql_usuario = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE nome_usuario = ?");
$sql_usuario->bind_param("s", $usuario_destino);
$sql_usuario->execute();
$resultado = $sql_usuario->get_result();

if ($resultado->num_rows === 0) {
    // Usuário não encontrado
    header("Location: index.php?page=compartilhar&erro=usuario_nao_encontrado");
    exit;
}

$row = $resultado->fetch_assoc();
$id_compartilhado = $row['id_usuario'];

// Inserir compartilhamentos (evitando duplicados)
foreach ($iniciativas as $id_iniciativa) {
    // Verificar se já existe
    $verifica = $conexao->prepare("SELECT 1 FROM compartilhamentos WHERE id_dono = ? AND id_compartilhado = ? AND id_iniciativa = ?");
    $verifica->bind_param("iii", $id_dono, $id_compartilhado, $id_iniciativa);
    $verifica->execute();
    $res = $verifica->get_result();

    if ($res->num_rows === 0) {
        // Inserir novo compartilhamento
        $insere = $conexao->prepare("INSERT INTO compartilhamentos (id_dono, id_compartilhado, id_iniciativa) VALUES (?, ?, ?)");
        $insere->bind_param("iii", $id_dono, $id_compartilhado, $id_iniciativa);
        $insere->execute();
    }
}

// Redirecionar com sucesso
header("Location: index.php?page=visualizar&compartilhado=ok");
exit;
?>
