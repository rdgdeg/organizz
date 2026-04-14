<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('notify_organizer_on_registration');
            $table->boolean('waitlist_enabled')->default(true)->after('custom_fields');
            $table->unsignedSmallInteger('participant_edit_deadline_hours')->default(48)->after('waitlist_enabled');
            $table->boolean('matching_enabled')->default(true)->after('participant_edit_deadline_hours');
            $table->string('embed_token', 64)->nullable()->unique()->after('matching_enabled');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->json('custom_field_answers')->nullable()->after('phone');
            $table->boolean('waitlist')->default(false)->after('custom_field_answers');
            $table->unsignedSmallInteger('waitlist_position')->nullable()->after('waitlist');
            $table->timestamp('checked_in_at')->nullable()->after('reminder_sent_at');
            $table->string('preferred_reminder_channel', 16)->default('email')->after('checked_in_at');
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('max_volunteers');
        });

        Schema::create('event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 32);
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('participant_email_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->text('endpoint');
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('participant_email_tokens');
        Schema::dropIfExists('event_user');

        Schema::table('slots', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'custom_field_answers',
                'waitlist',
                'waitlist_position',
                'checked_in_at',
                'preferred_reminder_channel',
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'custom_fields',
                'waitlist_enabled',
                'participant_edit_deadline_hours',
                'matching_enabled',
                'embed_token',
            ]);
        });
    }
};
