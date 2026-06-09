<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLegitimacyProof;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileLegitimacyProofDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_own_legitimacy_proof_and_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $path = 'legitimacy_proofs/test-proof.jpg';
        Storage::disk('public')->put($path, 'x');

        $proof = UserLegitimacyProof::create([
            'user_id' => $user->id,
            'file_path' => $path,
        ]);

        $this->actingAs($user);

        $this->delete(route('profile.legitimacy-proofs.destroy', $proof))
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseMissing('user_legitimacy_proofs', ['id' => $proof->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_cannot_delete_other_users_legitimacy_proof(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $attacker = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000002',
            'address' => 'Attacker Address',
        ]);

        $path = 'legitimacy_proofs/other-proof.jpg';
        Storage::disk('public')->put($path, 'x');

        $proof = UserLegitimacyProof::create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $this->actingAs($attacker);
        $this->delete(route('profile.legitimacy-proofs.destroy', $proof))->assertForbidden();

        $this->assertDatabaseHas('user_legitimacy_proofs', ['id' => $proof->id]);
        Storage::disk('public')->assertExists($path);
    }
}

