<?php

namespace App\SemVer;

final readonly class SemVer implements \Stringable
{
    public function __construct(
        public int $major = 0,
        public int $minor = 0,
        public int $patch = 0,
    ) {}

    public function __toString(): string
    {
        return sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);
    }

    public static function fromString(string $version): self
    {
        if (!preg_match('/^\d+(\.\d+){2}$/', $version)) {
            throw new \InvalidArgumentException('Invalid version');
        }

        return new self(...array_map('intval', explode('.', $version)));
    }

    public function bumpMajor(int $amount = 1): self
    {
        return new self(
            major: $this->major + $amount,
            minor: 0,
            patch: 0,
        );
    }

    public function bumpMinor(int $amount = 1): self
    {
        return new self(
            major: $this->major,
            minor: $this->minor + $amount,
            patch: 0,
        );
    }

    public function bumpPatch(int $amount = 1): self
    {
        return new self(
            major: $this->major,
            minor: $this->minor,
            patch: $this->patch + $amount,
        );
    }
}
