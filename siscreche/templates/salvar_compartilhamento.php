<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    echo "Sessão expirada.";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$usuario_rede = trim($_POST['usuario'] ?? '');
$iniciativas = $_POST['iniciativas'] ?? [];

if (empty($usuario_rede) || empty($iniciativas)) {
    echo "Preencha o nome do usuário e selecione pelo menos uma iniciativa.";
    exit;
}

// Conexão com banco CEHAB Online
$cehab_online = new mysqli("localhost", "root", "", "cehab_online"); // ajuste se necessário
if ($cehab_online->connect_error) {
    echo "Erro ao conectar no CEHAB Online.";
    exit;
}

$query_user = "SELECT g_id FROM users WHERE u_rede = ?";
$stmt = $cehab_online->prepare($query_user);
$stmt->bind_param("s", $usuario_rede);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuário não encontrado na base CEHAB Online.";
    exit;
}

$g_id = $result->fetch_assoc()['g_id'];
$stmt->close();
$cehab_online->close();

// Conexão com banco SISCRECHE
include("config.php");

$query_local = "SELECT id_usuario FROM usuarios WHERE id_usuario_cehab_online = ?";
$stmt2 = $conexao->prepare($query_local);
$stmt2->bind_param("i", $g_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    echo "Este usuário ainda não foi ativado no sistema.";
    exit;
}

$id_compartilhado = $result2->fetch_assoc()['id_usuario'];
$stmt2->close();

// Evitar duplicatas: exclui anteriores antes de inserir novamente
$stmt_del = $conexao->prepare("DELETE FROM compartilhamentos WHERE id_dono = ? AND id_compartilhado = ?");
$stmt_del->bind_param("ii", $id_usuario, $id_compartilhado);
$stmt_del->execute();
$stmt_del->close();

// Inserir os novos compartilhamentos
$stmt_insert = $conexao->prepare("INSERT INTO compartilhamentos (id_dono, id_compartilhado, id_iniciativa) VALUES (?, ?, ?)");
foreach ($iniciativas as $id_iniciativa) {
    $stmt_insert->bind_param("iii", $id_usuario, $id_compartilhado, $id_iniciativa);
    $stmt_insert->execute();
}
$stmt_insert->close();

echo "Compartilhado com sucesso!";

header("Location: index.php?page=compartilhar");
exit;

?>
