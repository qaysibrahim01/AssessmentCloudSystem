@extends('layouts.dashboard')

@section('content')

<div class="max-w-3xl mx-auto space-y-6 text-sm">

    <div class="flex justify-between items-start border-b pb-4">

        <div class="flex gap-2">
            <a href="{{ route('chra.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>

        </div>
    </div>


    <h1 class="text-2xl font-semibold">
        Create Chemical Health Risk Assessment (CHRA)
    </h1>

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('chra.store') }}"
          onsubmit="combineAddress()"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <!-- COMPANY NAME -->
        <div>
            <label class="block font-medium">Company Name</label>
            <input name="company_name"
                   value="{{ old('company_name') }}"
                   required
                   class="border px-3 py-2 rounded w-full">
        </div>

        <!-- COMPANY ADDRESS -->
        <div class="space-y-2">
            <label class="block font-medium">Company Address</label>

            <input name="address_line_1"
                   id="address_line_1"
                   value="{{ old('address_line_1') }}"
                   placeholder="Address Line 1"
                   class="border px-3 py-2 rounded w-full"
                   required>

            <input name="address_line_2"
                   id="address_line_2"
                   value="{{ old('address_line_2') }}"
                   placeholder="Address Line 2 (Optional)"
                   class="border px-3 py-2 rounded w-full">

            <div class="grid grid-cols-3 gap-4">
                <select name="state"
                        id="state"
                        class="border px-3 py-2 rounded w-full"
                        required>
                    <option value="">Select State</option>
                    <option value="Selangor">Selangor</option>
                    <option value="WP Kuala Lumpur">WP Kuala Lumpur</option>
                    <option value="WP Putrajaya">WP Putrajaya</option>
                    <option value="Johor">Johor</option>
                    <option value="Penang">Penang</option>
                </select>

                <select name="city"
                        id="city"
                        class="border px-3 py-2 rounded w-full"
                        required>
                    <option value="">Select City</option>
                </select>

                <input name="postcode"
                       id="postcode"
                       value="{{ old('postcode') }}"
                       placeholder="Postcode"
                       class="border px-3 py-2 rounded w-full"
                       required>
            </div>
        </div>

        <!-- COMBINED ADDRESS -->
        <input type="hidden"
               name="company_address"
               id="company_address"
               value="{{ old('company_address') }}">

        <!-- ASSESSMENT TYPE -->
        <div>
            <label class="block font-medium">Type of Assessment</label>
            <select name="assessment_type"
                    class="border px-3 py-2 rounded w-full">
                <option value="initial">Initial Assessment</option>
                <option value="reassessment">Re-Assessment</option>
            </select>
        </div>

        <!-- ASSESSMENT DATE -->
        <div>
            <label class="block font-medium">Assessment Date</label>
            <input type="date"
                   name="assessment_date"
                   value="{{ old('assessment_date') }}"
                   class="border px-3 py-2 rounded w-full"
                   required>
        </div>

        <!-- SCOPE -->
        <div>
            <label class="block font-medium">Assessment Scope</label>
            <textarea name="description"
                      rows="3"
                      class="border px-3 py-2 rounded w-full">{{ old('description') }}</textarea>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-between">
            <a href="{{ route('chra.index') }}"
                class="border px-4 py-2 rounded">
                Cancel
            </a>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded">
                Create & Proceed
            </button>
        </div>
    </form>
</div>

<script>
const cities = {
    Selangor: ["Shah Alam","Petaling Jaya","Subang Jaya","Klang"],
    "WP Kuala Lumpur": ["Kuala Lumpur"],
    "WP Putrajaya": ["Putrajaya"],
    Johor: ["Johor Bahru","Skudai"],
    Penang: ["George Town","Bayan Lepas"]
};

const stateEl = document.getElementById('state');
const cityEl = document.getElementById('city');

stateEl.addEventListener('change', function () {
    cityEl.innerHTML = '<option value="">Select City</option>';
    (cities[this.value] || []).forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        opt.textContent = city;
        cityEl.appendChild(opt);
    });
});

function combineAddress() {
    const parts = [
        document.getElementById('address_line_1').value,
        document.getElementById('address_line_2').value,
        cityEl.value,
        document.getElementById('postcode').value,
        stateEl.value
    ].filter(Boolean);

    document.getElementById('company_address').value = parts.join(', ');

    return true;
}

// live update (UX)
document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', combineAddress);
});
</script>

@endsection
