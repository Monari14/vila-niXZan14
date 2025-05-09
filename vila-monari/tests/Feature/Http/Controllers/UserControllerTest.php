<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }
    #[Test]
    public function user_list(){
        $response = $this->get('/api/users');
        $response->assertStatus(200);

    }

    #[Test]
    public function create_user_email_duplicated() {
        User::factory()->withEmail('felipeemonari@gmail.com')->create();

        $userRequestBody = [
            'name' => 'Felipe Eduardo Monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'senha',
        ];
        $response = $this->post('/api/users', $userRequestBody);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    #[Test]
    public function create_user(){
        User::create([
            'name' => 'Felipe Eduardo Monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'senha',
        ]);

        $response = $this->post('/api/users');
        $response->assertStatus(200);
    }
}
