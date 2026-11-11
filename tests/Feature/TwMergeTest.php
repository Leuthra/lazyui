<?php

namespace LazyUI\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use LazyUI\LazyUI;
use LazyUI\Tests\TestCase;

class TwMergeTest extends TestCase
{
    public function test_later_classes_win(): void
    {
        $this->assertSame('py-2 px-4', LazyUI::twMerge('px-2 py-2', 'px-4'));
    }

    public function test_conditional_and_nested_arrays(): void
    {
        $this->assertSame('a c d', LazyUI::twMerge(['a', 'b' => false, 'c' => true], ['class' => ['d']]));
    }

    public function test_blade_directive(): void
    {
        $this->assertSame('p-4', trim(Blade::render("@twMerge('p-2', 'p-4')")));
    }
}
