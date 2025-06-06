<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
class SessionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    #[Test]
    private function list_all_sessions(): void
    {
        $response = $this->get('/api/user/sessions');
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    #[Test]
    private function delete_session(): void
    {
        $response = $this->delete('/api/user/sessions/1');
        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
