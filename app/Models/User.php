<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'contact_number', 'address', 'about_owner', 'is_aaracc', 'profile_photo_path', 'password', 'google_id', 'otp_code', 'otp_expires_at'])]
#[Hidden(['password', 'remember_token', 'otp_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_aaracc' => 'boolean',
        ];
    }

    public function serviceFeePayments()
    {
        return $this->hasMany(ServiceFeePayment::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'owner_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function legitimacyProofs()
    {
        return $this->hasMany(UserLegitimacyProof::class);
    }

    public function capital()
    {
        return $this->hasOne(MemberCapital::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    public function loanApprovals()
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }
}
