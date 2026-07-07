<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatorio Sabor Alma</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #2c2c2c; font-size: 12px; }
        h1 { color: #1a3c2a; font-size: 20px; margin-bottom: 2px; }
        .periodo { color: #6c6c6c; margin-bottom: 20px; }
        h2 { color: #1a3c2a; font-size: 14px; border-bottom: 2px solid #c9a84c; padding-bottom: 4px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #e0e0e0; }
        th { background: #f5f0e8; color: #1a3c2a; }
        .sem-dados { color: #999999; font-style: italic; }
        .rodape { margin-top: 30px; color: #999999; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <h1>Relatorio Sabor Alma</h1>
    <p class="periodo">Periodo: <?= htmlspecialchars($dataInicio) ?> a <?= htmlspecialchars($dataFim) ?></p>

    <h2>Produtos Mais Vendidos</h2>
    <?php if (empty($produtos)): ?>
        <p class="sem-dados">Sem vendas neste periodo.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Produto</th><th>Quantidade Vendida</th><th>Total Faturado</th></tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= (int) $p['total_vendido'] ?></td>
                        <td>Kz <?= number_format((float) $p['total_faturado'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Vendas por Periodo</h2>
    <?php if (empty($vendas)): ?>
        <p class="sem-dados">Sem pedidos neste periodo.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Dia</th><th>Pedidos</th><th>Total Vendido</th></tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['dia']) ?></td>
                        <td><?= (int) $v['total_pedidos'] ?></td>
                        <td>Kz <?= number_format((float) $v['total_vendido'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Desempenho por Operador</h2>
    <?php if (empty($operadores)): ?>
        <p class="sem-dados">Sem pedidos neste periodo.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Operador</th><th>Pedidos Registados</th><th>Total Vendido</th></tr>
            </thead>
            <tbody>
                <?php foreach ($operadores as $o): ?>
                    <tr>
                        <td><?= htmlspecialchars($o['operador']) ?></td>
                        <td><?= (int) $o['total_pedidos'] ?></td>
                        <td>Kz <?= number_format((float) $o['total_vendido'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p class="rodape">Gerado em <?= date('d/m/Y H:i') ?> pelo sistema Sabor Alma</p>
</body>
</html>
