<?php

declare(strict_types=1);

namespace App\Cache;

/** Usado quando o Redis está indisponível: a aplicação segue funcionando, sem cache. */
final class NullCache extends AbstractCache
{
    public function get(string $chave): mixed
    {
        return null;
    }

    public function set(string $chave, mixed $valor, int $ttlSegundos): void
    {
    }

    public function delete(string ...$chaves): void
    {
    }
}