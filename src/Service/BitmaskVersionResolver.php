<?php

namespace App\Service;

use App\SemVer\SemVer;

final readonly class BitmaskVersionResolver implements BitmaskVersionResolverInterface
{
    private const int PATCH = 0b001;
    private const int MINOR = 0b010;
    private const int MAJOR = 0b100;
    /**
     * {@inheritDoc}
     */
    public function resolve(string $version, int $currentBitmask, int $newBitmask): array
    {
        $current = $currentBitmask & 0b111;
        $new = $newBitmask & 0b111;

        if ($new <= $current) {
            return ['version' => $version, 'bitmask' => $current];
        }

        $newVersion = SemVer::fromString($version);

        $raised = $new & (~$current);

        if ($raised & self::MAJOR) {
            return ['version' => (string)$newVersion->bumpMajor(), 'bitmask' => $new];
        }

        if ($raised & self::MINOR) {
            return ['version' => (string)$newVersion->bumpMinor(), 'bitmask' => $new];
        }

        if ($raised & self::PATCH) {
            return ['version' => (string)$newVersion->bumpPatch(), 'bitmask' => $new];
        }



        throw new \LogicException('it\'s should never happens');
    }
}
