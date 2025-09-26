<?php

namespace App\Tests\Service;

use App\Service\BitmaskVersionResolver;
use App\Service\BitmaskVersionResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BitmaskVersionResolverTest extends TestCase
{
    private BitmaskVersionResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new BitmaskVersionResolver();
    }

    #[DataProvider('dataProvider')]
    public function testResolve(int $current, int $new, string $version, string $resultVersion, int $resultBitmask): void
    {
        $this->assertSame(
            ['version' => $resultVersion, 'bitmask' => $resultBitmask],
            $this->resolver->resolve($version, $current, $new),
        );
    }

    public static function dataProvider(): \Generator
    {
        yield 'new mask < current' => [0b100, 0b001, '1.0.0', '1.0.0', 0b100];
        yield 'new mask = current' => [0b111, 0b111, '1.0.0', '1.0.0', 0b111];
        yield 'should bump patch' => [0b000, 0b001, '1.0.0', '1.0.1', 0b001];
        yield 'should bump minor' => [0b000, 0b011, '1.0.0', '1.1.0', 0b011];
        yield 'should bump major' => [0b000, 0b101, '1.3.5', '2.0.0', 0b101];
    }
}
