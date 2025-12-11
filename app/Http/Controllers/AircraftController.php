<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\AircraftDocument;
use App\Models\AircraftStatus;
use App\Models\AircraftDocumentType;
use App\Models\AircraftDocumentStatus;
use App\Models\AircraftManufacturer;
use App\Models\AircraftModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AircraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Aircraft::with(['currentDocuments', 'status', 'manufacturer', 'model'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('home_base', 'like', "%{$search}%")
                  ->orWhereHas('manufacturer', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('model', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('status', function ($sq) use ($request) {
                $sq->where('name', $request->status);
            });
        }

        if ($request->filled('manufacturer')) {
            $query->whereHas('manufacturer', function ($mq) use ($request) {
                $mq->where('name', $request->manufacturer);
            });
        }

        if ($request->filled('home_base')) {
            $query->where('home_base', $request->home_base);
        }

        $aircraft = $query->paginate(15)->withQueryString();

        // Add compliance status to each aircraft
        $aircraft->getCollection()->transform(function ($aircraft) {
            $aircraft->compliance_status = $aircraft->getComplianceStatus();
            $aircraft->is_compliant = $aircraft->isCompliant();
            $aircraft->has_expiring_documents = $aircraft->hasExpiringDocuments();
            return $aircraft;
        });

        // Get filter options
        $manufacturers = AircraftManufacturer::active()->ordered()->pluck('name')->filter()->sort()->values();
        $homeBases = Aircraft::distinct('home_base')->pluck('home_base')->filter()->sort()->values();
        $statuses = AircraftStatus::active()->ordered()->pluck('name')->filter()->sort()->values();

        return Inertia::render('Aircraft/Index', [
            'aircraft' => $aircraft,
            'manufacturers' => $manufacturers,
            'homeBases' => $homeBases,
            'statuses' => $statuses,
            'filters' => $request->only(['search', 'status', 'manufacturer', 'home_base']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $manufacturers = AircraftManufacturer::active()->ordered()->get(['id', 'name']);
        $models = AircraftModel::active()->ordered()->with('manufacturer:id,name')->get(['id', 'manufacturer_id', 'name']);
        $homeBases = Aircraft::distinct('home_base')->pluck('home_base')->filter()->sort()->values();
        $statuses = AircraftStatus::active()->ordered()->get(['id', 'name']);

        return Inertia::render('Aircraft/Create', [
            'manufacturers' => $manufacturers,
            'models' => $models,
            'homeBases' => $homeBases,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|max:20|unique:aircraft,registration_number',
            'manufacturer_id' => 'required|exists:aircraft_manufacturers,id',
            'model_id' => 'required|exists:aircraft_models,id',
            'serial_number' => 'required|string|max:50|unique:aircraft,serial_number',
            'year_of_manufacture' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_airframe_hours' => 'nullable|numeric|min:0|max:99999999.99',
            'total_cycles' => 'nullable|integer|min:0',
            'status_id' => 'required|exists:aircraft_statuses,id',
            'home_base' => 'required|string|max:100',
            'seating_configuration' => 'nullable|string',
            'total_capacity' => 'nullable|integer|min:1',
            'max_passengers' => 'nullable|integer|min:1',
            'max_takeoff_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'max_landing_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'empty_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'useful_load' => 'nullable|numeric|min:0|max:999999999.99',
            'engine_type' => 'nullable|string|max:100',
            'number_of_engines' => 'nullable|integer|min:1|max:10',
            'fuel_capacity' => 'nullable|numeric|min:0|max:999999999.99',
            'service_ceiling' => 'nullable|integer|min:0|max:100000',
            'maximum_range' => 'nullable|integer|min:0|max:50000',
            'cruise_speed' => 'nullable|integer|min:0|max:1000',
            'aircraft_category' => 'nullable|string|max:50',
            'certification_basis' => 'nullable|string|max:50',
            'ifr_certified' => 'nullable|boolean',
            'rvsm_approved' => 'nullable|boolean',
            'etops_rating' => 'nullable|string|max:50',
            'certificate_of_airworthiness' => 'nullable|string|max:100',
            'coa_issue_date' => 'nullable|date|before_or_equal:today',
            'coa_expiry_date' => 'nullable|date|after:coa_issue_date',
            'avionics_suite' => 'nullable|string|max:200',
            'propeller_type' => 'nullable|string|max:100',
            'noise_certification' => 'nullable|string|max:100',
            'photos' => 'nullable|array',
            'photos.*' => 'string', // Paths from AJAX uploads
            'notes' => 'nullable|string',
        ]);

        Aircraft::create($validated);

        return redirect()->route('aircraft.index')
            ->with('success', 'Aircraft created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aircraft $aircraft): Response
    {
        $aircraft->load([
            'documents' => function($query) {
                $query->with(['documentType', 'documentStatus'])
                      ->orderBy('document_type_id')
                      ->orderBy('created_at', 'desc');
            },
            'manufacturer:id,name',
            'model:id,name',
            'status:id,name'
        ]);

        // Get compliance overview
        $complianceStatus = $aircraft->getComplianceStatus();

        // Group documents by type name
        $documentsByType = $aircraft->documents->groupBy(function($document) {
            return $document->documentType?->name ?? 'Unknown';
        });

        // Get document types for the form
        $documentTypes = AircraftDocumentType::active()->ordered()->get();

        // Get maintenance data
        $maintenanceController = new \App\Http\Controllers\AircraftMaintenanceController();
        $maintenanceData = $maintenanceController->getMaintenanceData($aircraft);

        return Inertia::render('Aircraft/Show', [
            'aircraft' => $aircraft,
            'complianceStatus' => $complianceStatus,
            'documentsByType' => $documentsByType,
            'documentTypes' => $documentTypes,
            'maintenanceSchedules' => $maintenanceData['schedules'],
            'workOrders' => $maintenanceData['workOrders'],
            'maintenanceTypes' => $maintenanceData['maintenanceTypes'],
            'maintenanceOrganizations' => $maintenanceData['maintenanceOrganizations'],
            'maintenancePriorities' => $maintenanceData['maintenancePriorities'],
            'workOrderPriorities' => $maintenanceData['workOrderPriorities'],
            'priorities' => $maintenanceData['priorities'],
            'workOrderStatuses' => $maintenanceData['workOrderStatuses'],
            'users' => $maintenanceData['users'],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aircraft $aircraft): Response
    {
        $aircraft->load(['manufacturer:id,name', 'model:id,name', 'status:id,name']);

        $manufacturers = AircraftManufacturer::active()->ordered()->get(['id', 'name']);
        $models = AircraftModel::active()->ordered()->with('manufacturer:id,name')->get(['id', 'manufacturer_id', 'name']);
        $homeBases = Aircraft::distinct('home_base')->pluck('home_base')->filter()->sort()->values();
        $statuses = AircraftStatus::active()->ordered()->get(['id', 'name']);

        return Inertia::render('Aircraft/Edit', [
            'aircraft' => $aircraft,
            'manufacturers' => $manufacturers,
            'models' => $models,
            'homeBases' => $homeBases,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aircraft $aircraft): RedirectResponse
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|max:20|unique:aircraft,registration_number,' . $aircraft->id,
            'manufacturer_id' => 'required|exists:aircraft_manufacturers,id',
            'model_id' => 'required|exists:aircraft_models,id',
            'serial_number' => 'required|string|max:50|unique:aircraft,serial_number,' . $aircraft->id,
            'year_of_manufacture' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_airframe_hours' => 'nullable|numeric|min:0|max:99999999.99',
            'total_cycles' => 'nullable|integer|min:0',
            'status_id' => 'required|exists:aircraft_statuses,id',
            'home_base' => 'required|string|max:100',
            'seating_configuration' => 'nullable|string',
            'total_capacity' => 'nullable|integer|min:1',
            'max_passengers' => 'nullable|integer|min:1',
            'max_takeoff_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'max_landing_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'empty_weight' => 'nullable|numeric|min:0|max:999999999.99',
            'useful_load' => 'nullable|numeric|min:0|max:999999999.99',
            'engine_type' => 'nullable|string|max:100',
            'number_of_engines' => 'nullable|integer|min:1|max:10',
            'fuel_capacity' => 'nullable|numeric|min:0|max:999999999.99',
            'service_ceiling' => 'nullable|integer|min:0|max:100000',
            'maximum_range' => 'nullable|integer|min:0|max:50000',
            'cruise_speed' => 'nullable|integer|min:0|max:1000',
            'aircraft_category' => 'nullable|string|max:50',
            'certification_basis' => 'nullable|string|max:50',
            'ifr_certified' => 'nullable|boolean',
            'rvsm_approved' => 'nullable|boolean',
            'etops_rating' => 'nullable|string|max:50',
            'certificate_of_airworthiness' => 'nullable|string|max:100',
            'coa_issue_date' => 'nullable|date|before_or_equal:today',
            'coa_expiry_date' => 'nullable|date|after:coa_issue_date',
            'avionics_suite' => 'nullable|string|max:200',
            'propeller_type' => 'nullable|string|max:100',
            'noise_certification' => 'nullable|string|max:100',
            'photos' => 'nullable|array',
            'photos.*' => 'string',
            'notes' => 'nullable|string',
        ]);

        $aircraft->update($validated);

        return redirect()->route('aircraft.show', $aircraft)
            ->with('success', 'Aircraft updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aircraft $aircraft): RedirectResponse
    {
        // Delete associated photos
        if ($aircraft->photos) {
            foreach ($aircraft->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        // Delete associated document files
        foreach ($aircraft->documents as $document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
        }

        $aircraft->delete();

        return redirect()->route('aircraft.index')
            ->with('success', 'Aircraft deleted successfully!');
    }

    /**
     * Store a new document for the aircraft.
     */
    public function storeDocument(Request $request, Aircraft $aircraft): RedirectResponse
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:aircraft_document_types,id',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:200',
            'file_path' => 'nullable|string', // Path from AJAX upload
            'document_details' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['aircraft_id'] = $aircraft->id;
        $validated['document_status_id'] = AircraftDocumentStatus::getValidId(); // Default status

        // Mark other documents of same type as not current
        $aircraft->documents()
            ->where('document_type_id', $validated['document_type_id'])
            ->update(['is_current' => false]);

        AircraftDocument::create($validated);

        return redirect()->route('aircraft.show', $aircraft)
            ->with('success', 'Document added successfully!');
    }

    /**
     * Update an aircraft document.
     */
    public function updateDocument(Request $request, Aircraft $aircraft, AircraftDocument $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'document_type_id' => 'required|exists:aircraft_document_types,id',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:200',
            'file_path' => 'nullable|string',
            'document_details' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $document->update($validated);

        return redirect()->route('aircraft.show', $aircraft)
            ->with('success', 'Document updated successfully!');
    }

    /**
     * Delete an aircraft document.
     */
    public function destroyDocument(Aircraft $aircraft, AircraftDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        // Delete associated file
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('aircraft.show', $aircraft)
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * Upload photos for aircraft.
     */
    public function uploadPhoto(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('aircraft/photos', 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => Storage::url($path),
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Upload document files.
     */
    public function uploadDocument(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,png,jpeg|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('aircraft/documents', 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => Storage::url($path),
                'filename' => $file->getClientOriginalName(),
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Get models by manufacturer for AJAX requests
     */
    public function getModelsByManufacturer(Request $request)
    {
        $request->validate([
            'manufacturer_id' => 'required|exists:aircraft_manufacturers,id'
        ]);

        $models = AircraftModel::active()
            ->byManufacturer($request->manufacturer_id)
            ->ordered()
            ->get(['id', 'name', 'description']);

        return response()->json($models);
    }
}
