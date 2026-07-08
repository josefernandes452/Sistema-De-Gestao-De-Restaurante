<?php
// Partial incluido no top-navbar de todas as paginas do admin. Espera
// que Sessao/Csrf/NotificacaoModel ja estejam carregados (inicializar.php
// ja correu antes de qualquer view do admin ser desenhada).
$notificacaoModel = new NotificacaoModel();
$notificacoesRecentes = $notificacaoModel->recentesPorUtilizador(Sessao::utilizadorAtual()['id'], 6);
$totalNaoLidas = $notificacaoModel->contarNaoLidas(Sessao::utilizadorAtual()['id']);
?>
<div class="dropdown me-2">
    <button class="btn btn-link text-dark position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notificacoes">
        <i class="fas fa-bell fs-5"></i>
        <?php if ($totalNaoLidas > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                <?= $totalNaoLidas > 9 ? '9+' : $totalNaoLidas ?>
            </span>
        <?php endif; ?>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
            <span class="fw-semibold small">Notificacoes</span>
            <?php if ($totalNaoLidas > 0): ?>
                <form method="post" action="/index.php?rota=notificacoes.marcar-todas" class="m-0">
                    <?= Csrf::campo() ?>
                    <button type="submit" class="btn btn-link btn-sm p-0" style="font-size: 0.75rem;">Marcar todas como lidas</button>
                </form>
            <?php endif; ?>
        </div>
        <?php if (empty($notificacoesRecentes)): ?>
            <p class="text-muted small text-center py-3 mb-0">Sem notificacoes.</p>
        <?php else: ?>
            <?php foreach ($notificacoesRecentes as $n): ?>
                <a href="<?= htmlspecialchars($n['link'] ?? '#') ?>" class="dropdown-item small rounded mb-1 <?= $n['lida'] ? '' : 'bg-light fw-semibold' ?>">
                    <?= htmlspecialchars($n['mensagem']) ?>
                    <small class="d-block text-muted fw-normal"><?= htmlspecialchars($n['criado_em']) ?></small>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
