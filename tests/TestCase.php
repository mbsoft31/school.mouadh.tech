<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

abstract class TestCase extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        // Deterministic time (adjust if tests expect "now")
        Carbon::setTestNow('2025-01-01 00:00:00');

        // Safe fakes; enable/disable per test if needed
        Storage::fake();   // or Storage::fake('local');
        Event::fake();
        Queue::fake();
        Mail::fake();

        // Optional: locale determinism if assertions compare messages
        app()->setLocale('en');
    }
}
