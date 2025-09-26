<?php

namespace App\Tests\SemVer;

use App\SemVer\SemVer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SemVerTest extends TestCase
{
    public function testConstruct(): void
    {
        $version = new SemVer(1, 0, 0);

        $this->assertSame('1.0.0', (string)$version);

        $version = new SemVer(11, 27, 382);

        $this->assertSame('11.27.382', (string)$version);
    }

    public function testToString(): void
    {
        $ver = '3.12.6';
        $version = SemVer::fromString($ver);
        $this->assertSame($ver, (string)$version);
    }

    public function testBumpPatch(): void
    {
        $initialVersion = '3.17.28';
        $version = SemVer::fromString($initialVersion)->bumpPatch();
        $this->assertSame('3.17.29', (string)$version);
        $this->assertSame('3.17.32', (string)$version->bumpPatch()->bumpPatch()->bumpPatch());
    }

    public function testBumpMinor(): void
    {
        $initialVersion = '3.17.28';
        $version = SemVer::fromString($initialVersion)->bumpMinor();
        $this->assertSame('3.18.0', (string)$version);
        $this->assertSame('3.20.0', (string)$version->bumpPatch()->bumpMinor()->bumpPatch()->bumpMinor());
    }

    public function testBumpMajor(): void
    {
        $initialVersion = '3.17.28';
        $version = SemVer::fromString($initialVersion)
            ->bumpPatch()
            ->bumpPatch()
            ->bumpPatch()
            ->bumpMinor()
            ->bumpPatch()
            ->bumpPatch()
            ->bumpMajor()
        ;
        $this->assertSame('4.0.0', (string)$version);
    }
}
