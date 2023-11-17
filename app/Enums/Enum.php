<?php

namespace App\Enums;

abstract class Enum {

    protected const CASES = [];

    public string $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    /**
     * @return static[]
     */
    public static function from(string $value): Enum {
        $Instance = static::tryFrom($value);
        if (null === $Instance) {
            throw new \ValueError(sprintf('"%s" is not a valid backing value for enum "%s', $value, static::class));
        }

        return $Instance;
    }

    /**
     * @return static[]
     */
    public static function tryFrom(string $value): ?Enum {
        if (!in_array($value, static::CASES, true)) {
            return null;
        }

        static $instances = [];

        $key = static::class . '_' . $value;
        $Instance = &$instances[$key];
        if (null === $Instance) {
            $Instance = new static($value);
        }

        return $Instance;
    }

    /**
     * @return static[]
     */
    public static function cases(): array {
        $result = [];
        foreach (static::CASES as $value) {
            $result[$value] = static::from($value);
        }

        return $result;
    }
}
