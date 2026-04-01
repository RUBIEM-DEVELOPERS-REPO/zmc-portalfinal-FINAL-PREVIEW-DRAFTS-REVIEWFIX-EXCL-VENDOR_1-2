<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class NoticesEventsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user && ($user->role ?? '') === 'mediahouse') {
            return redirect()->route('mediahouse.notices');
        }
        return redirect()->route('accreditation.notices');
    }
}
