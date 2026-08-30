@extends('layouts.app')

@section('title', 'Welcome - Registration')

@section('content')
<div class="bg-white rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-hidden">
    <!-- Header / Progress -->
    <div class="bg-gray-50 border-b p-6">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-4">Customer info</h1>
        <div class="flex items-center justify-between">
            <div class="w-full flex justify-between items-center text-sm font-medium text-gray-500 relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t-2 border-gray-200"></div>
                </div>
                
                <div class="relative flex flex-col items-center step-indicator" data-step="1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-primary text-white border-4 border-white font-bold z-10 transition-colors" id="indicator-1">1</div>
                    <span class="mt-2 text-primary font-bold">Emirates ID</span>
                </div>
                
                <div class="relative flex flex-col items-center step-indicator" data-step="2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 border-4 border-white font-bold z-10 transition-colors" id="indicator-2">2</div>
                    <span class="mt-2">Personal</span>
                </div>
                
                <div class="relative flex flex-col items-center step-indicator" data-step="3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 border-4 border-white font-bold z-10 transition-colors" id="indicator-3">3</div>
                    <span class="mt-2">Consents</span>
                </div>
            </div>
        </div>
        <p class="text-center mt-4 text-gray-500 font-medium" id="step-text">Step 1 of 3</p>
    </div>

    <!-- Error Messages (Server side) -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">Please correct the errors below.</p>
                </div>
            </div>
        </div>
    @endif

    <form id="registrationForm" action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
        @csrf
        <input type="hidden" name="session_id" value="{{ $sessionId }}">

        <!-- STEP 1: Emirates ID -->
        <div id="step1" class="step-content">
            <div class="space-y-6">
                <!-- Emirates ID Number -->
                <div>
                    <label for="emirates_id_number" class="block text-lg font-medium text-gray-700 mb-2">Emirates ID Number <span class="text-red-500">*</span></label>
                    <input type="text" name="emirates_id_number" id="emirates_id_number" required 
                        placeholder="784-YYYY-NNNNNNN-C" value="{{ old('emirates_id_number') }}"
                        class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary font-mono tracking-widest text-center"
                        onblur="validateEmiratesId(this)"
                        oninput="formatEmiratesId(this)"
                        maxlength="18"
                        inputmode="numeric">
                    <p class="mt-2 text-gray-500 text-sm">Format: 784-YYYY-NNNNNNN-C</p>
                    <p id="eid_duplicate_error" class="mt-2 text-red-600 font-medium hidden">You are already registered with this Emirates ID</p>
                    @error('emirates_id_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Emirates ID Image -->
                <div class="mt-8 pt-8 border-t">
                    <label class="block text-lg font-medium text-gray-700 mb-2">Emirates ID Image <span class="text-gray-400 font-normal">(Optional)</span></label>
                    
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition relative">
                        <div class="space-y-1 text-center" id="upload-prompt">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-lg text-gray-600 justify-center">
                                <label for="emirates_id_image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary p-1">
                                    <span>Take a photo</span>
                                    <input id="emirates_id_image" name="emirates_id_image" type="file" accept="image/*" capture="environment" onchange="previewImage(this)" style="position:fixed;top:-100px;left:-100px;opacity:0;">                                
                                    <p class="pl-1 pt-1"></p>
                            </div>
                            <p class="text-sm text-gray-500">Max 5MB</p>
                        </div>
                        <img id="image-preview" class="hidden max-h-48 rounded mx-auto" />
                    </div>
                    <div id="remove-image-btn" class="hidden mt-2 text-center">
                        <button type="button" class="text-red-500 font-medium" onclick="removeImage()">Remove image</button>
                    </div>
                    @error('emirates_id_image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" onclick="goToStep2()" id="btn-step-1" class="bg-primary text-white font-bold py-4 px-10 rounded-lg text-lg hover:bg-emerald-600 transition">Next Step</button>
            </div>
        </div>

        <!-- STEP 2: Personal Information -->
        <div id="step2" class="step-content hidden">
            <div class="space-y-6">
                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-lg font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" required value="{{ old('full_name') }}"
                           class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary">
                    @error('full_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Mobile Number -->
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                    <div class="flex">
                    
                    <select name="mobile_code"
                        class="rounded-l-lg border-gray-300 border-y border-l p-4 text-lg bg-gray-50 focus:ring-primary focus:border-primary outline-none">

                        @foreach($phoneCountryCodes as $code => $country)
                            <option value="{{ $country['dial_code'] }}"
                                {{ old('mobile_code', '+971') == $country['dial_code'] ? 'selected' : '' }}>
                                {{ $country['dial_code'] }} ({{ $country['name'] }})
                            </option>
                        @endforeach

                    </select>

                        <input type="number" name="mobile_number_local" id="mobile_number_local" required value="{{ old('mobile_number_local') }}"
                               class="w-full rounded-r-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary">
                    </div>
                    @error('mobile_number_local') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Nationality -->
                <div>
                    <label for="nationality" class="block text-lg font-medium text-gray-700 mb-2">Nationality <span class="text-red-500">*</span></label>
                    <!-- <select name="nationality" id="nationality" required class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary bg-white">
                        <option value="">Select Nationality</option>
                        <option value="AE" {{ old('nationality') == 'AE' ? 'selected' : '' }}>United Arab Emirates</option>
                        <option disabled>──────────</option>
                        <option value="IN" {{ old('nationality') == 'IN' ? 'selected' : '' }}>India</option>
                        <option value="PK" {{ old('nationality') == 'PK' ? 'selected' : '' }}>Pakistan</option>
                        <option value="PH" {{ old('nationality') == 'PH' ? 'selected' : '' }}>Philippines</option>
                        <option value="EG" {{ old('nationality') == 'EG' ? 'selected' : '' }}>Egypt</option>
                        <option value="BD" {{ old('nationality') == 'BD' ? 'selected' : '' }}>Bangladesh</option>
                        <option value="GB" {{ old('nationality') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                        <option value="US" {{ old('nationality') == 'US' ? 'selected' : '' }}>United States</option>
                        <option value="JO" {{ old('nationality') == 'JO' ? 'selected' : '' }}>Jordan</option>
                        <option value="LB" {{ old('nationality') == 'LB' ? 'selected' : '' }}>Lebanon</option>
                        <option value="SY" {{ old('nationality') == 'SY' ? 'selected' : '' }}>Syria</option>
                        <option value="NP" {{ old('nationality') == 'NP' ? 'selected' : '' }}>Nepal</option>
                        <option disabled>──────────</option>
                        <option value="OTHER" {{ old('nationality') == 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select> -->
                    
                    <select name="nationality"
                        id="nationality"
                        required
                        class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary bg-white">

                        <option value="">Select Nationality</option>

                        @foreach($nationalities as $code => $name)
                            <option value="{{ $code }}"
                                {{ old('nationality') == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach

                    </select>



                    
                    @error('nationality') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Area of Residence -->
                <div>
                    <label for="area_of_residence" class="block text-lg font-medium text-gray-700 mb-2">Area of Residence <span class="text-red-500">*</span></label>
                    <select name="area_of_residence" id="area_of_residence" required class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary bg-white">
                        <option value="">Select Area</option>
                        @foreach(['Downtown Dubai', 'Deira', 'Bur Dubai', 'JBR', 'Marina', 'Jumeirah', 'Al Quoz', 'Mirdif', 'Al Barsha', 'Silicon Oasis', 'Other'] as $area)
                            <option value="{{ $area }}" {{ old('area_of_residence') == $area ? 'selected' : '' }}>{{ $area }}</option>
                        @endforeach
                    </select>
                    @error('area_of_residence') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Preferred Language -->
                <div>
                    <label for="preferred_language" class="block text-lg font-medium text-gray-700 mb-2">Preferred Language <span class="text-red-500">*</span></label>
                    <!-- <select name="preferred_language" id="preferred_language" required class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary bg-white">
                        <option value="">Select Language</option>
                        <option value="en" {{ old('preferred_language') == 'en' ? 'selected' : '' }}>English</option>
                        <option value="ar" {{ old('preferred_language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                        <option value="ur" {{ old('preferred_language') == 'ur' ? 'selected' : '' }}>Urdu</option>
                        <option value="hi" {{ old('preferred_language') == 'hi' ? 'selected' : '' }}>Hindi</option>
                        <option value="tl" {{ old('preferred_language') == 'tl' ? 'selected' : '' }}>Filipino</option>
                    </select> -->
                    <select
                        name="preferred_language"
                        id="preferred_language"
                        required
                        class="w-full rounded-lg border-gray-300 border p-4 text-lg focus:ring-primary focus:border-primary bg-white"
                    >
                        <option value="">Select Preferred Language</option>

                        @foreach($languages as $code => $language)
                            <option value="{{ $code }}"
                                {{ old('preferred_language') == $code ? 'selected' : '' }}>
                                {{ $language }}
                            </option>
                        @endforeach
                    </select>
                    @error('preferred_language') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Age Group -->
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-3">Age Group <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['under_18' => 'Under 18', '18_24' => '18-24', '25_34' => '25-34', '35_44' => '35-44', '45_54' => '45-54', '55_plus' => '55+'] as $val => $label)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('age_group') == $val ? 'border-primary bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="age_group" value="{{ $val }}" required class="w-6 h-6 text-primary border-gray-300 focus:ring-primary" {{ old('age_group') == $val ? 'checked' : '' }}> 
                            <!-- onchange="checkAgeGroup(this)" -->
                            <span class="ml-3 text-lg">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div id="under_18_error" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 font-medium">
                        Sorry, you must be 18 or older to register.
                    </div>
                    @error('age_group') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" onclick="nextStep(1)" class="bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-lg text-lg hover:bg-gray-300 transition">Back</button>
                <button type="button" onclick="goToStep3()" id="btn-step-2" class="bg-primary text-white font-bold py-4 px-10 rounded-lg text-lg hover:bg-emerald-600 transition">Next Step</button>
            </div>
        </div>

        <!-- STEP 3: Consents -->
        <div id="step3" class="step-content hidden">
            
            <!-- Summary -->
            <div class="bg-gray-50 border rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Your Details</h3>
                <div class="grid grid-cols-2 gap-4 text-lg">
                    <div>
                        <span class="block text-sm text-gray-500">Name</span>
                        <span id="summary_name" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Mobile</span>
                        <span id="summary_mobile" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Nationality</span>
                        <span id="summary_nationality" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Emirates ID</span>
                        <span id="summary_eid" class="font-medium"></span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Terms -->
                <div class="border rounded-lg p-4 {{ $errors->has('terms_consent') ? 'border-red-500' : 'border-gray-200' }}">
                    <div class="flex items-start">
                        <div class="flex items-center h-6">
                            <input id="terms_consent" name="terms_consent" type="checkbox" required value="1" {{ old('terms_consent') ? 'checked' : '' }} class="w-6 h-6 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        </div>
                        <div class="ml-3">
                            <label for="terms_consent" class="font-medium text-gray-800 text-lg cursor-pointer">I accept the Terms & Conditions <span class="text-red-500">*</span></label>
                            <div class="mt-2 text-sm text-gray-500 h-24 overflow-y-auto pr-2 bg-gray-50 p-2 rounded border">
                                <p class="mb-2">1. By registering, you agree to comply with mall policies and regulations.</p>
                                <p class="mb-2">2. You confirm that all information provided is accurate and belongs to you.</p>
                                <p>3. The mall management reserves the right to verify the provided information at any time.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy -->
                <div class="border rounded-lg p-4 {{ $errors->has('privacy_consent') ? 'border-red-500' : 'border-gray-200' }}">
                    <div class="flex items-start">
                        <div class="flex items-center h-6">
                            <input id="privacy_consent" name="privacy_consent" type="checkbox" required value="1" {{ old('privacy_consent') ? 'checked' : '' }} class="w-6 h-6 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        </div>
                        <div class="ml-3">
                            <label for="privacy_consent" class="font-medium text-gray-800 text-lg cursor-pointer">I agree to the Privacy Policy <span class="text-red-500">*</span></label>
                            <div class="mt-2 text-sm text-gray-500 h-24 overflow-y-auto pr-2 bg-gray-50 p-2 rounded border">
                                <p class="mb-2">1. We collect your data solely for registration and identification purposes.</p>
                                <p class="mb-2">2. Your data is stored securely and will not be shared with unauthorized third parties.</p>
                                <p>3. You have the right to request deletion of your data according to applicable laws.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marketing -->
                <div class="border rounded-lg p-4 border-gray-200 bg-emerald-50">
                    <div class="flex items-start">
                        <div class="flex items-center h-6">
                            <input id="marketing_consent" name="marketing_consent" type="checkbox" value="1" {{ old('marketing_consent') ? 'checked' : '' }} class="w-6 h-6 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        </div>
                        <div class="ml-3">
                            <label for="marketing_consent" class="font-medium text-emerald-800 text-lg cursor-pointer">I agree to receive promotional offers and updates from the mall</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <button type="button" onclick="nextStep(2)" class="bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-lg text-lg hover:bg-gray-300 transition">Back</button>
                <button type="submit" id="submitBtn" class="bg-primary text-white font-bold py-4 px-10 rounded-lg text-lg hover:bg-emerald-600 transition w-full ml-4 shadow-lg">Complete Registration</button>
            </div>
        </div>
    </form>
</div>

<script>
    // State
    let currentStep = 1;
    let isEidDuplicate = false;
    let isCheckingEid = false;
    let lastCheckedEid = '';

    const phoneCountryCodes = @json($phoneCountryCodes);

    const mobileCodeSelect = document.querySelector('select[name="mobile_code"]');
    const mobileNumberInput = document.querySelector('input[name="mobile_number_local"]');

    function updatePhoneValidation() {

        const selectedCode = mobileCodeSelect.value;

        const country = Object.values(phoneCountryCodes).find(
            country => country.dial_code === selectedCode
        );

        if (!country) {
            return;
        }

        const maxLength = country.phone_length;

        // Set maximum number of digits
        mobileNumberInput.maxLength = maxLength;

        // Remove anything that is not a number
        mobileNumberInput.value = mobileNumberInput.value.replace(/\D/g, '');

        // If existing number is longer than allowed, trim it
        if (mobileNumberInput.value.length > maxLength) {
            mobileNumberInput.value =
                mobileNumberInput.value.substring(0, maxLength);
        }

        validatePhoneNumber();
    }

    function validatePhoneNumber() {

        const selectedCode = mobileCodeSelect.value;

        const country = Object.values(phoneCountryCodes).find(
            country => country.dial_code === selectedCode
        );

        if (!country) {
            return;
        }

        const phone = mobileNumberInput.value;

        // Empty
        if (phone.length === 0) {
            mobileNumberInput.setCustomValidity('');
            return;
        }

        // Check length
        if (phone.length < country.phone_length) {

            mobileNumberInput.setCustomValidity(
                `Please enter ${country.phone_length} digits for ${country.name}.`
            );

        } else {

            mobileNumberInput.setCustomValidity('');
        }
    }

    // Country code changed
    mobileCodeSelect.addEventListener('change', function () {
        updatePhoneValidation();
    });

    // User typing
    mobileNumberInput.addEventListener('input', function () {

        const selectedCode = mobileCodeSelect.value;

        const country = Object.values(phoneCountryCodes).find(
            country => country.dial_code === selectedCode
        );

        if (!country) {
            return;
        }

        // Numbers only
        this.value = this.value.replace(/\D/g, '');

        // Prevent typing more than allowed digits
        if (this.value.length > country.phone_length) {
            this.value = this.value.substring(
                0,
                country.phone_length
            );
        }

        validatePhoneNumber();
    });

    // Initialize when page loads
    updatePhoneValidation();


    function formatEmiratesId(input) {
    // Strip everything except digits
        let digits = input.value.replace(/\D/g, '');

        // UAE Emirates ID is always 15 digits: 784 + 4 + 7 + 1
        // Cap at 15 digits
        digits = digits.substring(0, 15);

        // Build the formatted string: 784-YYYY-NNNNNNN-C
        let formatted = '';

        if (digits.length <= 3) {
            formatted = digits;
        } else if (digits.length <= 7) {
            formatted = digits.substring(0, 3) + '-' + digits.substring(3);
        } else if (digits.length <= 14) {
            formatted = digits.substring(0, 3) + '-' + digits.substring(3, 7) + '-' + digits.substring(7);
        } else {
            formatted = digits.substring(0, 3) + '-' + digits.substring(3, 7) + '-' + digits.substring(7, 14) + '-' + digits.substring(14);
        }

        input.value = formatted;

        // Auto-trigger duplicate check once all 15 digits are entered
        if (digits.length === 15) {
            validateEmiratesId(input);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Apply styling to radio buttons based on checked state
        document.querySelectorAll('input[type=radio][name=age_group]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[type=radio][name=age_group]').forEach(r => {
                    r.closest('label').classList.remove('border-primary', 'bg-green-50');
                    r.closest('label').classList.add('border-gray-300');
                });
                if(this.checked) {
                    this.closest('label').classList.remove('border-gray-300');
                    this.closest('label').classList.add('border-primary', 'bg-green-50');
                }
            });
        });

        // Show error step if validation failed on a specific step
        @if($errors->has('terms_consent') || $errors->has('privacy_consent'))
            nextStep(3);
        @elseif($errors->has('full_name') || $errors->has('mobile_number_local') || $errors->has('nationality') || $errors->has('area_of_residence') || $errors->has('preferred_language') || $errors->has('age_group'))
            nextStep(2);
        @else
            nextStep(1); // Default to Emirates ID step
        @endif
    });

    function checkAgeGroup(radio) {
        const errorDiv = document.getElementById('under_18_error');
        const nextBtn = document.getElementById('btn-step-2'); // Now btn-step-2 is for Personal Info
        
        if (radio.value === 'under_18') {
            errorDiv.classList.remove('hidden');
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorDiv.classList.add('hidden');
            nextBtn.disabled = false;
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function updateSummary() {
        document.getElementById('summary_name').textContent = document.getElementById('full_name').value || '-';
        document.getElementById('summary_mobile').textContent = document.querySelector('select[name="mobile_code"]').value + document.getElementById('mobile_number_local').value;
        
        const natSelect = document.getElementById('nationality');
        document.getElementById('summary_nationality').textContent = natSelect.options[natSelect.selectedIndex]?.text || '-';
        
        const eid = document.getElementById('emirates_id_number').value;
        if(eid && eid.length >= 15) {
            document.getElementById('summary_eid').textContent = eid.substring(0,3) + '-****-*******-' + eid.substring(eid.length - 1);
        } else {
            document.getElementById('summary_eid').textContent = eid || '-';
        }
    }

    function nextStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        
        // Show target step
        document.getElementById('step' + step).classList.remove('hidden');
        
        // Update indicators
        for(let i=1; i<=3; i++) {
            const ind = document.getElementById('indicator-' + i);
            const parent = ind.closest('.step-indicator');
            const label = parent.querySelector('span');
            
            if(i < step) {
                // Completed
                ind.className = "w-10 h-10 rounded-full flex items-center justify-center bg-primary text-white border-4 border-white font-bold z-10 transition-colors";
                ind.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                label.className = "mt-2 font-medium text-gray-500";
            } else if (i === step) {
                // Current
                ind.className = "w-10 h-10 rounded-full flex items-center justify-center bg-primary text-white border-4 border-white font-bold z-10 transition-colors";
                ind.innerHTML = i;
                label.className = "mt-2 font-bold text-primary";
            } else {
                // Upcoming
                ind.className = "w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 border-4 border-white font-bold z-10 transition-colors";
                ind.innerHTML = i;
                label.className = "mt-2 font-medium text-gray-500";
            }
        }
        
        document.getElementById('step-text').textContent = 'Step ' + step + ' of 3';
        currentStep = step;
        
        if(step === 3) {
            updateSummary();
        }
        
        window.scrollTo(0, 0);
    }

    async function checkEidDuplicate(val) {
        const errorEl = document.getElementById('eid_duplicate_error');
        const nextBtn = document.getElementById('btn-step-1');
        const input = document.getElementById('emirates_id_number');

        if (!val || val === lastCheckedEid) return isEidDuplicate;

        isCheckingEid = true;
        nextBtn.disabled = true;
        
        const originalBtnText = nextBtn.innerText;
        nextBtn.innerText = 'Checking...';

        try {
            const res = await fetch(`/api/v1/check-duplicate?emirates_id=${encodeURIComponent(val)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            
            lastCheckedEid = val;
            isEidDuplicate = data.is_duplicate;

            if (isEidDuplicate) {
                errorEl.classList.remove('hidden');
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                input.classList.add('border-red-500');
            } else {
                errorEl.classList.add('hidden');
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                input.classList.remove('border-red-500');
            }
        } catch (err) {
            console.error(err);
        } finally {
            isCheckingEid = false;
            nextBtn.innerText = originalBtnText;
            if (!isEidDuplicate) {
                nextBtn.disabled = false;
            }
        }
        
        return isEidDuplicate;
    }

    async function goToStep2() {
        const eidInput = document.getElementById('emirates_id_number');
        const val = eidInput.value.trim();

        if(!val) {
            document.getElementById('registrationForm').reportValidity();
            return;
        }

        // Check format 784-YYYY-NNNNNNN-C
        const regex = /^784-\d{4}-\d{7}-\d{1}$/;
        if(!regex.test(val)) {
            eidInput.setCustomValidity("Format must be 784-YYYY-NNNNNNN-C");
            eidInput.reportValidity();
            return;
        } else {
            eidInput.setCustomValidity("");
        }

        // Wait if a check is currently in flight
        if (isCheckingEid) {
            return; // Button should be disabled anyway, but we abort click just in case
        }

        // Ensure we checked the *current* input value before proceeding
        if (val !== lastCheckedEid) {
            await checkEidDuplicate(val);
        }

        if(isEidDuplicate) {
            alert('This Emirates ID is already registered.');
            return;
        }

        nextStep(2);
    }

    function goToStep3() {
        const name = document.getElementById('full_name').value;
        const mobile = document.getElementById('mobile_number_local').value;
        const nat = document.getElementById('nationality').value;
        const area = document.getElementById('area_of_residence').value;
        const lang = document.getElementById('preferred_language').value;
        const ageChecked = document.querySelector('input[name="age_group"]:checked');
        
        if(!name || !mobile || !nat || !area || !lang || !ageChecked) {
            document.getElementById('registrationForm').reportValidity();
            return;
        }

        nextStep(3);
    }

    function validateEmiratesId(input) {
        const val = input.value.trim();
        const regex = /^784-\d{4}-\d{7}-\d{1}$/;
        
        if(val && regex.test(val)) {
            checkEidDuplicate(val);
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const prompt = document.getElementById('upload-prompt');
        const removeBtn = document.getElementById('remove-image-btn');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                prompt.classList.add('hidden');
                removeBtn.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.getElementById('emirates_id_image');
        const preview = document.getElementById('image-preview');
        const prompt = document.getElementById('upload-prompt');
        const removeBtn = document.getElementById('remove-image-btn');
        
        input.value = '';
        preview.src = '';
        preview.classList.add('hidden');
        prompt.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endsection
