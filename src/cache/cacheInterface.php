<?php

declare(strict_types=1);

namespace App\Cache;

interface CacheInterface
{
    public function get(string $chave): mixed;

    public function set(string $chave, mixed $valor, int $ttlSegundos): void;

    public function delete(string ...$chaves): void;

    public function remember(string $chave, int $ttlSegundos, callable $carregar): mixed;
}