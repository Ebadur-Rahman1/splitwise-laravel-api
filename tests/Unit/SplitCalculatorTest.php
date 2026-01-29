<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SplitCalculator;

class SplitCalculatorTest extends TestCase
{
    public function test_it_splits_amount_equally()
    {
        $calculator = new SplitCalculator();

        $result = $calculator->splitEqually(300, 3);

        $this->assertCount(3, $result);
        $this->assertEquals(100, $result[0]);
        $this->assertEquals(100, $result[1]);
        $this->assertEquals(100, $result[2]);

        // Total should remain same
        $this->assertEquals(300, array_sum($result));
    }

    public function test_it_handles_rounding_correctly()
    {
        $calculator = new SplitCalculator();

        // 100 / 3 = 33.33, 33.33, 33.34
        $result = $calculator->splitEqually(100, 3);

        $this->assertCount(3, $result);

        // Total must match exactly
        $this->assertEquals(100, array_sum($result));

        // All values should be near equal
        $this->assertTrue(abs($result[0] - 33.33) <= 0.02);
        $this->assertTrue(abs($result[1] - 33.33) <= 0.02);
        $this->assertTrue(abs($result[2] - 33.33) <= 0.02);
    }

    public function test_it_throws_exception_for_invalid_member_count()
    {
        $this->expectException(\InvalidArgumentException::class);

        $calculator = new SplitCalculator();
        $calculator->splitEqually(100, 0);
    }
}
