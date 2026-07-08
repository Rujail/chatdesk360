<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users table (Admins + Agents)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['admin', 'agent'])->default('agent');
            $table->enum('status', ['accepting_chats', 'not_accepting_chats', 'offline', 'busy'])->default('offline');
            $table->integer('concurrent_chat_limit')->default(6);
            $table->string('groups')->nullable();                    // comma separated e.g. "Sales,Support"
            $table->integer('total_chats_handled')->default(0);
            $table->integer('goals_achieved')->default(0);
            $table->decimal('avg_satisfaction', 5, 2)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        // 2. Visitors / Traffic table
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id')->unique();                 // session id / fingerprint / uuid
            $table->string('ip_address')->index();
            $table->string('name')->nullable()->default('Unnamed Customer');
            $table->string('email')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('referrer_url')->nullable();             // came from
            $table->string('device_type')->nullable();              // mobile/desktop/tablet
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->nullable();
            $table->integer('visit_count')->default(1);
            $table->string('last_page_url')->nullable();
            $table->integer('chat_count')->default(0);
            $table->timestamps();
        });

        // 3. Chats table
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('chat_identifier')->unique();            // public chat id shown to customer
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['active', 'ended', 'transferred', 'closed_by_agent', 'closed_by_visitor'])->default('active');
            $table->integer('duration_seconds')->nullable();
            $table->decimal('satisfaction_rating', 3, 1)->nullable(); // 1.0 to 5.0
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        // 4. Messages table (archival + searchable)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['agent', 'visitor', 'system']);
            $table->unsignedBigInteger('sender_id')->nullable();     // user_id if agent, null if visitor
            $table->text('content');
            $table->string('attachment_url')->nullable();
            $table->string('attachment_type')->nullable();           // image, pdf, payment_link, etc
            $table->timestamp('sent_at')->useCurrent();
            $table->boolean('is_read_by_agent')->default(false);
            $table->boolean('is_read_by_visitor')->default(false);
            $table->timestamps();
        });

        // 5. Banned Customers table
        Schema::create('banned_customers', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->index();
            $table->string('visitor_id')->nullable()->index();
            $table->timestamp('banned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('chat_id')->nullable()->constrained()->onDelete('set null');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 6. Canned Responses / Shortcuts table
        Schema::create('shortcuts', function (Blueprint $table) {
            $table->id();
            $table->string('shortcut')->unique();                   // #greeting, #price, etc
            $table->text('response_text');
            $table->string('tags')->nullable();                     // comma separated, max 10
            $table->boolean('auto_apply_tags')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 7. Widget Settings (single row usually)
        Schema::create('widget_settings', function (Blueprint $table) {
            $table->id();
            $table->json('appearance')->nullable();                 // bubble/bar, theme, colors...
            $table->json('position')->nullable();                   // side, spacing...
            $table->json('tweaks')->nullable();                     // logo, sound, rating, transcript...
            $table->string('eye_catcher_image_url')->nullable();
            $table->timestamps();
        });

        // Optional: default widget settings row
        \DB::table('widget_settings')->insert([
            'appearance' => json_encode([
                'minimized_type' => 'bubble',
                'theme' => 'light',
                'primary_color' => '#2366ff',
            ]),
            'position' => json_encode([
                'side' => 'right',
                'side_spacing' => 24,
                'bottom_spacing' => 24,
            ]),
            'tweaks' => json_encode([
                'show_logo' => true,
                'show_agent_photo' => true,
                'enable_sound' => true,
                'allow_rating' => true,
                'allow_transcript' => true,
                'white_label' => false,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_settings');
        Schema::dropIfExists('shortcuts');
        Schema::dropIfExists('banned_customers');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('users');
    }
};