<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to view organization settings.');
        }
        $organization = auth()->user()->organization;

        $activities = Activity::with(['causer', 'subject'])
            ->forOrganization($organization)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('ActivityLog/Index', [
            'activities' => $activities,
        ]);
    }
}
