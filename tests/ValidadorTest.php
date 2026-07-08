<?php

use PHPUnit\Framework\TestCase;

// Testes da classe Validador (config/Validador.php). E a peca que
// protege todos os formularios do site, entao vale a pena garantir
// que os casos simples e os casos limite se comportam como esperado.
final class ValidadorTest extends TestCase
{
    public function testTextoRemoveEspacosNasPontas(): void
    {
        $this->assertSame('Bife a Casa', Validador::texto('  Bife a Casa  '));
    }

    public function testTextoNaoEscapaHtml(): void
    {
        // De proposito: o escape acontece so na hora de mostrar
        // (htmlspecialchars na view), nunca aqui. Ver o comentario
        // no proprio Validador::texto() sobre o bug do "&amp;amp;".
        $this->assertSame('Pão & Cia', Validador::texto('Pão & Cia'));
    }

    public function testTextoComValorNuloDevolveStringVazia(): void
    {
        $this->assertSame('', Validador::texto(null));
    }

    public function testEmailValidoEAceite(): void
    {
        $this->assertSame('cliente@exemplo.com', Validador::email(' cliente@exemplo.com '));
    }

    public function testEmailInvalidoDevolveFalse(): void
    {
        $this->assertFalse(Validador::email('nao-e-um-email'));
    }

    public function testEmailVazioDevolveFalse(): void
    {
        $this->assertFalse(Validador::email(''));
    }

    public function testInteiroAceitaNumeroValido(): void
    {
        $this->assertSame(42, Validador::inteiro('42'));
    }

    public function testInteiroRejeitaTextoNaoNumerico(): void
    {
        $this->assertFalse(Validador::inteiro('abc'));
    }

    public function testInteiroAceitaZero(): void
    {
        // Importante: 0 e um inteiro valido (ex: um id que ainda nao
        // existe), tem de se distinguir de false (invalido).
        $this->assertSame(0, Validador::inteiro('0'));
    }

    public function testDecimalAceitaValorComVirgulaTrocadaPorPonto(): void
    {
        $this->assertSame(2500.50, Validador::decimal('2500.50'));
    }

    public function testDecimalRejeitaTextoNaoNumerico(): void
    {
        $this->assertFalse(Validador::decimal('gratis'));
    }

    public function testObrigatorioRejeitaStringSoComEspacos(): void
    {
        $this->assertFalse(Validador::obrigatorio('   '));
    }

    public function testObrigatorioAceitaTextoComConteudo(): void
    {
        $this->assertTrue(Validador::obrigatorio('Mesa 5'));
    }
}
