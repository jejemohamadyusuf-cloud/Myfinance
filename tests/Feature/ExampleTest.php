<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }

    public function test_it_runs_a_database_ping_when_called_with_the_cron_secret(): void
    {
        config(['services.cron.secret' => 'test-secret']);

        $this->withToken('test-secret')
            ->get('/cron/keep-alive')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_it_rejects_requests_without_the_cron_secret(): void
    {
        config(['services.cron.secret' => 'test-secret']);

        $this->get('/cron/keep-alive')->assertUnauthorized();
    }

    public function test_it_fails_closed_when_the_cron_secret_is_not_configured(): void
    {
        config(['services.cron.secret' => '']);

        $this->withToken('test-secret')
            ->get('/cron/keep-alive')
            ->assertServiceUnavailable();
    }
}
