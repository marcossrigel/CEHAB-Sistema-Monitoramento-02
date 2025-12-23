<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
date_default_timezone_set('America/Recife');

require_once __DIR__ . '/config.php';

if (empty($_SESSION['id_usuario'])) {
  header('Location: ../login.php');
  exit;
}

$id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
$iniciativa    = trim($_POST['iniciativa'] ?? '');
$data_vistoria = trim($_POST['data_vistoria'] ?? '');
$ib_status     = trim($_POST['ib_status'] ?? '');

$ib_previsto   = trim($_POST['ib_previsto'] ?? '');
$ib_secretaria = trim($_POST['ib_secretaria'] ?? '');
$ib_diretoria  = trim($_POST['ib_diretoria'] ?? '');

$ib_gestor_responsavel = trim($_POST['ib_gestor_responsavel'] ?? '');
$ib_fiscal             = trim($_POST['ib_fiscal'] ?? '');

$objeto            = trim($_POST['objeto'] ?? '');
$informacoes_gerais = trim($_POST['informacoes_gerais'] ?? '');
$observacoes        = trim($_POST['observacoes'] ?? '');
$prefixo = preg_replace('/\D/', '', $_POST['numero_contrato_prefixo'] ?? '');
$ano     = preg_replace('/\D/', '', $_POST['numero_contrato_ano'] ?? '');
$numero_contrato = ($prefixo && $ano) ? ($prefixo . '/' . $ano) : '';

if ($iniciativa === '' || $data_vistoria === '' || $ib_status === '' || $ib_previsto === '' || $ib_secretaria === '' || $ib_diretoria === '' || $numero_contrato === '') {
  die('Campos obrigatórios não preenchidos.');
}

$stmt = $conexao->prepare("
  INSERT INTO iniciativas
  (id_usuario, iniciativa, data_vistoria, ib_status, ib_previsto, ib_secretaria, ib_diretoria,
   ib_gestor_responsavel, ib_fiscal, numero_contrato, objeto, informacoes_gerais, observacoes)
  VALUES
  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "issssssssssss",
  $id_usuario,
  $iniciativa,
  $data_vistoria,
  $ib_status,
  $ib_previsto,
  $ib_secretaria,
  $ib_diretoria,
  $ib_gestor_responsavel,
  $ib_fiscal,
  $numero_contrato,
  $objeto,
  $informacoes_gerais,
  $observacoes
);

if (!$stmt->execute()) {
  die("Erro ao criar: " . $conexao->error);
}

$newId = $stmt->insert_id;

header("Location: ../index.php?page=home&open=detalhes&id_iniciativa=" . urlencode($newId));
exit;
