<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/RelatorioModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class RelatorioController extends Controller
{
    private RelatorioModel $relatorioModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->relatorioModel = new RelatorioModel();
    }

    public function exportarCsv(): void
    {
        $dados = $this->carregarDados();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="relatorio_' . $dados['dataInicio'] . '_a_' . $dados['dataFim'] . '.csv"');

        $saida = fopen('php://output', 'w');

        // O BOM no inicio e para o Excel abrir o ficheiro com os
        // acentos certos. Sem isto o "ã" e o "ç" ficam trocados.
        fwrite($saida, "\xEF\xBB\xBF");

        fputcsv($saida, ['Relatorio Sabor Alma']);
        fputcsv($saida, ['Periodo', $dados['dataInicio'] . ' a ' . $dados['dataFim']]);
        fputcsv($saida, []);

        fputcsv($saida, ['Produtos Mais Vendidos']);
        fputcsv($saida, ['Produto', 'Quantidade Vendida', 'Total Faturado (Kz)']);
        foreach ($dados['produtos'] as $p) {
            fputcsv($saida, [$p['nome'], $p['total_vendido'], number_format((float) $p['total_faturado'], 2, '.', '')]);
        }
        fputcsv($saida, []);

        fputcsv($saida, ['Vendas por Periodo']);
        fputcsv($saida, ['Dia', 'Pedidos', 'Total Vendido (Kz)']);
        foreach ($dados['vendas'] as $v) {
            fputcsv($saida, [$v['dia'], $v['total_pedidos'], number_format((float) $v['total_vendido'], 2, '.', '')]);
        }
        fputcsv($saida, []);

        fputcsv($saida, ['Desempenho por Operador']);
        fputcsv($saida, ['Operador', 'Pedidos Registados', 'Total Vendido (Kz)']);
        foreach ($dados['operadores'] as $o) {
            fputcsv($saida, [$o['operador'], $o['total_pedidos'], number_format((float) $o['total_vendido'], 2, '.', '')]);
        }

        fclose($saida);
        exit;
    }

    public function exportarPdf(): void
    {
        $dados = $this->carregarDados();

        ob_start();
        $this->view('admin/relatorio-pdf', $dados);
        $html = ob_get_clean();

        $opcoes = new Options();
        $opcoes->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($opcoes);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('relatorio_' . $dados['dataInicio'] . '_a_' . $dados['dataFim'] . '.pdf', ['Attachment' => true]);
        exit;
    }

    private function carregarDados(): array
    {
        $dataInicio = Validador::texto($_GET['data_inicio'] ?? '') ?: date('Y-m-d', strtotime('-7 day'));
        $dataFim = Validador::texto($_GET['data_fim'] ?? '') ?: date('Y-m-d');

        return [
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
            'produtos' => $this->relatorioModel->produtosMaisVendidos($dataInicio, $dataFim),
            'vendas' => $this->relatorioModel->vendasPorPeriodo($dataInicio, $dataFim),
            'operadores' => $this->relatorioModel->desempenhoPorOperador($dataInicio, $dataFim),
        ];
    }
}
