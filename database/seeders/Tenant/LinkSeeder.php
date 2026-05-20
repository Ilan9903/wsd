<?php

namespace Database\Seeders\Tenant;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Link::factory(1)->create(['user_id' => $user->id, 'expired_at' => now()->addDays(15)]);
            Link::factory(1)->withoutPassword()->create(['user_id' => $user->id, 'expired_at' => now()->addDays(15)]);
            Link::factory(1)->expired()->create(['user_id' => $user->id]);
            Link::factory(1)->expired()->create(['is_active' => true]);
            Link::factory(1)->expired()->create(['has_receipt' => true]);
            Link::factory(1)->expired()->create(['has_watermark' => true]);
            Link::factory(1)->expired()->create(['recipients_email_addresses' => []]);
            Link::factory(1)->expired()->create(['message_subject' => 'Your link']);
            Link::factory(1)->expired()->create(['message' => 'This link is available.']);
            Link::factory(1)->expired()->create(['src_folder_id' => '']);
        });
    }
}
