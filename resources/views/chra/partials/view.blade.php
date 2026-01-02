<div class="space-y-6 text-sm">

    {{-- COMPANY INFO --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Company Information</h2>

        <p><strong>Company Name:</strong> {{ $chra->company_name }}</p>
        <p><strong>Company Address:</strong> {{ $chra->company_address }}</p>
        <p><strong>Assessment Date:</strong>
            {{ optional($chra->assessment_date)->format('d M Y') }}
        </p>
        <p><strong>Status:</strong>
            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">
                {{ ucfirst($chra->status) }}
            </span>
        </p>
    </div>

    {{-- WORK UNITS --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Work Units</h2>

        <table class="w-full text-xs border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-2 py-1">Name</th>
                    <th class="border px-2 py-1">Work Area</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chra->workUnits as $unit)
                    <tr>
                        <td class="border px-2 py-1">{{ $unit->name }}</td>
                        <td class="border px-2 py-1">{{ $unit->work_area }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-2">
                            No work units recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CHEMICALS --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Chemicals</h2>

        <table class="w-full text-xs border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-2 py-1">Chemical</th>
                    <th class="border px-2 py-1">H-Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chra->chemicals as $chemical)
                    <tr>
                        <td class="border px-2 py-1">{{ $chemical->chemical_name }}</td>
                        <td class="border px-2 py-1">{{ $chemical->h_code ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-2">
                            No chemicals recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- RECOMMENDATIONS --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Recommendations</h2>

        <table class="w-full text-xs border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-2 py-1">Category</th>
                    <th class="border px-2 py-1">Priority</th>
                    <th class="border px-2 py-1">Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chra->recommendations as $rec)
                    <tr>
                        <td class="border px-2 py-1">{{ $rec->category }}</td>
                        <td class="border px-2 py-1">{{ $rec->action_priority }}</td>
                        <td class="border px-2 py-1">{{ $rec->recommendation }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-2">
                            No recommendations recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
