<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ConsentRecord;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrationApiController extends Controller
{
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'emirates_id' => ['required', 'string']
        ]);

        $emiratesId = $request->emirates_id;
        $isValidFormat = preg_match('/^784-\d{4}-\d{7}-\d{1}$/', $emiratesId);

        if (!$isValidFormat) {
            return response()->json([
                'is_duplicate' => false,
                'format_valid' => false,
                'message' => 'Invalid Emirates ID format'
            ]);
        }

        $isDuplicate = Registration::isDuplicate($emiratesId);

        return response()->json([
            'is_duplicate' => $isDuplicate,
            'format_valid' => true,
            'message' => $isDuplicate ? 'Already registered' : 'Not registered'
        ]);
    }

    public function heartbeat(Request $request)
    {
        $tablet = $request->_tablet;
        
        $tablet->update([
            'last_heartbeat_at' => now(),
            'app_version' => $request->input('app_version', $tablet->app_version)
        ]);

        return response()->json([
            'success' => true,
            'server_time' => now()->toIso8601String()
        ]);
    }

    public function store(Request $request)
    {
        $tablet = $request->_tablet;

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'emirates_id_number' => ['required', 'string', 'regex:/^784-\d{4}-\d{7}-\d{1}$/'],
            'emirates_id_image' => 'nullable|image|max:5120',
            'nationality' => 'required|string',
            'area_of_residence' => 'required|string',
            'preferred_language' => 'required|string|in:en,ar,ur,hi,tl',
            'age_group' => 'required|string|in:18_24,25_34,35_44,45_54,55_plus',
            'terms_consent' => 'required|accepted',
            'privacy_consent' => 'required|accepted',
            'marketing_consent' => 'nullable|boolean',
            'session_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'code' => 'VALIDATION_ERROR',
                'fields' => $validator->errors()
            ], 422);
        }

        if (Registration::isDuplicate($request->emirates_id_number)) {
            return response()->json([
                'error' => 'Already registered',
                'code' => 'ALREADY_REGISTERED'
            ], 409);
        }

        $imagePath = null;
        if ($request->hasFile('emirates_id_image')) {
            $imagePath = $request->file('emirates_id_image')->store('', 'emirates_id');
        }

        $registration = Registration::create([
            'tablet_id' => $tablet->id,
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'emirates_id_number' => $request->emirates_id_number,
            'emirates_id_hash' => Registration::hashEmiratesId($request->emirates_id_number),
            'emirates_id_image_path' => $imagePath,
            'image_uploaded_at' => $imagePath ? now() : null,
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

        AuditLog::tablet($tablet->id, 'registration.created', $registration->id);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'registration_id' => $registration->id
        ], 201);
    }
}
