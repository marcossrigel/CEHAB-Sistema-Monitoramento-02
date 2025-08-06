<?php
require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conexao->prepare("DELETE FROM projeto_licitacoes WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "sucesso";
    } else {
        echo "erro: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
} else {
    echo "requisicao invalida";
}
