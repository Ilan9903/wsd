<?php

namespace Database\Seeders\Tenant;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            $contact = Contact::factory()->create();
            $contact->users()->attach($user->id);
        });
    }
}
