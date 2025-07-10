<?php
require_once("../templates/config.php"); // ou ajuste o caminho da conexão com $conexao2

$termo = $_GET['termo'] ?? '';

if (strlen($termo) >= 2) {
    $stmt = $conexao2->prepare("SELECT u_rede FROM users WHERE u_rede LIKE ? ORDER BY u_rede ASC LIMIT 10");
    $like = "%" . $termo . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row['u_rede'];
    }

    echo json_encode($usuarios);
}
