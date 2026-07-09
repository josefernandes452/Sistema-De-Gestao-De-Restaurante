<?php
// Modal de "Alterar Senha", incluido em todas as paginas do painel
// administrativo. O avatar no topo (ver top-navbar de cada view) abre
// este modal. voltar_para leva de volta a mesma pagina onde o
// utilizador estava, para nao o mandar sempre para o dashboard.
?>
<div class="modal fade" id="modalAlterarSenha" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="/index.php?rota=alterar-senha">
                <?= Csrf::campo() ?>
                <input type="hidden" name="voltar_para" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <div class="modal-header" style="background: #1a3c2a; color: white;">
                    <h5 class="modal-title"><i class="fas fa-key me-2"></i> Alterar Senha</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Senha Atual</label>
                        <input type="password" name="senha_atual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nova Senha</label>
                        <input type="password" name="nova_senha" class="form-control" placeholder="Minimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar Nova Senha</label>
                        <input type="password" name="confirmar_nova_senha" class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
