<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

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
            'username' =>'monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ]);

        $userRequestBody = [
            'name' => 'Felipe Eduardo Monari',
            'username' =>'monari14',
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
            'username' => 'monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ];

        $response = $this->post('/api/v1/register', $request);
        $response->assertStatus(201);
    }

    #[Test]
    public function assert_json_structure_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get("/api/v1/users");
        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'username',
                'dados' => [
                    'follows' => [
                        'seguidores',
                        'seguindo'
                    ],
                    'posts' => [
                        'posts',
                        'likes'
                    ],
                    'comments' => [
                        'comments',
                        'likes'
                    ]
                ]
            ]
        ]);
    }

    #[Test]
    public function display_one_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get("/api/v1/users/{$user->id}");
        $response->assertStatus(200);
        $response->assertExactJsonStructure([
            "id",
            'username',
            'dados' => [
                "follows" => [
                    "seguidores",
                    "seguindo"
                ],
                "posts" => [
                    "posts",
                    "likes"
                ],
                "comments" => [
                    "comments",
                    "likes"
                ]
            ],
        ]);
    }

    #[Test]
    public function update_single_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $requestUpdateBody = [
            "name" => "Felipe Eduardo Monari",
            "username" => "monari",
            "email" => "felipeemonari@gmail.com",
        ];

        $responseNew = $this->put("/api/v1/user", $requestUpdateBody);
        $responseNew->assertStatus(200);
    }

    #[Test]
    public function delete_single_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->delete("/api/v1/user");
        $response->assertStatus(204);

    }

    #[Test]
    public function follow_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $user2 = User::factory()->create();

        $response = $this->post("/api/v1/user/{$user2->id}/follow");
        $response->assertStatus(200);
    }

    #[Test]
    public function unfollow_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $user2 = User::factory()->create();
        $this->post("/api/v1/user/{$user2->id}/follow");

        $response = $this->post("/api/v1/user/{$user2->id}/unfollow");

        $response->assertStatus(200);
    }

    #[Test]
    public function login_user()
    {
        $user = User::factory()->create([
            'name' => 'Felipe Monari',
            'username' =>'monari',
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ]);
        $userRequestBody = [
            'email' => 'felipeemonari@gmail.com',
            'password' => 'felipe',
        ];

        $request = $this->post('/api/v1/login', $userRequestBody);
        $request->assertStatus(200);
    }
    #[Test]
    public function logout_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->post('/api/v1/logout');
        $response->assertStatus(200);
    }
}
