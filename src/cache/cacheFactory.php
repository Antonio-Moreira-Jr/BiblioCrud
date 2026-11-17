<?php

declare(strict_types=1);

namespace App\Cache;

use Redis;
use RedisException;

final class CacheFactory
{
    private static ?CacheInterface $instancia = null;

    public static function criar(): CacheInterface
    {
        return self::$instancia ??= self::conectar();
    }

    private static function conectar(): CacheInterface
    {
        if (!extension_loaded('redis')) {
            error_log('[Cache] Extensão phpredis não instalada; seguindo sem cache.');

            return new NullCache();
        }

        $host  = getenv('REDIS_HOST') ?: 'redis';
        $porta = (int) (getenv('REDIS_PORT') ?: 6379);

        try {
            $redis = new Redis();
            if (!$redis->connect($host, $porta, 1.0)) {
                error_log("[Cache] Não foi possível conectar ao Redis em {$host}:{$porta}; seguindo sem cache.");

                return new NullCache();
            }
            $redis->setOption(Redis::OPT_READ_TIMEOUT, 1.0);

            return new RedisCache($redis);
        } catch (RedisException $e) {
            error_log('[Cache] Redis indisponível: ' . $e->getMessage() . '; seguindo sem cache.');

            return new NullCache();
        }
    }
}