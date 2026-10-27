<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\Text;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NewsSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_published_news_from_newest_to_oldest(): void
    {
        $category = Category::create(['name' => 'Технологии']);
        $author = User::factory()->create();

        $oldText = Text::create([
            'heading' => 'Старая новость',
            'body' => 'Старый текст',
            'category_id' => $category->id,
            'user_id' => $author->id,
        ]);
        $oldText->created_at = now()->subDay();
        $oldText->save();

        Text::create([
            'heading' => 'Новая новость',
            'body' => 'Новый текст',
            'category_id' => $category->id,
            'user_id' => $author->id,
        ]);

        Text::create([
            'heading' => 'Черновик',
            'body' => 'Не опубликован',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'published' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Новая новость', 'Старая новость'])
            ->assertDontSee('Черновик');
    }

    public function test_category_page_only_shows_published_texts_from_that_category(): void
    {
        $games = Category::create(['name' => 'Игры']);
        $technology = Category::create(['name' => 'Технологии']);
        $author = User::factory()->create();

        Text::create([
            'heading' => 'Новость об игре',
            'body' => 'Текст',
            'category_id' => $games->id,
            'user_id' => $author->id,
        ]);
        Text::create([
            'heading' => 'Новость о технологии',
            'body' => 'Текст',
            'category_id' => $technology->id,
            'user_id' => $author->id,
        ]);

        $this->get(route('categories.show', $games))
            ->assertOk()
            ->assertSee('Новость об игре')
            ->assertDontSee('Новость о технологии');
    }

    public function test_registration_stores_a_hashed_password_and_assigns_user_role(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'new_reader',
            'name' => 'Новый читатель',
            'email' => 'reader@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('home'));
        $user = User::where('username', 'new_reader')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertSame('user', $user->role->name);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_journalist_can_create_and_edit_only_their_own_texts(): void
    {
        $journalist = $this->createUserWithRole('journalist');
        $otherJournalist = $this->createUserWithRole('journalist');
        $category = Category::create(['name' => 'Игры']);
        $otherText = Text::create([
            'heading' => 'Чужая статья',
            'body' => 'Текст',
            'category_id' => $category->id,
            'user_id' => $otherJournalist->id,
        ]);

        $this->actingAs($journalist)
            ->get(route('journalist'))
            ->assertOk();

        $this->post(route('journalist.texts.store'), [
            'heading' => 'Моя статья',
            'body' => 'Мой текст',
            'category_id' => $category->id,
        ])->assertRedirect(route('journalist'));

        $this->assertDatabaseHas('texts', [
            'heading' => 'Моя статья',
            'user_id' => $journalist->id,
            'published' => true,
        ]);

        $myText = Text::where('heading', 'Моя статья')->firstOrFail();

        $this->put(route('journalist.texts.update', $myText), [
            'heading' => 'Моя обновленная статья',
            'body' => 'Обновленный текст',
            'category_id' => $category->id,
        ])->assertRedirect(route('journalist'));

        $this->assertDatabaseHas('texts', [
            'id' => $myText->id,
            'heading' => 'Моя обновленная статья',
        ]);

        $this->get(route('journalist.texts.edit', $otherText))->assertForbidden();
    }

    public function test_regular_user_cannot_open_journalist_or_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('journalist'))
            ->assertForbidden();

        $this->get(route('admin'))->assertForbidden();
    }

    public function test_admin_can_manage_articles_roles_and_blocked_users(): void
    {
        $admin = $this->createUserWithRole('admin');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Программирование']);
        $text = Text::create([
            'heading' => 'Первый заголовок',
            'body' => 'Текст',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin'))
            ->assertOk();

        $this->put(route('admin.texts.update', $text), [
            'heading' => 'Измененный заголовок',
            'body' => 'Обновленный текст',
            'category_id' => $category->id,
        ])->assertRedirect(route('admin'));

        $this->patch(route('admin.users.role', $user), [
            'role_id' => Role::where('name', 'journalist')->value('id'),
        ])->assertRedirect(route('admin'));

        $this->patch(route('admin.users.block', $user), [
            'is_blocked' => 1,
        ])->assertRedirect(route('admin'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_blocked' => true,
        ]);
        $this->assertDatabaseHas('texts', [
            'id' => $text->id,
            'heading' => 'Измененный заголовок',
        ]);

        $this->delete(route('admin.texts.delete', $text))
            ->assertRedirect(route('admin'));

        $this->assertDatabaseMissing('texts', ['id' => $text->id]);
    }

    public function test_blocked_user_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'username' => 'blocked_reader',
            'password' => 'secret-password',
        ]);
        $user->is_blocked = true;
        $user->save();

        $this->post(route('login'), [
            'username' => 'blocked_reader',
            'password' => 'secret-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    private function createUserWithRole(string $roleName): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $roleName)->value('id'),
        ]);
    }
}
