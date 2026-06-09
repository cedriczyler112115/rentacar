<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('faqs')->insert([
            [
                'question' => 'What are the steps on renting a vehicle?',
                'answer' => "Renting from Auto Amegos Rent-A-Car Co. is easy and it is the first in the Philippines to offer a seamless booking experience.\n\nPick a vehicle to rent and check out the estemated rental price.\nBook it by clicking the Book Now button and you have to pay the reservation fee or Downpayment Fee.\nWe support GCash, Visa, Mastercard and QRPH for the payment of reservation fee.\nThat's it! Remaining balance will be settled upon pick up of the vehicle. Easy!",
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What about the car washing?',
                'answer' => "Auto Amegos Rent-A-Car Co. will wash the car for you before pick up. Just ready the price for the following upon pick up:\n\nPHP 150 for Hatchbacks and Sedans.\nPHP 250 for MPVs, SUVs, Pickups and Vans.",
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What are the requirements to rent?',
                'answer' => "Requirement - Bring 2 Primary IDs\n\nPlease be reminded to bring your two primary IDs (including your driver's license) with you when you pick up the car.\nThe other primary ID presented will be handed to the assigned Carbnb employee and will be released as soon as you return the vehicle for security purposes.\n\nFriend, relative or driver is going to pick up?\n\nIf there are other individuals like a driver, friend or relative that will pick up the vehicle for you, please be advised that we will require:\n\nA duly signed Authorization Letter stating that the client who booked the vehicle is authorizing you to pick up the vehicle on their behalf.\nDriver's license and 1 valid ID (Primary ID) of the person who will pick up the vehicle.\nTwo (2) primary valid IDs of the client that is named after the booking.\nThe Carbnb team will contact the primary client on the day of the pick up of the vehicle for verification and security purposes.\nThis is a strict requirement and Carbnb staffs have the rights to deny the release of vehicle in cases of incomplete requirements.",
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How about the late return policy?',
                'answer' => "We charge PHP 300 pesos per hour maximum of 3 hours extension and subject to availability. Please return on time and estimate your travel time with the best of your ability considering the traffic we have in the Philippines. The failure of the client to return the car to Carbnb for whatever reason within 24 hours from the scheduled deadline shall lead to a penalty of PHP 500,000.00 in addition to the acquisition cost of the vehicle and other fees.",
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

