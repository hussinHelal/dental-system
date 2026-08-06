<?php

namespace Tests\Unit;

use App\Http\Requests\StaffRequest;
use PHPUnit\Framework\TestCase;

class StaffRequestTest extends TestCase
{
    public function test_working_hours_text_is_preserved_as_a_single_string(): void
    {
        $request = new StaffRequest();
        $method = new \ReflectionMethod(StaffRequest::class, 'normalizeWorkingHoursText');
        $method->setAccessible(true);

        $this->assertSame('Saturday 1pm-10pm, Sunday 1pm-10pm', $method->invoke($request, 'Saturday 1pm-10pm, Sunday 1pm-10pm'));
        $this->assertSame('Friday 9am-5pm', $method->invoke($request, 'Friday 9am-5pm'));
    }
}
