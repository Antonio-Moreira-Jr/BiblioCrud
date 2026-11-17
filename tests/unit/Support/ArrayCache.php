<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Cache\AbstractCache;

/** Cache em memória usado nos testes, sem depender do Redis. */
final class ArrayCache extends AbstractCache
{
    /** @var array<string, mixed> */
    public array $itens = [];

    /** @var array<string, int> */
    public array $ttls = [];

    public int $gravacoes = 0;

    public function get(string $chave): mixed
    {
        return $this->itens[$chave] ?? null;
    }

    public function set(string $chave, mixed $valor, int $ttlSegundos): void
    {
        $this->itens[$chave] = $valor;
        $this->ttls[$chave]  = $ttlSegundos;
        $this->gravacoes++;
    }

    public function delete(string ...$chaves): void
    {
        foreach ($chaves as $chave) {
            unset($this->itens[$chave]);
        }
    }
}