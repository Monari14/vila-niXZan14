<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    #[Test]
    public function example(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function user_list(){
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
    }

    #[Test]
    public function create_user_email_duplicated() {
        $user = User::factory()->create([
            'name' => 'Felipe Monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ]);

        $userRequestBody = [
            'name' => 'Felipe Eduardo Monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ];
        $response = $this->post('/api/v1/register', $userRequestBody);
        $response->assertStatus(500);
    }

    #[Test]
    public function create_user(){
        $request = [
            'name' => 'Felipe Eduardo Monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ];

        $response = $this->post('/api/v1/register', $request);
        $response->assertStatus(201);
    }
}
