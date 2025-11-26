<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_number',
        'first_name',
        'middle_name',
        'last_name',
        'profile_photo',
        'date_of_birth',
        'gender_id',
        'country_id',
        'hire_date',
        'contract_start_date',
        'contract_end_date',
        'department_id',
        'position_id',
        'status_id',
        'employment_type_id',
        'current_base',
        'work_location_id',
        'supervisor_name',
        'salary',
        'email',
        'phone_primary',
        'phone_secondary',
        'address_line_1',
        'address_line_2',
        'city',
        'state_region',
        'postal_code',
        'country',
        'notes',
        'identification_type',
        'identification_type_id',
        'identification_number',
        'identification_file',
        'highest_qualification_id',
        'qualification_name',
        'institution_name',
        'graduation_year',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_email',
        'next_of_kin_relationship',
        'additional_info',
        'last_login_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'graduation_year' => 'integer',
        'last_login_at' => 'datetime',
        'additional_info' => 'array',
        'salary' => 'decimal:2',
    ];

    protected $appends = [
        'full_name',
        'age',
        'years_of_service'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staff) {
            if (empty($staff->staff_number)) {
                $staff->staff_number = self::generateStaffNumber();
            }
        });
    }

    /**
     * Generate unique staff number
     */
    public static function generateStaffNumber(): string
    {
        $year = now()->format('y');
        $prefix = 'ZFS' . $year; // Zanzibar Fly System + Year

        // Get the last staff number for this year
        $lastStaff = self::where('staff_number', 'LIKE', $prefix . '%')
                        ->orderBy('staff_number', 'desc')
                        ->first();

        if ($lastStaff) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastStaff->staff_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format: ZFS24001, ZFS24002, etc.
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StaffStatus::class, 'status_id');
    }



    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function workLocation(): BelongsTo
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function highestQualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'highest_qualification_id');
    }

    public function employmentContracts(): HasMany
    {
        return $this->hasMany(EmploymentContract::class);
    }

    public function currentContract(): HasOne
    {
        return $this->hasOne(EmploymentContract::class)->where('is_current', true);
    }

    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(IdentificationType::class);
    }

    // Aviation Certification Relationships
    public function licenses(): HasMany
    {
        return $this->hasMany(StaffLicense::class);
    }

    public function currentLicenses(): HasMany
    {
        return $this->hasMany(StaffLicense::class)->where('is_current', true);
    }

    public function typeRatings(): HasMany
    {
        return $this->hasMany(StaffTypeRating::class);
    }

    public function medicalCertificates(): HasMany
    {
        return $this->hasMany(StaffMedicalCertificate::class);
    }

    public function currentMedicalCertificate(): HasOne
    {
        return $this->hasOne(StaffMedicalCertificate::class)->where('is_current', true);
    }

    public function proficiencies(): HasMany
    {
        return $this->hasMany(StaffProficiency::class);
    }

    public function currentProficiencies(): HasMany
    {
        return $this->hasMany(StaffProficiency::class)->where('is_current', true);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getYearsOfServiceAttribute(): ?int
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('allows_access', true);
        });
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeCrew($query)
    {
        return $query->whereHas('position', function ($q) {
            $q->where('type', 'crew');
        });
    }

    public function scopeStaff($query)
    {
        return $query->whereHas('position', function ($q) {
            $q->where('type', 'staff');
        });
    }
}
