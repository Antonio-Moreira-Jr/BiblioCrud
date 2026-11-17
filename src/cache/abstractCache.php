<?php

declare(strict_types=1);

namespace App\Cache;

abstract class AbstractCache implements CacheInterface
{
    public function remember(string $chave, int $ttlSegundos, callable $carregar): mixed
    {
        $valor = $this->get($chave);
        if ($valor !== null) {
            return $valor;
        }

        $valor = $carregar();
        if ($valor !== null) {
            $this->set($chave, $valor, $ttlSegundos);
        }

        return $valor;
    }
}