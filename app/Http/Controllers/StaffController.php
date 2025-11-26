<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use App\Models\Position;
use App\Models\Gender;
use App\Models\StaffStatus;
use App\Models\Country;
use App\Models\EmploymentType;
use App\Models\WorkLocation;
use App\Models\Qualification;
use App\Models\IdentificationType;
use App\Models\LicenseType;
use App\Models\LicenseStatus;
use App\Models\MedicalClass;
use App\Models\MedicalStatus;
use App\Models\ProficiencyType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Staff::with(['department', 'position', 'status', 'gender'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('type')) {
            if ($request->type === 'crew') {
                $query->crew();
            } elseif ($request->type === 'staff') {
                $query->staff();
            }
        }

        $staff = $query->paginate(15)->withQueryString();

        return Inertia::render('Staff/Index', [
            'staff' => $staff,
            'departments' => Department::where('is_active', true)->get(),
            'statuses' => StaffStatus::where('is_active', true)->get(),
            'filters' => $request->only(['search', 'department', 'status', 'type']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Staff/Create', [
            'departments' => Department::where('is_active', true)->get(),
            'positions' => Position::with('department')->where('is_active', true)->get(),
            'genders' => Gender::where('is_active', true)->get(),
            'statuses' => StaffStatus::where('is_active', true)->get(),
            'countries' => Country::active()->orderBy('name')->get(),
            'employmentTypes' => EmploymentType::active()->get(),
            'workLocations' => WorkLocation::active()->get(),
            'qualifications' => Qualification::active()->get(),
            'identificationTypes' => IdentificationType::active()->orderByName()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'profile_photo' => 'nullable|string', // Path from AJAX upload
            'date_of_birth' => 'required|date|before:today',
            'gender_id' => 'required|exists:genders,id',
            'country_id' => 'required|exists:countries,id',
            'hire_date' => 'required|date',

            // Employment Information
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status_id' => 'required|exists:staff_statuses,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'current_base' => 'nullable|string|max:100',
            'work_location_id' => 'nullable|exists:work_locations,id',
            'supervisor_name' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',

            // Contact Information
            'email' => 'nullable|email|max:150|unique:staff,email',
            'phone_primary' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state_region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',

            // Next of Kin Information
            'next_of_kin_name' => 'nullable|string|max:100',
            'next_of_kin_phone' => 'nullable|string|max:20',
            'next_of_kin_email' => 'nullable|email|max:150',
            'next_of_kin_relationship' => 'nullable|string|max:50',

            // Documents & Education
            'identification_type' => 'nullable|string|max:50', // Keep for backward compatibility
            'identification_type_id' => 'nullable|exists:identification_types,id',
            'identification_number' => 'nullable|string|max:100',
            'identification_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'highest_qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_name' => 'nullable|string|max:150',
            'institution_name' => 'nullable|string|max:150',
            'graduation_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'additional_info' => 'nullable|string',
        ]);

        // Generate staff number automatically
        $validated['staff_number'] = Staff::generateStaffNumber();

        // Profile photo is already handled by AJAX upload and path is provided
        // No additional processing needed as validated['profile_photo'] already contains the path

        $staff = Staff::create($validated);

        return redirect()->route('staff.show', $staff)
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff): Response
    {
        $staff->load([
            'department',
            'position',
            'status',
            'gender',
            'country',
            'employmentType',
            'workLocation',
            'highestQualification',
            'identificationType',
            'employmentContracts' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'currentContract',
            // Aviation certification relationships
            'licenses.licenseType',
            'licenses.licenseStatus',
            'medicalCertificates.medicalClass',
            'medicalCertificates.medicalStatus',
            'proficiencies.proficiencyType',
            'typeRatings'
        ]);

        return Inertia::render('Staff/Show', [
            'staff' => $staff,
            'licenseTypes' => LicenseType::active()->orderBy('name')->get(),
            'licenseStatuses' => LicenseStatus::active()->orderBy('name')->get(),
            'medicalClasses' => MedicalClass::active()->orderBy('name')->get(),
            'medicalStatuses' => MedicalStatus::active()->orderBy('name')->get(),
            'proficiencyTypes' => ProficiencyType::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff): Response
    {
        // Load relationships to ensure all data is available
        $staff->load([
            'department',
            'position',
            'status',
            'gender',
            'country',
            'employmentType',
            'workLocation',
            'highestQualification',
            'identificationType'
        ]);

        return Inertia::render('Staff/Edit', [
            'staff' => $staff,
            'departments' => Department::where('is_active', true)->get(),
            'positions' => Position::with('department')->where('is_active', true)->get(),
            'genders' => Gender::where('is_active', true)->get(),
            'statuses' => StaffStatus::where('is_active', true)->get(),
            'countries' => Country::active()->orderBy('name')->get(),
            'employmentTypes' => EmploymentType::active()->get(),
            'workLocations' => WorkLocation::active()->get(),
            'qualifications' => Qualification::active()->get(),
            'identificationTypes' => IdentificationType::active()->orderByName()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'profile_photo' => 'nullable|string', // Path from AJAX upload or existing path
            'date_of_birth' => 'required|date|before:today',
            'gender_id' => 'required|exists:genders,id',
            'country_id' => 'required|exists:countries,id',
            'hire_date' => 'required|date',

            // Employment Information
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status_id' => 'required|exists:staff_statuses,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'current_base' => 'nullable|string|max:100',
            'work_location_id' => 'nullable|exists:work_locations,id',
            'supervisor_name' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',

            // Contact Information
            'email' => 'nullable|email|max:150|unique:staff,email,' . $staff->id,
            'phone_primary' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state_region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',

            // Next of Kin Information
            'next_of_kin_name' => 'nullable|string|max:100',
            'next_of_kin_phone' => 'nullable|string|max:20',
            'next_of_kin_email' => 'nullable|email|max:150',
            'next_of_kin_relationship' => 'nullable|string|max:50',

            // Documents & Education
            'identification_type' => 'nullable|string|max:50', // Keep for backward compatibility
            'identification_type_id' => 'nullable|exists:identification_types,id',
            'identification_number' => 'nullable|string|max:100',
            'identification_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'highest_qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_name' => 'nullable|string|max:150',
            'institution_name' => 'nullable|string|max:150',
            'graduation_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'additional_info' => 'nullable|string',
        ]);

        // Profile photo is already handled by AJAX upload and path is provided
        // No additional processing needed as validated['profile_photo'] already contains the path

        $staff->update($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Handle AJAX photo upload
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        try {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'message' => 'Photo uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload employment contract for staff member
     */
    public function uploadContract(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'contract_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'contract_type' => 'required|in:fixed_term,permanent,probation,temporary',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            $contractFile = $request->file('contract_file');
            $fileName = time() . '_' . $staff->id . '_contract.' . $contractFile->getClientOriginalExtension();
            $path = $contractFile->storeAs('contracts', $fileName, 'public');

            // Create employment contract record
            $contract = $staff->employmentContracts()->create([
                'contract_file' => $path,
                'contract_type' => $validated['contract_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'notes' => $validated['notes'],
                'is_current' => $request->boolean('is_current', true),
                'uploaded_by' => auth()->user()->name ?? 'System',
                'status' => 'pending' // Will be auto-updated by the model boot method
            ]);

            return response()->json([
                'success' => true,
                'contract' => $contract,
                'message' => 'Contract uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contract upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new license for staff member
     */
    public function storeLicense(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'license_number' => 'required|string|max:100',
            'issuing_authority' => 'required|string|max:150',
            'license_status_id' => 'required|exists:license_statuses,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'restrictions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_license.' . $documentFile->getClientOriginalExtension();
                $documentPath = $documentFile->storeAs('licenses', $fileName, 'public');
            }

            // Create license record
            $license = $staff->licenses()->create([
                'license_type_id' => $validated['license_type_id'],
                'license_number' => $validated['license_number'],
                'issuing_authority' => $validated['issuing_authority'],
                'license_status_id' => $validated['license_status_id'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'restrictions' => $validated['restrictions'],
                'notes' => $validated['notes'],
                'document_file' => $documentPath,
                'is_current' => $request->boolean('is_current', true)
            ]);

            return response()->json([
                'success' => true,
                'license' => $license->load(['licenseType', 'licenseStatus']),
                'message' => 'License added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'License creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing license for staff member
     */
    public function updateLicense(Request $request, Staff $staff, $licenseId)
    {
        $license = $staff->licenses()->findOrFail($licenseId);

        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'license_number' => 'required|string|max:100',
            'issuing_authority' => 'required|string|max:150',
            'license_status_id' => 'required|exists:license_statuses,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'restrictions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_license.' . $documentFile->getClientOriginalExtension();
                $validated['document_file'] = $documentFile->storeAs('licenses', $fileName, 'public');
            }

            $license->update($validated);

            return response()->json([
                'success' => true,
                'license' => $license->load(['licenseType', 'licenseStatus']),
                'message' => 'License updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'License update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a license for staff member
     */
    public function destroyLicense(Staff $staff, $licenseId)
    {
        try {
            $license = $staff->licenses()->findOrFail($licenseId);
            $license->delete();

            return response()->json([
                'success' => true,
                'message' => 'License deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'License deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new medical certificate for staff member
     */
    public function storeMedicalCertificate(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'medical_class_id' => 'required|exists:medical_classes,id',
            'certificate_number' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'examining_doctor' => 'required|string|max:200',
            'examining_facility' => 'required|string|max:200',
            'medical_status_id' => 'required|exists:medical_statuses,id',
            'limitations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_medical.' . $documentFile->getClientOriginalExtension();
                $documentPath = $documentFile->storeAs('medical_certificates', $fileName, 'public');
            }

            // Create medical certificate record
            $medicalCertificate = $staff->medicalCertificates()->create([
                'medical_class_id' => $validated['medical_class_id'],
                'certificate_number' => $validated['certificate_number'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'examining_doctor' => $validated['examining_doctor'],
                'examining_facility' => $validated['examining_facility'],
                'medical_status_id' => $validated['medical_status_id'],
                'limitations' => $validated['limitations'],
                'notes' => $validated['notes'],
                'document_file' => $documentPath,
                'is_current' => $request->boolean('is_current', true)
            ]);

            return response()->json([
                'success' => true,
                'medicalCertificate' => $medicalCertificate->load(['medicalClass', 'medicalStatus']),
                'message' => 'Medical certificate added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical certificate creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing medical certificate for staff member
     */
    public function updateMedicalCertificate(Request $request, Staff $staff, $certificateId)
    {
        $medicalCertificate = $staff->medicalCertificates()->findOrFail($certificateId);

        $validated = $request->validate([
            'medical_class_id' => 'required|exists:medical_classes,id',
            'certificate_number' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'examining_doctor' => 'required|string|max:200',
            'examining_facility' => 'required|string|max:200',
            'medical_status_id' => 'required|exists:medical_statuses,id',
            'limitations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_medical.' . $documentFile->getClientOriginalExtension();
                $validated['document_file'] = $documentFile->storeAs('medical_certificates', $fileName, 'public');
            }

            $medicalCertificate->update($validated);

            return response()->json([
                'success' => true,
                'medicalCertificate' => $medicalCertificate->load(['medicalClass', 'medicalStatus']),
                'message' => 'Medical certificate updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical certificate update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a medical certificate for staff member
     */
    public function destroyMedicalCertificate(Staff $staff, $certificateId)
    {
        try {
            $medicalCertificate = $staff->medicalCertificates()->findOrFail($certificateId);
            $medicalCertificate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medical certificate deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical certificate deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new type rating for staff member
     */
    public function storeTypeRating(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'aircraft_type' => 'required|string|max:100',
            'rating_type' => 'required|in:type_rating,class_rating,endorsement',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'required|string|max:200',
            'limitations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_type_rating.' . $documentFile->getClientOriginalExtension();
                $documentPath = $documentFile->storeAs('type_ratings', $fileName, 'public');
            }

            // Create type rating record
            $typeRating = $staff->typeRatings()->create([
                'aircraft_type' => $validated['aircraft_type'],
                'rating_type' => $validated['rating_type'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'issuing_authority' => $validated['issuing_authority'],
                'limitations' => $validated['limitations'],
                'notes' => $validated['notes'],
                'document_file' => $documentPath,
                'is_current' => $request->boolean('is_current', true)
            ]);

            return response()->json([
                'success' => true,
                'typeRating' => $typeRating,
                'message' => 'Type rating added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Type rating creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing type rating for staff member
     */
    public function updateTypeRating(Request $request, Staff $staff, $ratingId)
    {
        $typeRating = $staff->typeRatings()->findOrFail($ratingId);

        $validated = $request->validate([
            'aircraft_type' => 'required|string|max:100',
            'rating_type' => 'required|in:type_rating,class_rating,endorsement',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'required|string|max:200',
            'limitations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_type_rating.' . $documentFile->getClientOriginalExtension();
                $validated['document_file'] = $documentFile->storeAs('type_ratings', $fileName, 'public');
            }

            $typeRating->update($validated);

            return response()->json([
                'success' => true,
                'typeRating' => $typeRating,
                'message' => 'Type rating updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Type rating update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a type rating for staff member
     */
    public function destroyTypeRating(Staff $staff, $ratingId)
    {
        try {
            $typeRating = $staff->typeRatings()->findOrFail($ratingId);
            $typeRating->delete();

            return response()->json([
                'success' => true,
                'message' => 'Type rating deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Type rating deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new proficiency record for staff member
     */
    public function storeProficiency(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'proficiency_type_id' => 'required|exists:proficiency_types,id',
            'last_completion_date' => 'required|date',
            'next_due_date' => 'required|date|after:last_completion_date',
            'training_provider' => 'nullable|string|max:200',
            'epl_level' => 'nullable|in:1,2,3,4,5',
            'status' => 'required|in:active,expired,due_soon,overdue',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_proficiency.' . $documentFile->getClientOriginalExtension();
                $documentPath = $documentFile->storeAs('proficiencies', $fileName, 'public');
            }

            // Create proficiency record
            $proficiency = $staff->proficiencies()->create([
                'proficiency_type_id' => $validated['proficiency_type_id'],
                'last_completion_date' => $validated['last_completion_date'],
                'next_due_date' => $validated['next_due_date'],
                'training_provider' => $validated['training_provider'],
                'epl_level' => $validated['epl_level'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'document_file' => $documentPath,
                'is_current' => $request->boolean('is_current', true)
            ]);

            return response()->json([
                'success' => true,
                'proficiency' => $proficiency->load('proficiencyType'),
                'message' => 'Proficiency record added successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proficiency creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing proficiency record for staff member
     */
    public function updateProficiency(Request $request, Staff $staff, $proficiencyId)
    {
        $proficiency = $staff->proficiencies()->findOrFail($proficiencyId);

        $validated = $request->validate([
            'proficiency_type_id' => 'required|exists:proficiency_types,id',
            'last_completion_date' => 'required|date',
            'next_due_date' => 'required|date|after:last_completion_date',
            'training_provider' => 'nullable|string|max:200',
            'epl_level' => 'nullable|in:1,2,3,4,5',
            'status' => 'required|in:active,expired,due_soon,overdue',
            'notes' => 'nullable|string|max:1000',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_current' => 'boolean'
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $fileName = time() . '_' . $staff->id . '_proficiency.' . $documentFile->getClientOriginalExtension();
                $validated['document_file'] = $documentFile->storeAs('proficiencies', $fileName, 'public');
            }

            $proficiency->update($validated);

            return response()->json([
                'success' => true,
                'proficiency' => $proficiency->load('proficiencyType'),
                'message' => 'Proficiency record updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proficiency update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a proficiency record for staff member
     */
    public function destroyProficiency(Staff $staff, $proficiencyId)
    {
        try {
            $proficiency = $staff->proficiencies()->findOrFail($proficiencyId);
            $proficiency->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proficiency record deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proficiency deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
