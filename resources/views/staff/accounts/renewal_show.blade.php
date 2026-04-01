@extends('layouts.staff')

@section('content')
<div class="container-fluid px-4 py-6 max-w-6xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Renewal Payment Verification</h1>
        <p class="text-gray-600 mt-2">{{ $renewal->getRenewalTypeLabel() }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Applicant Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Applicant Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $renewal->applicant->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $renewal->applicant->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Original Number</p>
                        <p class="font-medium">{{ $renewal->original_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Renewal Type</p>
                        <p class="font-medium">{{ ucfirst($renewal->renewal_type) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="font-medium">{{ $renewal->getPaymentMethodLabel() }}</p>
                    </div>
                    @if($renewal->payment_reference)
                        <div>
                            <p class="text-sm text-gray-600">Reference</p>
                            <p class="font-medium">{{ $renewal->payment_reference }}</p>
                        </div>
                    @endif
                    @if($renewal->payment_amount)
                        <div>
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-medium">${{ number_format($renewal->payment_amount, 2) }}</p>
                        </div>
                    @endif
                    @if($renewal->payment_date)
                        <div>
                            <p class="text-sm text-gray-600">Payment Date</p>
                            <p class="font-medium">{{ $renewal->payment_date->format('M d, Y') }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Submitted</p>
                        <p class="font-medium">{{ $renewal->payment_submitted_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($renewal->payment_proof_path)
                    <div class="mt-4">
                        <a href="{{ Storage::url($renewal->payment_proof_path) }}" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Proof Document
                        </a>
                    </div>
                @endif
            </div>

            <!-- Changes Requested -->
            @if($renewal->has_changes && $renewal->changeRequests->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Changes Requested</h2>
                    <div class="space-y-4">
                        @foreach($renewal->changeRequests as $change)
                            <div class="border rounded-lg p-4">
                                <p class="font-medium">{{ $change->field_name }}</p>
                                <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                    <div>
                                        <p class="text-gray-600">Old Value</p>
                                        <p>{{ $change->old_value }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">New Value</p>
                                        <p>{{ $change->new_value }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Verification Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Verification Actions</h3>
                
                <form method="POST" action="{{ route('staff.accounts.renewals.verify', $renewal) }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    
                    <button type="submit" 
                            name="action" 
                            value="verify"
                            class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
                        Verify Payment
                    </button>
                    
                    <button type="submit" 
                            name="action" 
                            value="reject"
                            class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                        Reject Payment
                    </button>
                </form>
            </div>

            <!-- Status Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Current Status</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $renewal->getStatusLabel() }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('staff.accounts.renewals.queue') }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
            Back to Queue
        </a>
    </div>
</div>
@endsection
