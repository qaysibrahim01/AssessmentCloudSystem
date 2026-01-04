@extends('layouts.dashboard')

@section('content')

<div class="max-w-3xl mx-auto space-y-6 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start border-b pb-4">
        <a href="{{ route('admin.chra.index') }}"
           class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
            Back
        </a>
    </div>

    <h1 class="text-2xl font-semibold">
        Upload Official CHRA Report
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
          action="{{ route('admin.chra.uploaded.store') }}"
          enctype="multipart/form-data"
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

            <input id="address_line_1"
                   placeholder="Address Line 1"
                   class="border px-3 py-2 rounded w-full"
                   required>

            <input id="address_line_2"
                   placeholder="Address Line 2 (Optional)"
                   class="border px-3 py-2 rounded w-full">

            <div class="grid grid-cols-3 gap-4">
                <select id="state"
                        class="border px-3 py-2 rounded w-full"
                        required>
                    <option value="">Select State</option>
                    <option value="Selangor">Selangor</option>
                    <option value="WP Kuala Lumpur">WP Kuala Lumpur</option>
                    <option value="WP Putrajaya">WP Putrajaya</option>
                    <option value="Johor">Johor</option>
                    <option value="Penang">Penang</option>
                </select>

                <select id="city"
                        class="border px-3 py-2 rounded w-full"
                        required>
                    <option value="">Select City</option>
                </select>

                <input id="postcode"
                       placeholder="Postcode"
                       class="border px-3 py-2 rounded w-full"
                       required>
            </div>
        </div>

        <!-- COMBINED ADDRESS -->
        <input type="hidden" name="company_address" id="company_address">

        <!-- ASSESSMENT DATE -->
        <div>
            <label class="block font-medium">Assessment Date</label>
            <input type="date"
                   name="assessment_date"
                   value="{{ old('assessment_date') }}"
                   required
                   class="border px-3 py-2 rounded w-full">
        </div>

        <!-- REPORT SUMMARY -->
        <div>
            <label class="block font-medium">Report Summary</label>
            <textarea name="summary"
                      rows="3"
                      required
                      class="border px-3 py-2 rounded w-full">{{ old('summary') }}</textarea>
        </div>

        <!-- PDF -->
        <div>
            <label class="block font-medium">Upload Official PDF</label>
            <input type="file"
                   name="pdf"
                   accept="application/pdf"
                   required>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-between">
            <a href="{{ route('admin.chra.index') }}"
               class="border px-4 py-2 rounded">
                Cancel
            </a>

            <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded">
                Upload & Register
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
}
</script>

@endsection
