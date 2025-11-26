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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('employee_id', 20)->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->unsignedBigInteger('gender_id');
            $table->string('nationality', 100);

            // Employment Information
            $table->date('hire_date');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('status_id');
            $table->string('current_base', 100)->nullable(); // For crew members
            $table->decimal('salary', 10, 2)->nullable();

            // Contact Information
            $table->string('email', 150)->unique()->nullable();
            $table->string('phone_primary', 20)->nullable();
            $table->string('phone_secondary', 20)->nullable();

            // Address Information
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state_region', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Additional Information
            $table->string('profile_photo')->nullable();
            $table->text('notes')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->json('additional_info')->nullable(); // For flexible data storage

            // System fields
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            // Indexes and Foreign Keys
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('status_id')->references('id')->on('staff_statuses');

            $table->index(['employee_id']);
            $table->index(['status_id']);
            $table->index(['department_id', 'position_id']);
            $table->index(['hire_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
