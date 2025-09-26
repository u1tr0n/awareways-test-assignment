<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class UuidData
{
    public function __construct(
        public UuidV7 $id,
    ) {}
}
