<?php

namespace Tests\Feature;

use Tests\TestCase;

class TranslationKeysTest extends TestCase
{
    public function test_tooth_chart_side_labels_are_available_in_both_locales(): void
    {
        $this->assertSame('Left', __('messages.left', [], 'en'));
        $this->assertSame('Right', __('messages.right', [], 'en'));
        $this->assertSame('يسار', __('messages.left', [], 'ar'));
        $this->assertSame('يمين', __('messages.right', [], 'ar'));
    }
}
