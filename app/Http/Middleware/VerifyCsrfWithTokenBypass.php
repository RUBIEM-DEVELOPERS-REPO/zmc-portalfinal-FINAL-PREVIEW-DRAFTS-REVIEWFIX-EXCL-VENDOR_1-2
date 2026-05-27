<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

class VerifyCsrfWithTokenBypass extends ValidateCsrfToken
{
    protected $except = [
        'logout',
        'staff/logout',
        'payments/*/upload-proof',
        'payments/*/upload-waiver',
        'payments/*/initiate',
        'payments/*/initiate-mobile',
        'payments/*/submit-reference',
        'portal/accreditation/submit',
        'portal/accreditation/save-draft',
        'portal/accreditation/submit-ap5',
        'portal/media-house/submit',
        'portal/media-house/save-draft',
        'portal/media-house/register',
        'portal/media-house/submit-ap5',
        'portal/media-house/save-draft-ap5',
        'settings/theme/ajax',
        'staff/*/applications/*/approve',
        'staff/*/applications/*/forward-to-registrar',
        'staff/*/applications/*/fix-request',
        'staff/*/applications/*/push-to-accounts',
        'staff/*/applications/*/request-correction',
        'staff/*/applications/*/payment/reject',
        'staff/*/applications/*/proof/approve',
        'staff/*/applications/*/proof/reject',
        'staff/*/applications/*/waiver/approve',
        'staff/*/applications/*/waiver/reject',
        'staff/*/cash-payment',
        'staff/*/physical-intake',
        'staff/*/reminders',
        'staff/*/templates',
        'staff/*/templates/*/activate',
        'staff/*/applications/*/mark-reviewed',
        'staff/*/applications/batch-mark-reviewed',
    ];

    protected function tokensMatch($request): bool
    {
        if ($request->attributes->get('_token_authenticated')) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
