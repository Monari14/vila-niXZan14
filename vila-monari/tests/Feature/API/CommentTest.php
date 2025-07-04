<?php
namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Tests\TestCase;
use Storage;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;
    private Comment $comment;

    #[Test]
    public function create_comment()
    {
        // Criando um usuário pois o post precisa de um usuário autenticado
        $user = User::factory()->create();

        // Isso basicamente já loga o user
        $this->actingAs($user, 'sanctum');

        $post = Post::factory()->create();
        $requestBody = [
            'content' => 'texto texto texto',
        ];
        // Faz a requisição para a rota
        $response = $this->post("/api/v1/comments/{$post->id}/posts", $requestBody);


        // Espera 200 como status
        $response->assertStatus(200);

        $this->comment = Comment::latest()->first();
    }

    #[Test]
    public function list_zero_comments()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->get('/api/v1/comments');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    #[Test]
    public function list_n_comments()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Post::factory()->create();

        Comment::factory(10)->create();

        $response = $this->get('/api/v1/posts');

        $response->assertStatus(200);
    }

    #[Test]
    public function display_one_comment(): void
    {
        $this->create_comment();
        $response = $this->get("/api/v1/comments/{$this->comment->id}");

        $response->assertStatus(200);
        $response->assertExactJsonStructure([
            "comment" => [
                "id",
                "user_id",
                "post_id",
                "content",
                "created_at",
                "updated_at"
            ],
            "nLikes",
        ]);
    }

    #[Test]
    public function display_wrong_comment(): void
    {
        $this->display_one_comment();

        $response = $this->get("/api/v1/comments/SOME_WRONG_ID");

        $response->assertStatus(404);
    }

    #[Test]
    public function create_comment_through_api(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $post = Post::factory()->create();

        $requestBody = [
            'content' => 'Minha primeira mensagem escrita aqui.',
        ];

        $response = $this->post("/api/v1/comments/{$post->id}/posts", $requestBody);
        $response->assertStatus(200);

        $responseBody = $response->json();

        $response->assertExactJson([
            'comment' => [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'content' => $requestBody['content'],
                'updated_at' => $responseBody['comment']['updated_at'],
                'created_at' => $responseBody['comment']['created_at'],
                'id' => $responseBody['comment']['id'],
            ],
            'message' => 'Comentário adicionado com sucesso!',
        ]);

        $this->assertDatabaseCount('comments', 1);

        $this->comment = Comment::findOrFail($responseBody['comment']['id']);
    }

    #[Test]
    public function delete_single_comment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comment = Comment::factory()->create();

        $responseWrongDeleted = $this->delete("/api/v1/comments/SOME_WRONG_ID");
        $responseWrongDeleted->assertStatus(404);

        $this->assertDatabaseCount($comment->getTable(), 1);

        $responseDeleted = $this->delete("/api/v1/comments/{$comment->id}");
        $responseDeleted->assertStatus(200);

        $responseAlreadyDeleted = $this->delete("/api/v1/comments/{$comment->id}");
        $responseAlreadyDeleted->assertStatus(404);

        $this->assertDatabaseEmpty($comment->getTable());
    }

    #[Test]
    public function create_comment_through_api_and_delete_it(): void
    {
        $this->create_comment_through_api();
        $this->assertInstanceOf(Comment::class, $this->comment);

        $responseWrongDeleted = $this->delete("/api/v1/comments/SOME_WRONG_ID");
        $responseWrongDeleted->assertStatus(404);

        $this->assertDatabaseCount($this->comment->getTable(), 1);

        $responseDeleted = $this->delete("/api/v1/comments/{$this->comment->id}");
        $responseDeleted->assertStatus(200);

        $responseAlreadyDeleted = $this->delete("/api/v1/comments/{$this->comment->id}");
        $responseAlreadyDeleted->assertStatus(404);

        $this->assertDatabaseEmpty($this->comment->getTable());
    }

    #[Test]
    public function like_comment(): void
    {
        $this->create_comment_through_api();
        $this->assertInstanceOf(Comment::class, $this->comment);

        $response = $this->post("/api/v1/comments/{$this->comment->id}/like");
        $response->assertStatus(200);

        $this->comment = Comment::findOrFail($this->comment->id);
    }
    #[Test]
    public function dislike_comment(): void
    {
        $this->like_comment();
        $this->assertInstanceOf(Comment::class, $this->comment);
        $response = $this->post("/api/v1/comments/{$this->comment->id}/dislike");
        $response->assertStatus(200);
    }
}
