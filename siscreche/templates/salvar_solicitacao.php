<?php
require_once("config.php");

$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$nome_rede = $_POST['nome_rede'];

$query = "INSERT INTO solicitacoes (nome, nome_rede, telefone, data_solicitacao) VALUES (?, ?, ?, NOW())";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "sss", $nome, $nome_rede, $telefone);

echo "<script>alert('Solicitação enviada com sucesso!'); window.location.href='https://www.getic.pe.gov.br/?p=home';</script>";
exit;