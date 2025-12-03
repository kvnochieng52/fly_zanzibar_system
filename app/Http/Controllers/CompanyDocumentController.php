<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use App\Models\CompanyDocumentType;
use App\Models\CompanyDocumentStatus;
use App\Models\IssuingAuthority;
use App\Models\ResponsibleDepartment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CompanyDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyDocument::with([
            'documentType',
            'status',
            'issuingAuthority',
            'responsibleDepartment',
            'creator'
        ]);

        // Filter by document type
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        // Filter by status
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('responsible_department_id', $request->department_id);
        }

        // Filter expiring documents
        if ($request->boolean('expiring_soon')) {
            $query->expiringSoon();
        }

        // Filter expired documents
        if ($request->boolean('expired')) {
            $query->expired();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('CompanyDocuments/Index', [
            'documents' => $documents,
            'documentTypes' => CompanyDocumentType::active()->get(),
            'statuses' => CompanyDocumentStatus::active()->get(),
            'departments' => ResponsibleDepartment::active()->get(),
            'filters' => $request->only(['document_type_id', 'status_id', 'department_id', 'expiring_soon', 'expired', 'search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('CompanyDocuments/Create', [
            'documentTypes' => CompanyDocumentType::active()->get(),
            'issuingAuthorities' => IssuingAuthority::active()->get(),
            'departments' => ResponsibleDepartment::active()->get(),
            'statuses' => CompanyDocumentStatus::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:company_document_types,id',
            'document_number' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'version_revision' => 'nullable|string|max:50',
            'issuing_authority_id' => 'required|exists:issuing_authorities,id',
            'responsible_department_id' => 'required|exists:responsible_departments,id',
            'status_id' => 'required|exists:company_document_statuses,id',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $validated['created_by'] = auth()->id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('company-documents', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientMimeType();
        }

        CompanyDocument::create($validated);

        return redirect()->route('company-documents.index')
            ->with('success', 'Document created successfully.');
    }

    public function show(CompanyDocument $companyDocument)
    {
        $companyDocument->load([
            'documentType',
            'status',
            'issuingAuthority',
            'responsibleDepartment',
            'creator',
            'updater',
            'versions.creator'
        ]);

        return Inertia::render('CompanyDocuments/Show', [
            'document' => $companyDocument
        ]);
    }

    public function edit(CompanyDocument $companyDocument)
    {
        return Inertia::render('CompanyDocuments/Edit', [
            'document' => $companyDocument->load([
                'documentType',
                'status',
                'issuingAuthority',
                'responsibleDepartment'
            ]),
            'documentTypes' => CompanyDocumentType::active()->get(),
            'issuingAuthorities' => IssuingAuthority::active()->get(),
            'departments' => ResponsibleDepartment::active()->get(),
            'statuses' => CompanyDocumentStatus::active()->get(),
        ]);
    }

    public function update(Request $request, CompanyDocument $companyDocument)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:company_document_types,id',
            'document_number' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'version_revision' => 'nullable|string|max:50',
            'issuing_authority_id' => 'required|exists:issuing_authorities,id',
            'responsible_department_id' => 'required|exists:responsible_departments,id',
            'status_id' => 'required|exists:company_document_statuses,id',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $validated['updated_by'] = auth()->id();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($companyDocument->file_path) {
                Storage::disk('public')->delete($companyDocument->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('company-documents', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientMimeType();
        }

        $companyDocument->update($validated);

        return redirect()->route('company-documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(CompanyDocument $companyDocument)
    {
        // Delete associated file
        if ($companyDocument->file_path) {
            Storage::disk('public')->delete($companyDocument->file_path);
        }

        $companyDocument->delete();

        return redirect()->route('company-documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(CompanyDocument $companyDocument)
    {
        if (!$companyDocument->file_path || !Storage::disk('public')->exists($companyDocument->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $companyDocument->file_path,
            $companyDocument->file_name
        );
    }

    public function dashboard()
    {
        $totalDocuments = CompanyDocument::count();
        $expiredDocuments = CompanyDocument::expired()->count();
        $expiringSoonDocuments = CompanyDocument::expiringSoon()->count();
        $activeDocuments = CompanyDocument::whereHas('status', function($q) {
            $q->where('code', 'ACTIVE');
        })->count();

        $recentDocuments = CompanyDocument::with([
            'documentType',
            'status',
            'responsibleDepartment'
        ])->latest()->limit(10)->get();

        $documentsByType = CompanyDocumentType::withCount('documents')->get();

        return Inertia::render('CompanyDocuments/Dashboard', [
            'stats' => [
                'total' => $totalDocuments,
                'expired' => $expiredDocuments,
                'expiring_soon' => $expiringSoonDocuments,
                'active' => $activeDocuments,
            ],
            'recentDocuments' => $recentDocuments,
            'documentsByType' => $documentsByType,
        ]);
    }

    public function showRenew(CompanyDocument $companyDocument)
    {
        $companyDocument->load([
            'documentType',
            'status',
            'issuingAuthority',
            'responsibleDepartment',
            'versions' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        $documentTypes = CompanyDocumentType::orderBy('name')->get();
        $issuingAuthorities = IssuingAuthority::orderBy('name')->get();
        $departments = ResponsibleDepartment::orderBy('name')->get();
        $statuses = CompanyDocumentStatus::orderBy('name')->get();

        return Inertia::render('CompanyDocuments/Renew', [
            'document' => $companyDocument,
            'documentTypes' => $documentTypes,
            'issuingAuthorities' => $issuingAuthorities,
            'departments' => $departments,
            'statuses' => $statuses,
        ]);
    }

    public function processRenewal(Request $request, CompanyDocument $companyDocument)
    {
        $request->validate([
            'document_number' => 'required|string|max:255',
            'title' => 'required|string|max:500',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'version_revision' => 'nullable|string|max:100',
            'status_id' => 'required|exists:company_document_statuses,id',
            'issuing_authority_id' => 'required|exists:issuing_authorities,id',
            'responsible_department_id' => 'required|exists:responsible_departments,id',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240',
        ]);

        // Create version record for the old document
        if ($companyDocument->file_path) {
            CompanyDocumentVersion::create([
                'company_document_id' => $companyDocument->id,
                'version_number' => $companyDocument->versions()->count() + 1,
                'file_path' => $companyDocument->file_path,
                'file_name' => $companyDocument->file_name,
                'file_size' => $companyDocument->file_size,
                'notes' => 'Previous version before renewal',
                'created_by' => auth()->id(),
            ]);
        }

        // Handle file upload
        $fileData = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('company_documents', $fileName, 'public');

            $fileData = [
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ];
        }

        // Update the document with renewal information
        $companyDocument->update([
            'document_number' => $request->document_number,
            'title' => $request->title,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'version_revision' => $request->version_revision,
            'status_id' => $request->status_id,
            'issuing_authority_id' => $request->issuing_authority_id,
            'responsible_department_id' => $request->responsible_department_id,
            'notes' => $request->notes,
            'renewed_at' => now(),
            'renewed_by' => auth()->id(),
            ...$fileData
        ]);

        return redirect()->route('company-documents.show', $companyDocument)
            ->with('success', 'Document renewed successfully.');
    }
}
