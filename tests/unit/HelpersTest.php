<?php

namespace unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    private const HAYSTACK = ['Word1', 'word2', 'word 3', 'Word 4',];

    public function testInArrayCiReturnsTrue(): void
    {
        $this->assertTrue(in_array_ci('word1', self::HAYSTACK));
        $this->assertTrue(in_array_ci('Word1', self::HAYSTACK));
        $this->assertTrue(in_array_ci('WORD2', self::HAYSTACK));
    }

    public function testInArrayCiReturnsFalse(): void
    {
        $this->assertFalse(in_array_ci('Word3', self::HAYSTACK));
        $this->assertFalse(in_array_ci('Word4', self::HAYSTACK));
    }
}