<?php

namespace App\Service;

interface BitmaskVersionResolverInterface
{
    /**
     * @param string $version
     * @param int $currentBitmask
     * @param int $newBitmask
     * @return array{ version: string, bitmask: int}
     */
    public function resolve(string $version, int $currentBitmask, int $newBitmask): array;
}
