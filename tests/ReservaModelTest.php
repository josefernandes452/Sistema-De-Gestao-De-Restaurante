<?php

use PHPUnit\Framework\TestCase;

// Teste de integracao (usa a base de dados real): confirma que
// ReservaModel::existeConflito() bloqueia mesmo duas reservas na
// mesma mesa/dia/hora, mas deixa passar se a reserva anterior estiver
// cancelada. Tudo dentro de uma transacao que e sempre desfeita no
// tearDown, para nao deixar nenhum dado de teste na base a serio.
final class ReservaModelTest extends TestCase
{
    private PDO $pdo;
    private ReservaModel $reservaModel;
    private int $mesaId;

    protected function setUp(): void
    {
        $this->pdo = Database::getConexao();
        $this->pdo->beginTransaction();

        $this->reservaModel = new ReservaModel();
        $this->mesaId = (int) $this->pdo->query('SELECT id FROM mesas ORDER BY id LIMIT 1')->fetchColumn();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testDetetaConflitoNaMesmaMesaDiaEHora(): void
    {
        $this->reservaModel->inserir([
            'mesa_id' => $this->mesaId,
            'nome' => 'Reserva de teste',
            'telefone' => '900000000',
            'data' => '2026-08-01',
            'hora' => '19:00:00',
            'pessoas' => 2,
            'estado' => 'Confirmada',
        ]);

        $this->assertTrue(
            $this->reservaModel->existeConflito($this->mesaId, '2026-08-01', '19:00:00')
        );
    }

    public function testNaoAcusaConflitoEmHoraDiferente(): void
    {
        $this->reservaModel->inserir([
            'mesa_id' => $this->mesaId,
            'nome' => 'Reserva de teste',
            'telefone' => '900000000',
            'data' => '2026-08-01',
            'hora' => '19:00:00',
            'pessoas' => 2,
            'estado' => 'Confirmada',
        ]);

        $this->assertFalse(
            $this->reservaModel->existeConflito($this->mesaId, '2026-08-01', '21:00:00')
        );
    }

    public function testReservaCanceladaNaoConta(): void
    {
        $this->reservaModel->inserir([
            'mesa_id' => $this->mesaId,
            'nome' => 'Reserva de teste',
            'telefone' => '900000000',
            'data' => '2026-08-01',
            'hora' => '19:00:00',
            'pessoas' => 2,
            'estado' => 'Cancelada',
        ]);

        $this->assertFalse(
            $this->reservaModel->existeConflito($this->mesaId, '2026-08-01', '19:00:00')
        );
    }
}
