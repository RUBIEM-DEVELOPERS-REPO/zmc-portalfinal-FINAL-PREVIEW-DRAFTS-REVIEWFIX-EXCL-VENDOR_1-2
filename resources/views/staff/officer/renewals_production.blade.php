@extends('layouts.staff')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Renewals Production</h1>
        <p class="text-gray-600 mt-2">Generate renewed documents</p>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Ready for Production</p>
            <p class="text-3xl font-bold text-blue-600">{{ $kpis['ready'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">In Production</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $kpis['in_production'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Ready for Collection</p>
            <p class="text-3xl font-bold text-green-600">{{ $kpis['ready_for_collection'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <select name="renewal_type" class="px-4 py-2 border rounded-lg">
                <option value="">All Types</option>
                <option value="accreditation" {{ request('renewal_type') === 'accreditation' ? 'selected' : '' }}>Accreditation</option>
                <option value="registration" {{ request('renewal_type') === 'registration' ? 'selected' : '' }}>Registration</option>
                <option value="permission" {{ request('renewal_type') === 'permission' ? 'selected' : '' }}>Permission</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-black text-yellow-400 rounded-lg hover:bg-gray-800">
                Filter
            </button>
        </form>
    </div>

    <!-- Renewals Table -->
    @if($renewals->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Original Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($renewals as $renewal)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium">{{ ucfirst($renewal->renewal_type) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $renewal->applicant->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $renewal->applicant->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $renewal->original_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $renewal->payment_verified_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('staff.officer.renewals.production.show', $renewal) }}" 
                                   class="text-black hover:text-gray-700">
                                    Process
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $renewals->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">No renewals ready for production</p>
        </div>
    @endif
</div>
@endsection
