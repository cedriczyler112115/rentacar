<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLegitimacyProof;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileLegitimacyUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_legitimacy_proofs_and_save_about_owner(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Owner Address',
        ]);

        $this->actingAs($user);

        $res = $this->patch(route('profile.update'), [
            'name' => 'Owner Name',
            'email' => 'hacker@example.com',
            'contact_number' => '09000000000',
            'address' => 'Owner Address',
            'about_owner' => '<p>Hello</p>',
            'legitimacy_form' => '1',
            'legitimacy_terms' => '1',
            'legitimacy_proofs' => [
                UploadedFile::fake()->image('proof1.jpg')->size(1024),
                UploadedFile::fake()->image('proof2.png')->size(512),
            ],
        ]);

        $res->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('owner@example.com', $user->email);
        $this->assertSame('<p>Hello</p>', $user->about_owner);

        $this->assertSame(2, UserLegitimacyProof::query()->where('user_id', $user->id)->count());
        $paths = UserLegitimacyProof::query()->where('user_id', $user->id)->pluck('file_path')->all();
        foreach ($paths as $p) {
            Storage::disk('public')->assertExists($p);
        }
    }

    public function test_legitimacy_upload_rejects_more_than_15_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Owner Address',
        ]);

        $this->actingAs($user);

        $files = [];
        for ($i = 0; $i < 16; $i++) {
            $files[] = UploadedFile::fake()->image('proof' . $i . '.jpg')->size(200);
        }

        $res = $this->from(route('profile.edit'))->patch(route('profile.update'), [
            'name' => $user->name,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            'legitimacy_form' => '1',
            'legitimacy_terms' => '1',
            'legitimacy_proofs' => $files,
        ]);

        $res->assertRedirect(route('profile.edit'));
        $res->assertSessionHasErrors(['legitimacy_proofs']);
        $this->assertSame(0, UserLegitimacyProof::query()->count());
    }

    public function test_legitimacy_upload_rejects_total_more_than_15_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Owner Address',
        ]);

        $this->actingAs($user);

        $files10 = [];
        for ($i = 0; $i < 10; $i++) {
            $files10[] = UploadedFile::fake()->image('p' . $i . '.jpg')->size(200);
        }
        $this->patch(route('profile.update'), [
            'name' => $user->name,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            'legitimacy_form' => '1',
            'legitimacy_terms' => '1',
            'legitimacy_proofs' => $files10,
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame(10, UserLegitimacyProof::query()->where('user_id', $user->id)->count());

        $files6 = [];
        for ($i = 0; $i < 6; $i++) {
            $files6[] = UploadedFile::fake()->image('x' . $i . '.jpg')->size(200);
        }

        $res = $this->from(route('profile.edit'))->patch(route('profile.update'), [
            'name' => $user->name,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            'legitimacy_form' => '1',
            'legitimacy_terms' => '1',
            'legitimacy_proofs' => $files6,
        ]);

        $res->assertRedirect(route('profile.edit'));
        $res->assertSessionHasErrors(['legitimacy_proofs']);
        $this->assertSame(10, UserLegitimacyProof::query()->where('user_id', $user->id)->count());
    }
}
