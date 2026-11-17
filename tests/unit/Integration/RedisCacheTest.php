<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Cache\RedisCache;
use PHPUnit\Framework\TestCase;
use Redis;
use RedisException;

final class RedisCacheTest extends TestCase
{
    private Redis $redis;
    private RedisCache $cache;
    private string $prefixo;

    protected function setUp(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Extensão phpredis não instalada.');
        }

        $redis = new Redis();
        try {
            $conectou = $redis->connect(
                getenv('REDIS_HOST') ?: '127.0.0.1',
                (int) (getenv('REDIS_PORT') ?: 6379),
                1.0
            );
        } catch (RedisException) {
            $conectou = false;
        }

        if (!$conectou) {
            $this->markTestSkipped('Servidor Redis indisponível.');
        }

        $this->redis   = $redis;
        $this->prefixo = 'teste:' . uniqid('', true) . ':';
        $this->cache   = new RedisCache($this->redis, $this->prefixo);
    }

    protected function tearDown(): void
    {
        if (!isset($this->redis)) {
            return;
        }

        foreach ($this->redis->keys($this->prefixo . '*') ?: [] as $chave) {
            $this->redis->del($chave);
        }
    }

    public function testGravaELeDadosEstruturados(): void
    {
        $dados = ['totais' => ['livros' => 3, 'valorAcervo' => 150.5], 'ultimos' => [['Titulo' => 'Código Limpo']]];

        $this->cache->set('dashboard', $dados, 60);

        $this->assertSame($dados, $this->cache->get('dashboard'));
    }

    public function testRetornaNullQuandoAChaveNaoExiste(): void
    {
        $this->assertNull($this->cache->get('nao-existe'));
    }

    public function testDeleteRemoveAChave(): void
    {
        $this->cache->set('k', 'valor', 60);

        $this->cache->delete('k');

        $this->assertNull($this->cache->get('k'));
    }

    public function testDeleteAceitaVariasChaves(): void
    {
        $this->cache->set('a', 1, 60);
        $this->cache->set('b', 2, 60);

        $this->cache->delete('a', 'b');

        $this->assertNull($this->cache->get('a'));
        $this->assertNull($this->cache->get('b'));
    }

    public function testDefineExpiracaoNaChave(): void
    {
        $this->cache->set('k', 'valor', 120);

        $ttl = $this->redis->ttl($this->prefixo . 'k');

        $this->assertGreaterThan(0, $ttl);
        $this->assertLessThanOrEqual(120, $ttl);
    }

    public function testRememberConsultaOBancoUmaVezSo(): void
    {
        $consultas = 0;
        $carregar  = function () use (&$consultas): array {
            $consultas++;

            return ['livros' => 7];
        };

        $this->cache->remember('dashboard', 60, $carregar);
        $this->cache->remember('dashboard', 60, $carregar);

        $this->assertSame(1, $consultas);
    }

    public function testFalhaDoRedisNaoLancaExcecao(): void
    {
        // Redis sem conexão: o cache deve se comportar como vazio e registrar no log.
        $logAnterior = ini_set('error_log', sys_get_temp_dir() . '/phpunit-bibliocrud.log');

        try {
            $semConexao = new RedisCache(new Redis(), 'x:');

            $this->assertNull($semConexao->get('k'));
            $semConexao->set('k', 1, 10);
            $semConexao->delete('k');
        } finally {
            ini_set('error_log', $logAnterior === false ? '' : $logAnterior);
        }
    }
}