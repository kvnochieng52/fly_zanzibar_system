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
        Schema::create('document_alert_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained('company_document_types');
            $table->integer('days_before_expiry');
            $table->string('alert_type');
            $table->string('notification_method');
            $table->json('recipients')->nullable();
            $table->string('message_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['document_type_id', 'days_before_expiry'], 'alert_thresholds_idx');
        });

        // Insert default alert thresholds
        DB::table('document_alert_thresholds')->insert([
            ['document_type_id' => 1, 'days_before_expiry' => 30, 'alert_type' => 'WARNING', 'notification_method' => 'EMAIL'],
            ['document_type_id' => 1, 'days_before_expiry' => 7, 'alert_type' => 'CRITICAL', 'notification_method' => 'EMAIL'],
            ['document_type_id' => 2, 'days_before_expiry' => 30, 'alert_type' => 'WARNING', 'notification_method' => 'EMAIL'],
            ['document_type_id' => 2, 'days_before_expiry' => 7, 'alert_type' => 'CRITICAL', 'notification_method' => 'EMAIL'],
            ['document_type_id' => 3, 'days_before_expiry' => 60, 'alert_type' => 'WARNING', 'notification_method' => 'EMAIL'],
            ['document_type_id' => 3, 'days_before_expiry' => 30, 'alert_type' => 'CRITICAL', 'notification_method' => 'EMAIL'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_alert_thresholds');
    }
};
