<?php

namespace Tests\Unit;

use App\Enums\StockType;
use PHPUnit\Framework\TestCase;

class StockTypeEnumTest extends TestCase
{
    public function test_stock_type_enum_has_correct_string_values(): void
    {
        $this->assertSame('entrada', StockType::Entrada->value);
        $this->assertSame('saída', StockType::Saida->value);
        $this->assertSame('ajuste', StockType::Ajuste->value);
    }

    public function test_can_create_stock_type_from_string_value(): void
    {
        $this->assertSame(StockType::Entrada, StockType::from('entrada'));
        $this->assertSame(StockType::Saida, StockType::from('saída'));
        $this->assertSame(StockType::Ajuste, StockType::from('ajuste'));
    }

    public function test_stock_type_enum_has_three_cases(): void
    {
        $this->assertCount(3, StockType::cases());
    }

    public function test_invalid_stock_type_value_throws_error(): void
    {
        $this->expectException(\ValueError::class);
        StockType::from('invalid');
    }
}
