@php
    $countries = \App\Models\Country::where('is_active', true)
        ->orderBy('name')
        ->get();
    
    $selectedCountry = $selectedCountry ?? null;
    $selectedPhoneCode = $selectedPhoneCode ?? null;
    $fieldName = $fieldName ?? 'phone_country_code';
    $phoneFieldName = $phoneFieldName ?? 'phone_number';
    $countryCodeFieldName = $countryCodeFieldName ?? 'country_code';
    $required = $required ?? false;
    $showCountryCode = $showCountryCode ?? true;
@endphp

<div class="phone-input-group">
    <div class="row">
        @if($showCountryCode)
            <div class="col-md-4">
                <label for="{{ $fieldName }}" class="form-label">
                    Country Code {{ $required ? '<span class="text-danger">*</span>' : '' }}
                </label>
                <select 
                    {{ $attributes->merge(['class' => 'form-select', 'id' => $fieldName, 'name' => $fieldName]) }}
                    {{ $required ? 'required' : '' }}
                >
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option 
                            value="{{ $country->code }}" 
                            data-phone-code="{{ $country->phone_code }}"
                            {{ $selectedCountry == $country->code ? 'selected' : '' }}
                        >
                            {{ $country->name }} ({{ $country->phone_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="{{ $phoneFieldName }}" class="form-label">
                    Phone Number {{ $required ? '<span class="text-danger">*</span>' : '' }}
                </label>
                <input 
                    type="tel" 
                    {{ $attributes->merge(['class' => 'form-control', 'id' => $phoneFieldName, 'name' => $phoneFieldName]) }}
                    value="{{ $selectedPhoneCode }}"
                    placeholder="1234567890"
                    {{ $required ? 'required' : '' }}
                >
            </div>
        @else
            <div class="col-md-6">
                <label for="{{ $phoneFieldName }}" class="form-label">
                    Phone Number {{ $required ? '<span class="text-danger">*</span>' : '' }}
                </label>
                <div class="input-group">
                    <select 
                        {{ $attributes->merge(['class' => 'form-select', 'id' => $fieldName, 'name' => $fieldName, 'style' => 'max-width: 120px;']) }}
                        {{ $required ? 'required' : '' }}
                    >
                        <option value="">Code</option>
                        @foreach($countries as $country)
                            <option 
                                value="{{ $country->code }}" 
                                data-phone-code="{{ $country->phone_code }}"
                                {{ $selectedCountry == $country->code ? 'selected' : '' }}
                            >
                                {{ $country->phone_code }}
                            </option>
                        @endforeach
                    </select>
                    <input 
                        type="tel" 
                        {{ $attributes->merge(['class' => 'form-control', 'id' => $phoneFieldName, 'name' => $phoneFieldName, 'placeholder' => 'Phone Number']) }}
                        value="{{ $selectedPhoneCode }}"
                        {{ $required ? 'required' : '' }}
                    >
                </div>
            </div>
        @endif
        
        @if($showCountryCode)
            <div class="col-md-4">
                <label for="{{ $countryCodeFieldName }}" class="form-label">
                    Country {{ $required ? '<span class="text-danger">*</span>' : '' }}
                </label>
                <select 
                    {{ $attributes->merge(['class' => 'form-select', 'id' => $countryCodeFieldName, 'name' => $countryCodeFieldName]) }}
                    {{ $required ? 'required' : '' }}
                >
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option 
                            value="{{ $country->code }}"
                            {{ $selectedCountry == $country->code ? 'selected' : '' }}
                        >
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
    
    <div class="mt-2">
        <small class="text-muted">
            <i class="ri-information-line"></i>
            Select your country to automatically set the correct phone code
        </small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('{{ $fieldName }}');
    const phoneInput = document.getElementById('{{ $phoneFieldName }}');
    
    if (countrySelect && phoneInput) {
        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const phoneCode = selectedOption.getAttribute('data-phone-code');
            
            // Auto-populate phone country code field if it exists separately
            const phoneCountryCodeInput = document.getElementById('phone_country_code');
            if (phoneCountryCodeInput && phoneCode) {
                phoneCountryCodeInput.value = phoneCode;
            }
        });
    }
});
</script>
