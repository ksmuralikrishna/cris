<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Tablet;
use App\Models\ConsentRecord;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function show(Request $request)
    {
        if (!$request->session()->has('registration_session_id')) {
            $request->session()->put(
                'registration_session_id',
                (string) Str::uuid()
            );
        }
        
        $nationalities = config('nationalities');
        $phoneCountryCodes = config('phone_country_codes');
        $languages = config('languages');

        // Sort nationalities alphabetically
        asort($nationalities);

        // Sort phone countries alphabetically
        uasort($phoneCountryCodes, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return view('register.form', [
            'sessionId' => $request->session()->get('registration_session_id'),
            'nationalities' => $nationalities,
            'phoneCountryCodes' => $phoneCountryCodes,
            'languages' => $languages,
        ]);
    }
    public function store(Request $request)
    {
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            // 'mobile_code' => 'required|string',
            // 'mobile_number_local' => 'required|string|max:20',

            
            'mobile_code' => 'required|string',
            'mobile_number_local' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d+$/'
            ],

            'emirates_id_number' => ['required', 'string', 'regex:/^784-\d{4}-\d{7}-\d{1}$/'],
            'emirates_id_image_front' => 'nullable|image|max:5120',
            'emirates_id_image_back'  => 'nullable|image|max:5120',
            'nationality' => 'required|string',
            'area_of_residence' => 'required|string',
            'preferred_language' => 'required|string',
            'age_group' => 'required|string|in:under_18,18_24,25_34,35_44,45_54,55_plus',
            'terms_consent' => 'required|accepted',
            'privacy_consent' => 'required|accepted',
            'session_id' => 'required|uuid'
        ]);

        if ($request->age_group === 'under_18') {
            return back()->withInput()->withErrors(['age_group' => 'You must be at least 18 years old to register.']);
        }

        if (Registration::isDuplicate($request->emirates_id_number)) {
            return redirect()->route('register.duplicate');
        }

        // Get a random tablet for web simulation (or use a default one)
        $tablet = Tablet::where('is_active', true)->first();
        if (!$tablet) {
            abort(500, 'No active tablets configured in the system.');
        }

        $imageFrontPath = null;
        if ($request->hasFile('emirates_id_image_front')) {
            $imageFrontPath = $request->file('emirates_id_image_front')->store('', 'emirates_id');
        }
        
        $imageBackPath = null;
        if ($request->hasFile('emirates_id_image_back')) {
            $imageBackPath = $request->file('emirates_id_image_back')->store('', 'emirates_id');
        }

        $mobileNumber = $request->mobile_code . ltrim($request->mobile_number_local, '0');

        $registration = Registration::create([
            'tablet_id' => $tablet->id,
            'full_name' => $request->full_name,
            'mobile_number' => $mobileNumber,
            'emirates_id_number' => $request->emirates_id_number,
            'emirates_id_hash' => Registration::hashEmiratesId($request->emirates_id_number),
            'emirates_id_image_path' => $imageFrontPath,
            'image_uploaded_at' => $imageFrontPath ? now() : null,
            'emirates_id_image_back_path' => $imageBackPath,
            'image_back_uploaded_at' => $imageBackPath ? now() : null,
            'nationality' => $request->nationality,
            'area_of_residence' => $request->area_of_residence,
            'preferred_language' => $request->preferred_language,
            'age_group' => $request->age_group,
            'session_id' => $request->session_id,
            'submitted_at' => now(),
        ]);

        ConsentRecord::create([
            'registration_id' => $registration->id,
            'consent_type' => 'terms',
            'granted' => true,
            'granted_at' => now(),
            'document_version' => 'terms_v1.0'
        ]);

        ConsentRecord::create([
            'registration_id' => $registration->id,
            'consent_type' => 'privacy',
            'granted' => true,
            'granted_at' => now(),
            'document_version' => 'privacy_v1.0'
        ]);

        ConsentRecord::create([
            'registration_id' => $registration->id,
            'consent_type' => 'marketing',
            'granted' => $request->boolean('marketing_consent'),
            'granted_at' => now(),
            'document_version' => 'marketing_v1.0'
        ]);

        Visit::create([
            'registration_id' => $registration->id,
            'tablet_id' => $tablet->id,
            'location_zone' => $tablet->location_zone,
            'visited_at' => now()
        ]);

        AuditLog::system('registration.created', $registration->id, ['session_id' => $request->session_id]);

        $request->session()->regenerate();
        $request->session()->forget('registration_session_id');

        return redirect()->route('register.success');
    }

    public function duplicate()
    {
        return view('register.duplicate');
    }

    public function success()
    {
        return view('register.success');
    }
}
