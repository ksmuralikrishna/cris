<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ConsentRecord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistrationsExport;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $totalRegistrations = Registration::count();
        $todayRegistrations = Registration::whereDate('submitted_at', Carbon::today())->count();
        $thisWeekRegistrations = Registration::where('submitted_at', '>=', Carbon::now()->startOfWeek())->count();
        $marketingOptIns = ConsentRecord::where('consent_type', 'marketing')->where('granted', true)->count();

        // Filters
        $nationalities = Registration::distinct()->pluck('nationality')->filter()->values();
        
        $query = Registration::with(['tablet', 'consentRecords']);

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        if ($request->filled('nationality') && $request->nationality !== 'All') {
            $query->where('nationality', $request->nationality);
        }

        if ($request->filled('preferred_language') && $request->preferred_language !== 'All') {
            $query->where('preferred_language', $request->preferred_language);
        }

        if ($request->filled('age_group') && $request->age_group !== 'All') {
            $query->where('age_group', $request->age_group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderBy('submitted_at', 'desc')->paginate(25)->withQueryString();

        return view('admin.dashboard', compact(
            'totalRegistrations',
            'todayRegistrations',
            'thisWeekRegistrations',
            'marketingOptIns',
            'nationalities',
            'registrations'
        ));
    }

    public function export(Request $request)
    {
        $filename = 'registrations_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new RegistrationsExport($request), $filename);
    }
}
