<?php
require_once("config.php");

$nome = $_POST['nome'];
$telefone = $_POST['telefone'];

$query = "INSERT INTO solicitacoes (nome, telefone, data_solicitacao) VALUES (?, ?, NOW())";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "ss", $nome, $telefone);
mysqli_stmt_execute($stmt);

echo "<script>alert('Solicitação enviada com sucesso!'); window.location.href='https://www.getic.pe.gov.br/?p=home';</script>";
exit;