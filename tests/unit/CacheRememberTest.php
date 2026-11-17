<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Cache\NullCache;
use PHPUnit\Framework\TestCase;
use tests\Support\ArrayCache;

final class CacheRememberTest extends TestCase
{
    public function testExecutaOCarregadorApenasNaPrimeiraChamada(): void
    {
        $cache    = new ArrayCache();
        $chamadas = 0;
        $carregar = function () use (&$chamadas): array {
            $chamadas++;

            return ['livros' => 3];
        };

        $primeira = $cache->remember('dashboard', 300, $carregar);
        $segunda  = $cache->remember('dashboard', 300, $carregar);

        $this->assertSame(1, $chamadas);
        $this->assertSame(['livros' => 3], $primeira);
        $this->assertSame($primeira, $segunda);
    }

    public function testRecarregaDepoisDeApagarAChave(): void
    {
        $cache    = new ArrayCache();
        $chamadas = 0;
        $carregar = function () use (&$chamadas): int {
            return ++$chamadas;
        };

        $this->assertSame(1, $cache->remember('k', 60, $carregar));
        $cache->delete('k'); // é o que os controllers fazem após inserir/editar/excluir
        $this->assertSame(2, $cache->remember('k', 60, $carregar));
    }

    public function testRepassaOTtlParaOCache(): void
    {
        $cache = new ArrayCache();

        $cache->remember('k', 300, fn (): string => 'valor');

        $this->assertSame(300, $cache->ttls['k']);
    }

    public function testValoresVaziosContamComoAcerto(): void
    {
        $cache    = new ArrayCache();
        $chamadas = 0;
        $carregar = function () use (&$chamadas): array {
            $chamadas++;

            return [];
        };

        $cache->remember('vazio', 60, $carregar);
        $cache->remember('vazio', 60, $carregar);

        $this->assertSame(1, $chamadas);
    }

    public function testNaoGuardaResultadoNull(): void
    {
        $cache = new ArrayCache();

        $resultado = $cache->remember('k', 60, fn () => null);

        $this->assertNull($resultado);
        $this->assertSame(0, $cache->gravacoes);
    }

    public function testExcecaoDoCarregadorNaoContaminaOCache(): void
    {
        $cache = new ArrayCache();

        try {
            $cache->remember('k', 60, function (): never {
                throw new \RuntimeException('banco fora do ar');
            });
            $this->fail('A exceção deveria ter sido propagada.');
        } catch (\RuntimeException) {
            $this->assertSame(0, $cache->gravacoes);
        }
    }

    public function testNullCacheSempreExecutaOCarregador(): void
    {
        $cache    = new NullCache();
        $chamadas = 0;
        $carregar = function () use (&$chamadas): int {
            return ++$chamadas;
        };

        $cache->remember('k', 60, $carregar);
        $cache->remember('k', 60, $carregar);

        $this->assertSame(2, $chamadas);
        $this->assertNull($cache->get('k'));
    }
}