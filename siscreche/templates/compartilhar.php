<?php
// templates/compartilhar_modal.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['id_usuario'])) { http_response_code(401); exit('Sem sessão'); }

require_once __DIR__ . '/config.php';
$id_usuario = (int)$_SESSION['id_usuario'];

// Suas iniciativas
$sql_iniciativas = "SELECT id, iniciativa FROM iniciativas WHERE id_usuario = $id_usuario ORDER BY id DESC";
$res_iniciativas = $conexao->query($sql_iniciativas);

// Já compartilhados
$sql_compartilhados = "
  SELECT DISTINCT u.nome AS nome_usuario, u.id_usuario
    FROM compartilhamentos c
    JOIN usuarios u ON u.id_usuario = c.id_compartilhado
   WHERE c.id_dono = $id_usuario
   ORDER BY u.nome
";
$res_comp = $conexao->query($sql_compartilhados);
?>
<div class="space-y-5">
  <h2 class="text-lg font-semibold text-slate-800">Compartilhar Iniciativas</h2>

  <form id="formCompartilhar" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Nome do Usuário (REDE)</label>
      <div class="relative">
        <input type="text" name="usuario" id="cmp_usuario"
               placeholder="Digite o nome do usuário da rede"
               required
               class="w-full border rounded-lg px-3 py-2">
        <div id="cmp_sugestoes"
             class="absolute left-0 right-0 top-full bg-white border rounded-md shadow max-h-52 overflow-y-auto hidden z-10"></div>
      </div>
    </div>

    <div class="pt-2">
      <div class="font-medium text-slate-800 mb-2">Selecione as iniciativas a compartilhar:</div>

      <label class="inline-flex items-center gap-2 mb-2">
        <input type="checkbox" id="cmp_todos" class="rounded border-slate-300">
        <span>Selecionar todas</span>
      </label>

      <div class="grid sm:grid-cols-2 gap-2">
        <?php if ($res_iniciativas && $res_iniciativas->num_rows): ?>
          <?php while ($l = $res_iniciativas->fetch_assoc()): ?>
            <label class="flex items-start gap-2 p-2 border rounded-lg bg-white">
              <input type="checkbox" name="iniciativas[]" value="<?= (int)$l['id'] ?>" class="mt-1 rounded border-slate-300">
              <span class="text-sm"><?= htmlspecialchars($l['iniciativa'], ENT_QUOTES, 'UTF-8') ?></span>
            </label>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="text-slate-500">Você não possui iniciativas para compartilhar.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2">
      <button type="button" data-close-compartilhar
              class="rounded-full px-4 py-2 border border-slate-300 text-slate-800 hover:bg-slate-50">Cancelar</button>
      <button type="submit"
              class="rounded-full px-5 py-2 bg-blue-600 text-white font-semibold hover:bg-blue-700">Compartilhar</button>
    </div>
  </form>

  <hr class="border-slate-200">

  <div>
    <div class="font-medium text-slate-800 mb-2">Já compartilhado com:</div>
    <?php if ($res_comp && $res_comp->num_rows): ?>
      <ul class="divide-y rounded-lg border bg-white">
        <?php while ($l = $res_comp->fetch_assoc()): ?>
          <ul class="lista-compartilhados divide-y divide-slate-200">
            <?php while ($linha = $res_compartilhados->fetch_assoc()): ?>
              <li class="flex items-center gap-2 py-2">
                <img src="img/user.png"
                    alt=""
                    class="h-5 w-5 rounded-full shrink-0"
                    loading="lazy" />
                <span class="text-sm text-slate-800 flex-1 truncate">
                  <?= htmlspecialchars($linha['nome_usuario']) ?>
                </span>
                <button class="cmp-remover text-red-600 hover:underline text-sm"
                        data-id="<?= (int)$linha['id_usuario'] ?>">
                  Remover
                </button>
              </li>
            <?php endwhile; ?>
          </ul>

        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <div class="text-slate-500">Nenhum usuário ainda.</div>
    <?php endif; ?>
  </div>
</div>
