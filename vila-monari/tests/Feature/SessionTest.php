<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SessionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    #[Test]
    public function list_all_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->get('/api/v1/user/sessions');
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    #[Test]
    public function delete_session()
    {
        $user = User::factory()->create();
        $token = $user->createToken("TOKEN")->accessToken;
        $this->actingAs($user, 'sanctum');
        $response = $this->delete("/api/v1/user/sessions/{$token->id}");
        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
