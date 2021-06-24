<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Tests\Supports;

use PHPUnit\Framework\TestCase;
use Ziswapp\Payment\Supports\Str;

final class StrTest extends TestCase
{
    public function testCanConvertToCamelCase(): void
    {
        $value = 'this_is_snake_case';

        $newValue = Str::camelCase($value);

        $this->assertSame('thisIsSnakeCase', $newValue);
    }

    public function testMakeLowerString(): void
    {
        $value = 'THIS is UpperCase';

        $newValue = Str::lower($value);

        $this->assertSame('this is uppercase', $newValue);
    }

    public function testMakeSnakeCaeString(): void
    {
        $value = 'THIS is SnakeCase';

        $newValue = Str::snake($value);

        $this->assertSame('t_h_i_s_is_snake_case', $newValue);
    }
}
