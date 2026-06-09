<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationAdditionalFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_requires_contact_number_and_address(): void
    {
        Mail::fake();

        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['contact_number', 'address']);
    }

    public function test_register_saves_contact_number_address_and_optional_profile_photo(): void
    {
        Mail::fake();

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'contact_number' => '09001234567',
            'address' => 'Butuan City',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('otp.verify'));

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertSame('09001234567', $user->contact_number);
        $this->assertSame('Butuan City', $user->address);
        $this->assertFalse((bool) $user->is_aaracc);
        $this->assertNull($user->profile_photo_path);
    }
}

