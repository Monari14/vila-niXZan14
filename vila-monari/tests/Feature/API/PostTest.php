<?php
namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Tests\TestCase;
use Storage;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    #[Test]
    public function create_post()
    {
        // O que o post espera receber
        $requestBody = [
            'content' => 'texto texto texto',
            'image' => null,
        ];

        // Criando um usuário pois o post precisa de um usuário autenticado
        $user = User::factory()->create();

        // Isso basicamente já loga o user
        $this->actingAs($user, 'sanctum');

        // Faz a requisição para a rota
        $response = $this->post('/api/v1/posts', $requestBody);

        // Espera 201 como status
        $response->assertStatus(201);

    }

    #[Test]
    public function list_zero_posts()
    {
        $response = $this->get('/api/v1/posts');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    #[Test]
    public function list_n_posts()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $n = 10;
        Post::factory($n)->create();

        $response = $this->get('/api/v1/posts');

        $response->assertStatus(200);
        $response->assertJsonCount($n);
    }

    #[Test]
    public function display_one_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->post = Post::factory()->create();

        $response = $this->get("/api/v1/posts/{$this->post->id}");

        $response->assertStatus(200);
        $response->assertJson($this->post->toArray());
    }

    #[Test]
    public function display_wrong_post(): void
    {
        $this->display_one_post();

        $response = $this->get("/api/v1/posts/SOME_WRONG_ID");

        $response->assertStatus(404);
    }

    #[Test]
    public function create_post_through_api(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $requestBody = [
            'content' => 'Minha primeira mensagem escrita aqui.',
        ];

        $response = $this->post('/api/v1/posts', $requestBody);
        $response->assertStatus(201);

        $responseBody = $response->json();

        $this->assertIsInt($responseBody['id']);

        $response->assertSimilarJson([
            'id' => $responseBody['id'],
            'user_id' => $user->id,
            'content' => $requestBody['content'],
            'created_at' => $responseBody['created_at'],
            'updated_at' => $responseBody['updated_at'],
        ]);

        /**
         * Must return the same POST above
         */
        $response = $this->get("/api/v1/posts/{$responseBody['id']}");
        $response->assertStatus(200);

        $responseBody = $response->json();

        $this->assertIsInt($responseBody['id']);
        $this->assertLessThanOrEqual(255, strlen($responseBody['image']));

        $response->assertExactJson([
            'id' => $responseBody['id'],
            'content' => $requestBody['content'],
            'user_id' => $user->id,
            'image' => null,
            'created_at' => $responseBody['created_at'],
            'updated_at' => $responseBody['updated_at'],
        ]);

        $this->assertDatabaseCount('posts', 1);

        $this->post = (new Post())->forceFill($responseBody, true);
    }

    #[Test]
    public function create_post_through_api_with_image(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $requestBody = [
            'content' => 'Minha primeira mensagem com imagem anexada.',
            'image' => UploadedFile::fake()->image('post.jpg', 256, 256),
        ];

        $response = $this->post('/api/v1/posts', $requestBody);
        $response->assertStatus(201);

        Storage::assertExists("images/{$requestBody['image']->hashName()}");
    }

    #[Test]
    public function update_single_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->post = Post::factory()->create();
        $this->assertInstanceOf(Post::class, $this->post);

        $responseOld = $this->get("/api/v1/posts/{$this->post->id}");
        $responseOld->assertStatus(200);
        $responseOldBody = $responseOld->json();

        $requestUpdateBody = [
            'content' => 'Uma mensagem atualizada.'
        ];

        $this->assertNotEquals(
            $requestUpdateBody['content'],
            $responseOldBody['content']
        );

        $responseWrongUpdate = $this->put("/api/v1/posts/SOME_WRONG_ID", $requestUpdateBody);
        $responseWrongUpdate->assertStatus(404);

        $responseNew = $this->put("/api/v1/posts/{$this->post->id}", $requestUpdateBody);
        $responseNew->assertStatus(200);

        $responseNewBody = $responseNew->json();
        $this->assertEquals(
            $requestUpdateBody['content'],
            $responseNewBody['content']
        );
    }

    #[Test]
    public function delete_single_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->post = Post::factory()->create();

        $responseWrongDeleted = $this->delete("/api/v1/posts/SOME_WRONG_ID");
        $responseWrongDeleted->assertStatus(404);

        $this->assertDatabaseCount($this->post->getTable(), 1);

        $responseDeleted = $this->delete("/api/v1/posts/{$this->post->id}");
        $responseDeleted->assertStatus(200);

        $responseAlreadyDeleted = $this->delete("/api/v1/posts/{$this->post->id}");
        $responseAlreadyDeleted->assertStatus(404);

        $this->assertDatabaseEmpty($this->post->getTable());
    }

    #[Test]
    public function create_post_through_api_and_delete_it(): void
    {
        $this->create_post_through_api();
        $this->assertInstanceOf(Post::class, $this->post);

        $responseWrongDeleted = $this->delete("/api/v1/posts/SOME_WRONG_ID");
        $responseWrongDeleted->assertStatus(404);

        $this->assertDatabaseCount($this->post->getTable(), 1);

        $responseDeleted = $this->delete("/api/v1/posts/{$this->post->id}");
        $responseDeleted->assertStatus(200);

        $responseAlreadyDeleted = $this->delete("/api/v1/posts/{$this->post->id}");
        $responseAlreadyDeleted->assertStatus(404);

        $this->assertDatabaseEmpty($this->post->getTable());
    }
}
