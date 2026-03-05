<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public int $stokTersedia,
        public int $jumlahDiminta,
        ?string $message = null
    ) {
        parent::__construct(
            $message ?? "Stok tidak mencukupi. Tersedia: {$stokTersedia}, diminta: {$jumlahDiminta}."
        );
    }
}
