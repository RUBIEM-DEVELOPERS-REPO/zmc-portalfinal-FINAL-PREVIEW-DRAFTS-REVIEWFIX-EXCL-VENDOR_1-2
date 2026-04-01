@extends('layouts.staff')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Renewals Payment Verification</h1>
        <p class="text-gray-600 mt-2">Verify renewal payment submissions</p>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Pending Verification</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $kpis['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Verified Today</p>
            <p class="text-3xl font-bold text-green-600">{{ $kpis['verified_today'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">PayNow</p>
            <p class="text-3xl font-bold text-blue-600">{{ $kpis['paynow'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Proof Upload</p>
            <p class="text-3xl font-bold text-purple-600">{{ $kpis['proof'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <select name="payment_method" class="px-4 py-2 border rounded-lg">
                <option value="">All Payment Methods</option>
                <option value="PAYNOW" {{ request('payment_method') === 'PAYNOW' ? 'selected' : '' }}>PayNow</option>
                <option value="PROOF_UPLOAD" {{ request('payment_method') === 'PROOF_UPLOAD' ? 'selected' : '' }}>Proof Upload</option>
            </select>
            
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($renewal->payment_method === 'PAYNOW') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $renewal->getPaymentMethodLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $renewal->payment_submitted_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('staff.accounts.renewals.show', $renewal) }}" 
                                   class="text-black hover:text-gray-700">
                                    Review
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
            <p class="text-gray-500">No renewals pending verification</p>
        </div>
    @endif
</div>
@endsection
