<?php

declare(strict_types=1);

namespace App\Cache;

use JsonException;
use Redis;
use RedisException;

/**
 * Cache em Redis (extensão phpredis). Os valores são guardados como JSON.
 * Falhas do Redis são registradas no log e tratadas como "cache vazio":
 * o cache nunca deve derrubar a aplicação.
 */
final class RedisCache extends AbstractCache
{
    public function __construct(
        private readonly Redis $redis,
        private readonly string $prefixo = 'bibliocrud:'
    ) {
    }

    public function get(string $chave): mixed
    {
        try {
            $bruto = $this->redis->get($this->prefixo . $chave);
            if ($bruto === false) {
                return null;
            }

            return json_decode((string) $bruto, true, 512, JSON_THROW_ON_ERROR);
        } catch (RedisException | JsonException $e) {
            error_log('[Cache] Falha ao ler "' . $chave . '": ' . $e->getMessage());

            return null;
        }
    }

    public function set(string $chave, mixed $valor, int $ttlSegundos): void
    {
        try {
            $this->redis->setex(
                $this->prefixo . $chave,
                $ttlSegundos,
                json_encode($valor, JSON_THROW_ON_ERROR)
            );
        } catch (RedisException | JsonException $e) {
            error_log('[Cache] Falha ao gravar "' . $chave . '": ' . $e->getMessage());
        }
    }

    public function delete(string ...$chaves): void
    {
        if ($chaves === []) {
            return;
        }

        try {
            $this->redis->del(array_map(fn (string $c): string => $this->prefixo . $c, $chaves));
        } catch (RedisException $e) {
            error_log('[Cache] Falha ao apagar chaves: ' . $e->getMessage());
        }
    }
}