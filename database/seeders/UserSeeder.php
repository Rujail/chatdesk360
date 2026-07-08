<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@chatdesk360.com',
                'email_verified_at' => now(),
                'password' => Hash::make('w_J3&(08JHvZ'),
                'remember_token' => Str::random(10),
                'role' => 'admin',
                'status' => 'offline',
                'concurrent_chat_limit' => 10,
                'groups' => 'management,support',
                'total_chats_handled' => 0,
                'goals_achieved' => 0,
                'avg_satisfaction' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agent One',
                'email' => 'agent1@chatdesk360.com',
                'email_verified_at' => now(),
                'password' => Hash::make('!bEo9I02DQmo'),
                'remember_token' => Str::random(10),
                'role' => 'agent',
                'status' => 'accepting_chats',
                'concurrent_chat_limit' => 6,
                'groups' => 'sales',
                'total_chats_handled' => 50,
                'goals_achieved' => 10,
                'avg_satisfaction' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}