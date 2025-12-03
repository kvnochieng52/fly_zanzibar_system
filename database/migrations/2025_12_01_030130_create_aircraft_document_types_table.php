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
        Schema::create('aircraft_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->json('required_fields')->nullable(); // Store required fields for each document type
            $table->boolean('requires_expiry')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default document types
        DB::table('aircraft_document_types')->insert([
            [
                'name' => 'Aircraft Registration',
                'code' => 'REG',
                'description' => 'Official aircraft registration certificate',
                'required_fields' => json_encode([
                    'registration_number' => 'Registration Number',
                    'issuing_authority' => 'Issuing Authority',
                    'issue_date' => 'Issue Date'
                ]),
                'requires_expiry' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Certificate of Airworthiness',
                'code' => 'COA',
                'description' => 'Certificate confirming aircraft meets airworthiness standards',
                'required_fields' => json_encode([
                    'certificate_number' => 'Certificate Number',
                    'issue_date' => 'Issue Date',
                    'expiry_date' => 'Expiry Date',
                    'issuing_authority' => 'Issuing Authority',
                    'certificate_type' => 'Type (Standard, Special, Experimental)'
                ]),
                'requires_expiry' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Radio License',
                'code' => 'RADIO',
                'description' => 'Aircraft radio station license',
                'required_fields' => json_encode([
                    'license_number' => 'License Number',
                    'call_sign' => 'Call Sign',
                    'issue_date' => 'Issue Date',
                    'expiry_date' => 'Expiry Date',
                    'issuing_authority' => 'Issuing Authority'
                ]),
                'requires_expiry' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'CRS',
                'code' => 'CRS',
                'description' => 'Certificate of Release to Service',
                'required_fields' => json_encode([
                    'crs_number' => 'Current CRS Number',
                    'issue_date' => 'Issue Date',
                    'maintenance_organization' => 'Maintenance Organization',
                    'next_scheduled_maintenance' => 'Next Scheduled Maintenance'
                ]),
                'requires_expiry' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'POH Approval Page',
                'code' => 'POH',
                'description' => 'Pilot Operating Handbook approval page',
                'required_fields' => json_encode([
                    'poh_revision_number' => 'POH Revision Number',
                    'approval_date' => 'Approval Date',
                    'approving_authority' => 'Approving Authority',
                    'last_update_date' => 'Last Update Date'
                ]),
                'requires_expiry' => false,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Weight and Balance',
                'code' => 'WB',
                'description' => 'Aircraft weight and balance documentation',
                'required_fields' => json_encode([
                    'document_date' => 'Document Date',
                    'basic_empty_weight' => 'Basic Empty Weight',
                    'cg_location' => 'CG Location',
                    'last_weight_check_date' => 'Last Weight Check Date',
                    'next_weight_check_due' => 'Next Weight Check Due'
                ]),
                'requires_expiry' => false,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Operations Specifications',
                'code' => 'OPSPECS',
                'description' => 'Operations specifications document',
                'required_fields' => json_encode([
                    'ops_spec_number' => 'OPs Spec Number',
                    'issue_date' => 'Issue Date',
                    'expiry_date' => 'Expiry Date',
                    'amendment_number' => 'Amendment Number',
                    'authorized_operations' => 'Authorized Operations'
                ]),
                'requires_expiry' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_document_types');
    }
};
