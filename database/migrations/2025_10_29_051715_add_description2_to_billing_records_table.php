<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_records', function (Blueprint $table) {
            $table->text('description2')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('billing_records', function (Blueprint $table) {
            $table->dropColumn('description2');
        });
    }
};
