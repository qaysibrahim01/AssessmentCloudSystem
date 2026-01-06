@extends('layouts.dashboard')

@section('content')

<div class="max-w-3xl mx-auto space-y-6 text-sm">

    <div class="flex justify-between items-start border-b pb-4">

        <div class="flex gap-2">
            <a href="{{ route('nra.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>
        </div>
    </div>


    <h1 class="text-2xl font-semibold">
        Create NRA (Noise Risk Assessment)
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
          action="{{ route('nra.store') }}"
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
                    <option value="Johor">Johor</option>
                    <option value="Kedah">Kedah</option>
                    <option value="Kelantan">Kelantan</option>
                    <option value="Melaka">Melaka</option>
                    <option value="Negeri Sembilan">Negeri Sembilan</option>
                    <option value="Pahang">Pahang</option>
                    <option value="Perak">Perak</option>
                    <option value="Perlis">Perlis</option>
                    <option value="Selangor">Selangor</option>
                    <option value="Penang">Penang</option>
                    <option value="Terengganu">Terengganu</option>
                    <option value="Sabah">Sabah</option>
                    <option value="Sarawak">Sarawak</option>
                    <option value="WP Kuala Lumpur">WP Kuala Lumpur</option>
                    <option value="WP Putrajaya">WP Putrajaya</option>
                    <option value="WP Labuan">WP Labuan</option>
                </select>

                <select name="city"
                        id="city"
                        class="border px-3 py-2 rounded w-full"
                        required>
                    <option value="">Select District</option>
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
            <textarea name="assessment_scope"
                      rows="3"
                      class="border px-3 py-2 rounded w-full">{{ old('assessment_scope') }}</textarea>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-between">
            <a href="{{ route('nra.index') }}"
                class="border px-4 py-2 rounded">
                Cancel
            </a>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded">
                Create &amp; Proceed
            </button>
        </div>
    </form>
</div>

<script>
const districts = {
    Johor: ["Batu Pahat","Johor Bahru","Kluang","Kota Tinggi","Kulai","Mersing","Muar","Pontian","Segamat","Tangkak"],
    Kedah: ["Baling","Bandar Baharu","Kota Setar","Kuala Muda","Kubang Pasu","Kulim","Langkawi","Padang Terap","Pendang","Pokok Sena","Sik","Yan"],
    Kelantan: ["Bachok","Gua Musang","Jeli","Kota Bharu","Kuala Krai","Machang","Pasir Mas","Pasir Puteh","Tanah Merah","Tumpat","Lojing"],
    Melaka: ["Alor Gajah","Central Melaka","Jasin"],
    "Negeri Sembilan": ["Jelebu","Jempol","Kuala Pilah","Port Dickson","Rembau","Seremban","Tampin"],
    Pahang: ["Bentong","Bera","Cameron Highlands","Jerantut","Kuantan","Lipis","Maran","Pekan","Raub","Rompin","Temerloh"],
    Perak: ["Bagan Datuk","Batang Padang","Hilir Perak","Hulu Perak","Kampar","Kerian","Kinta","Kuala Kangsar","Larut Matang dan Selama","Manjung","Muallim","Perak Tengah"],
    Perlis: ["Kangar","Padang Besar","Arau"],
    Penang: ["Barat Daya","Seberang Perai Tengah","Seberang Perai Utara","Seberang Perai Selatan","Timur Laut"],
    Sabah: ["Beaufort","Beluran","Keningau","Kota Belud","Kota Kinabalu","Kota Marudu","Kuala Penyu","Kudat","Kunak","Lahad Datu","Nabawan","Papar","Penampang","Pitas","Ranau","Sandakan","Semporna","Sipitang","Tambunan","Tawau","Tenom","Tongod","Tuaran","Putatan"],
    Sarawak: ["Betong","Bintulu","Kapit","Kuching","Limbang","Miri","Mukah","Samarahan","Sarikei","Serian","Sibu","Sri Aman"],
    Selangor: ["Gombak","Hulu Langat","Hulu Selangor","Klang","Kuala Langat","Kuala Selangor","Petaling","Sabak Bernam","Sepang"],
    Terengganu: ["Besut","Dungun","Hulu Terengganu","Kemaman","Kuala Nerus","Kuala Terengganu","Marang","Setiu"],
    "WP Kuala Lumpur": ["Kuala Lumpur"],
    "WP Putrajaya": ["Putrajaya"],
    "WP Labuan": ["Labuan"]
};

const stateEl = document.getElementById('state');
const cityEl = document.getElementById('city');

stateEl.addEventListener('change', function () {
    cityEl.innerHTML = '<option value=\"\">Select District</option>';
    (districts[this.value] || []).forEach(city => {
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
</script>
@endsection
