<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationDetailController extends Controller
{
    public function show($id)
    {
        $registration = Registration::with(['tablet', 'consentRecords', 'visits.tablet'])->findOrFail($id);
        return view('admin.registration-detail', compact('registration'));
    }
}
