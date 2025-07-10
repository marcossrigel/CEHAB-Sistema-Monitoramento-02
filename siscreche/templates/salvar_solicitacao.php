<?php
require_once("config.php");

// Verifica se os campos foram enviados
if (isset($_POST['nome'], $_POST['telefone'], $_POST['nome_rede'])) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $nome_rede = $_POST['nome_rede'];

    $query = "INSERT INTO solicitacoes (nome, nome_rede, telefone, data_solicitacao) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conexao, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $nome, $nome_rede, $telefone);
        mysqli_stmt_execute($stmt);

        // Verifica se inseriu com sucesso
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>alert('Solicitação enviada com sucesso!'); window.location.href='https://www.getic.pe.gov.br/?p=home';</script>";
            exit;
        } else {
            echo "<script>alert('Erro ao salvar solicitação.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Erro ao preparar a consulta.'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('Dados incompletos.'); history.back();</script>";
    exit;
}
