<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LocalizationTest extends TestCase
{
    public function test_required_ui_translation_keys_are_available_in_both_locales(): void
    {
        $requiredKeys = [
            'click_to_enlarge',
            'gender_male',
            'gender_female',
            'visit_type_initial_consultation',
            'visit_type_follow_up',
            'backup_status_queued',
            'backup_status_completed',
            'backup_status_failed',
            'status_in_progress',
            'status_no_show',
            'day_mon',
            'module_staff',
            'module_auth',
            'event_login',
            'tooth_status_healthy',
            'tooth_1',
        ];

        $basePath = dirname(__DIR__, 1).'/../lang';

        foreach (['en', 'ar'] as $locale) {
            $messages = require $basePath.'/'.$locale.'/messages.php';

            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $messages, sprintf('Missing translation key "%s" in %s locale.', $key, $locale));
            }
        }
    }
}
