<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class RegistrationsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Registration::with(['tablet', 'consentRecords']);

        if ($this->request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $this->request->date_to);
        }

        if ($this->request->filled('nationality') && $this->request->nationality !== 'All') {
            $query->where('nationality', $this->request->nationality);
        }

        if ($this->request->filled('preferred_language') && $this->request->preferred_language !== 'All') {
            $query->where('preferred_language', $this->request->preferred_language);
        }

        if ($this->request->filled('age_group') && $this->request->age_group !== 'All') {
            $query->where('age_group', $this->request->age_group);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('submitted_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Mobile Number',
            'Emirates ID Number',
            'Nationality',
            'Area of Residence',
            'Preferred Language',
            'Age Group',
            'Marketing Consent',
            'Registered At',
            'Tablet Label',
            'Location Zone',
        ];
    }

    public function map($registration): array
    {
        $marketingConsent = $registration->consentRecords->firstWhere('consent_type', 'marketing');
        
        return [
            $registration->full_name,
            $registration->mobile_number,
            $registration->emirates_id_number,
            $registration->nationality,
            $registration->area_of_residence,
            $registration->preferred_language,
            $registration->age_group,
            $marketingConsent && $marketingConsent->granted ? 'Yes' : 'No',
            $registration->submitted_at->format('Y-m-d H:i:s'),
            $registration->tablet->label ?? 'N/A',
            $registration->tablet->location_zone ?? 'N/A',
        ];
    }
}
