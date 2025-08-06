<?php
include("config.php");

$usuario_rede = trim($_POST['usuario'] ?? '');
$iniciativas = $_POST['iniciativas'] ?? [];
$id_dono = $_SESSION['id_usuario'];

if (empty($usuario_rede) || empty($iniciativas)) {
    echo "Preencha o nome do usuário e selecione pelo menos uma iniciativa.";
    exit;
}

$stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE usuario_rede = ?");
$stmt->bind_param("s", $usuario_rede);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuário não encontrado na base local.";
    exit;
}

$id_compartilhado = $result->fetch_assoc()['id_usuario'];
$stmt->close();

$stmt_del = $conexao->prepare("DELETE FROM compartilhamentos WHERE id_dono = ? AND id_compartilhado = ?");
$stmt_del->bind_param("ii", $id_dono, $id_compartilhado);
$stmt_del->execute();
$stmt_del->close();

$stmt_insert = $conexao->prepare("INSERT INTO compartilhamentos (id_dono, id_compartilhado, id_iniciativa) VALUES (?, ?, ?)");
foreach ($iniciativas as $id_iniciativa) {
    $stmt_insert->bind_param("iii", $id_dono, $id_compartilhado, $id_iniciativa);
    $stmt_insert->execute();
}
$stmt_insert->close();

header("Location: index.php?page=compartilhar");
exit;

?>
