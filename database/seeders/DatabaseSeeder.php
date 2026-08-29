<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tablet;
use App\Models\Registration;
use App\Models\ConsentRecord;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@mall.com',
            'password' => Hash::make('Admin@1234'),
        ]);

        // 2. Tablets
        $tabletConfigs = [
            ['label' => 'Main Entrance - Tablet 1', 'location_zone' => 'Main Entrance'],
            ['label' => 'Main Entrance - Tablet 2', 'location_zone' => 'Main Entrance'],
            ['label' => 'Food Court - Tablet 1', 'location_zone' => 'Food Court'],
            ['label' => 'East Wing - Tablet 1', 'location_zone' => 'East Wing'],
            ['label' => 'Customer Service - Tablet 1', 'location_zone' => 'Customer Service'],
        ];

        $tablets = [];
        foreach ($tabletConfigs as $config) {
            $tablets[] = Tablet::create([
                'label' => $config['label'],
                'location_zone' => $config['location_zone'],
                'api_token' => Str::random(64),
                'app_version' => '1.0.0',
                'is_active' => true,
                'last_heartbeat_at' => Carbon::now()->subMinutes(rand(1, 30)),
            ]);
        }

        // 3. Registrations
        $nationalities = ['AE', 'IN', 'PK', 'PH', 'EG', 'BD', 'GB', 'US', 'JO', 'LB'];
        $areas = ['Downtown Dubai', 'Deira', 'Bur Dubai', 'JBR', 'Marina', 'Jumeirah', 'Al Quoz', 'Mirdif', 'Al Barsha', 'Silicon Oasis'];
        $languages = ['en' => 40, 'ar' => 30, 'ur' => 15, 'hi' => 10, 'tl' => 5];
        $ageGroups = ['18_24', '25_34', '35_44', '45_54', '55_plus'];

        for ($i = 0; $i < 60; $i++) {
            $tablet = $tablets[array_rand($tablets)];
            
            // Generate Fake Emirates ID 784-YYYY-NNNNNNN-C
            $eidYear = rand(1950, 2005);
            $eidNumber = str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            $eidCheck = rand(0, 9);
            $emiratesId = "784-{$eidYear}-{$eidNumber}-{$eidCheck}";

            // Select language based on weight
            $rand = rand(1, 100);
            $cumulative = 0;
            $selectedLanguage = 'en';
            foreach ($languages as $lang => $weight) {
                $cumulative += $weight;
                if ($rand <= $cumulative) {
                    $selectedLanguage = $lang;
                    break;
                }
            }

            $submittedAt = Carbon::now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));

            $registration = Registration::create([
                'tablet_id' => $tablet->id,
                'full_name' => $faker->name,
                'mobile_number' => '+97150' . rand(1000000, 9999999),
                'emirates_id_number' => $emiratesId,
                'emirates_id_hash' => Registration::hashEmiratesId($emiratesId),
                'emirates_id_image_path' => null, // Optional
                'image_uploaded_at' => null,
                'nationality' => $nationalities[array_rand($nationalities)],
                'area_of_residence' => $areas[array_rand($areas)],
                'preferred_language' => $selectedLanguage,
                'age_group' => $ageGroups[array_rand($ageGroups)],
                'session_id' => Str::uuid(),
                'submitted_at' => $submittedAt,
            ]);

            // Consents
            ConsentRecord::create([
                'registration_id' => $registration->id,
                'consent_type' => 'terms',
                'granted' => true,
                'granted_at' => $submittedAt,
                'document_version' => 'terms_v1.0'
            ]);

            ConsentRecord::create([
                'registration_id' => $registration->id,
                'consent_type' => 'privacy',
                'granted' => true,
                'granted_at' => $submittedAt,
                'document_version' => 'privacy_v1.0'
            ]);

            ConsentRecord::create([
                'registration_id' => $registration->id,
                'consent_type' => 'marketing',
                'granted' => rand(1, 100) <= 60, // 60% chance
                'granted_at' => $submittedAt,
                'document_version' => 'marketing_v1.0'
            ]);

            // Initial Visit
            Visit::create([
                'registration_id' => $registration->id,
                'tablet_id' => $tablet->id,
                'location_zone' => $tablet->location_zone,
                'visited_at' => $submittedAt
            ]);

            // 30% chance for a second visit 1-14 days later
            if (rand(1, 100) <= 30) {
                $secondVisitDate = (clone $submittedAt)->addDays(rand(1, 14));
                if ($secondVisitDate->isBefore(Carbon::now())) {
                    $randomTablet = $tablets[array_rand($tablets)];
                    Visit::create([
                        'registration_id' => $registration->id,
                        'tablet_id' => $randomTablet->id,
                        'location_zone' => $randomTablet->location_zone,
                        'visited_at' => $secondVisitDate
                    ]);
                }
            }

            // Audit Log
            AuditLog::create([
                'actor_type' => 'tablet',
                'actor_id' => $tablet->id,
                'action' => 'registration.created',
                'target_id' => $registration->id,
                'metadata' => ['seeder' => true],
                'ip_address' => '127.0.0.1',
                'occurred_at' => $submittedAt
            ]);
        }
    }
}
