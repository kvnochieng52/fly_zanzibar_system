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
        Schema::create('company_document_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default document statuses
        DB::table('company_document_statuses')->insert([
            ['name' => 'Active', 'code' => 'ACTIVE', 'description' => 'Document is current and valid', 'color' => '#10B981'],
            ['name' => 'Expired', 'code' => 'EXPIRED', 'description' => 'Document has expired', 'color' => '#EF4444'],
            ['name' => 'Expiring Soon', 'code' => 'EXPIRING_SOON', 'description' => 'Document will expire soon', 'color' => '#F59E0B'],
            ['name' => 'Under Review', 'code' => 'UNDER_REVIEW', 'description' => 'Document is being reviewed', 'color' => '#3B82F6'],
            ['name' => 'Pending Renewal', 'code' => 'PENDING_RENEWAL', 'description' => 'Document renewal is in progress', 'color' => '#8B5CF6'],
            ['name' => 'Archived', 'code' => 'ARCHIVED', 'description' => 'Document has been archived', 'color' => '#6B7280'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_document_statuses');
    }
};
