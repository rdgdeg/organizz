<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->longText('additional_info')->nullable()->after('description');
            $table->longText('recommendations')->nullable()->after('additional_info');
            $table->longText('practical_details')->nullable()->after('recommendations');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['additional_info', 'recommendations', 'practical_details']);
        });
    }
};
