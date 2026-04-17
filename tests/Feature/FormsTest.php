<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    // ── Auth pages ────────────────────────────────────────────────────────────

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_renders(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Password1!')]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'Password1!',
        ])->assertRedirect('/dashboard');
    }

    public function test_user_can_logout(): void
    {
        $this->actingAsUser();
        $this->post('/logout')->assertRedirect('/');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();
    }

    // ── Profile pages ─────────────────────────────────────────────────────────

    public function test_profile_page_renders(): void
    {
        $this->actingAsUser();
        $this->get('/profile')->assertStatus(200);
    }

    public function test_profile_can_be_updated(): void
    {
        $user = $this->actingAsUser();

        $this->patch('/profile', [
            'name'  => 'Updated Name',
            'email' => $user->email,
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
    }

    public function test_profile_update_requires_valid_email(): void
    {
        $this->actingAsUser();

        $this->patch('/profile', [
            'name'  => 'Test',
            'email' => 'not-an-email',
        ])->assertSessionHasErrors(['email']);
    }

    // ── Import form ───────────────────────────────────────────────────────────

    public function test_import_page_renders(): void
    {
        $this->actingAsUser();
        $this->get('/import')->assertStatus(200)->assertSee('Upload');
    }

    public function test_import_accepts_valid_csv(): void
    {
        Storage::fake('local');
        $this->actingAsUser();

        $file = UploadedFile::fake()->createWithContent(
            'transactions.csv',
            "date,amount,merchant,description\n2024-01-01,9.99,Netflix,Subscription"
        );

        $this->post('/import', ['csv_file' => $file])
             ->assertRedirect('/import')
             ->assertSessionHas('success');

        Storage::disk('local')->assertExists('imports/' . $file->hashName());
    }

    public function test_import_rejects_missing_file(): void
    {
        $this->actingAsUser();
        $this->post('/import', [])->assertSessionHasErrors(['csv_file']);
    }

    public function test_import_rejects_non_csv_file(): void
    {
        $this->actingAsUser();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->post('/import', ['csv_file' => $file])
             ->assertSessionHasErrors(['csv_file']);
    }

    public function test_import_rejects_oversized_file(): void
    {
        $this->actingAsUser();
        $file = UploadedFile::fake()->create('big.csv', 11000); // 11 MB > 10 MB limit

        $this->post('/import', ['csv_file' => $file])
             ->assertSessionHasErrors(['csv_file']);
    }

    // ── Transactions page ─────────────────────────────────────────────────────

    public function test_transactions_page_renders(): void
    {
        $this->actingAsUser();
        $this->get('/transactions')->assertStatus(200);
    }

    public function test_transactions_page_accepts_search_filter(): void
    {
        $this->actingAsUser();
        $this->get('/transactions?search=Netflix&date_from=2024-01-01&date_to=2024-12-31')
             ->assertStatus(200);
    }

    // ── Merchants page ────────────────────────────────────────────────────────

    public function test_merchants_page_renders(): void
    {
        $this->actingAsUser();
        $this->get('/merchants')->assertStatus(200);
    }

    public function test_merchants_page_accepts_search(): void
    {
        $this->actingAsUser();
        $this->get('/merchants?search=Netflix')->assertStatus(200);
    }

    // ── Unauthenticated redirects ─────────────────────────────────────────────

    public function test_import_requires_auth(): void
    {
        $this->get('/import')->assertRedirect('/login');
    }

    public function test_transactions_requires_auth(): void
    {
        $this->get('/transactions')->assertRedirect('/login');
    }

    public function test_merchants_requires_auth(): void
    {
        $this->get('/merchants')->assertRedirect('/login');
    }

    public function test_profile_requires_auth(): void
    {
        $this->get('/profile')->assertRedirect('/login');
    }
}
