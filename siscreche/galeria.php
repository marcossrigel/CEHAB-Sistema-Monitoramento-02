<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

include_once("config.php");

$id_usuario = $_SESSION['id_usuario'];
$id_iniciativa = isset($_GET['id_iniciativa']) ? intval($_GET['id_iniciativa']) : 0;

$query = "SELECT * FROM fotos WHERE id_iniciativa = $id_iniciativa AND id_usuario = $id_usuario ORDER BY id ASC";
$result = mysqli_query($conexao, $query);

$nome_iniciativa = "Desconhecida";
$res = mysqli_query($conexao, "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa");
if ($linha = mysqli_fetch_assoc($res)) {
    $nome_iniciativa = $linha['iniciativa'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeria de Fotos</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f1f1f1;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 20px;
      color: #333;
    }

    .galeria {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    .foto {
      border: 1px solid #ccc;
      padding: 10px;
      background: #fff;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
    }

    .foto img {
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
      border-radius: 6px;
    }

    .foto textarea {
      width: 100%;
      resize: none;
      height: 60px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      padding: 8px;
      background-color: #f9f9f9;
    }

    .voltar-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }

    .btn-voltar {
      background-color: #007bff;
      color: white;
      padding: 12px 24px;
      font-size: 15px;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-voltar:hover {
      background-color: #0056b3;
    }

    @media (max-width: 500px) {
      h2 {
        font-size: 18px;
      }

      .foto textarea {
        font-size: 13px;
      }
    }

    @media (max-width: 768px) {
      .galeria {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 480px) {
      .galeria {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <h2>Galeria - Iniciativa <?php echo htmlspecialchars($nome_iniciativa); ?></h2>

  <div class="galeria">
    
    <?php while ($foto = mysqli_fetch_assoc($result)): ?>
    <div class="foto">
      <img src="uploads/<?php echo htmlspecialchars($foto['caminho']); ?>" alt="">
      <textarea readonly><?php echo htmlspecialchars($foto['descricao']); ?></textarea>
      <form method="get" action="excluir_foto.php" onsubmit="return confirm('Tem certeza que deseja excluir esta foto?');">
        <input type="hidden" name="id" value="<?php echo $foto['id']; ?>">
        <button type="submit" style="margin-top: 10px; background-color: red; color: white; border: none; padding: 8px; border-radius: 6px; cursor: pointer;">Excluir</button>
      </form>
    </div>
    <?php endwhile; ?>

  </div>

  <div class="voltar-container">
    <button type="button" class="btn-voltar" onclick="window.location.href='visualizar.php';">&lt; Voltar</button>
  </div>
</body>
</html>