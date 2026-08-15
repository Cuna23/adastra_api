<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Incident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $user->role === 'staff'
            ? $this->staffStats($user)
            : $this->adminStats();
    }

    private function adminStats()
    {
        $incidentsByStatus = Incident::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $metrics = [
            'open_incidents'   => Incident::whereIn('status', ['Open', 'In Pending'])->count(),
            'pending_requests' => ServiceRequest::where('status', 'pending')->count(),
            'total_assets'     => Asset::count(),
            'active_staff'     => User::count(),
        ];

        $needsAttention = collect()
            ->concat(
                Incident::whereIn('status', ['Open', 'In Pending'])
                    ->orderByRaw("FIELD(priority, 'High','Medium','Low')")
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($i) => [
                        'type'     => 'incident',
                        'id'       => $i->id,
                        'title'    => $i->subject,
                        'subtitle' => $i->ticket_no,
                        'priority' => strtolower($i->priority),
                        'created_at' => $i->created_at,
                    ])
            )
            ->concat(
                ServiceRequest::where('status', 'pending')
                    ->orderByRaw("FIELD(priority, 'high','medium','low')")
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($sr) => [
                        'type'     => 'service_request',
                        'id'       => $sr->id,
                        'title'    => $sr->request_title,
                        'subtitle' => $sr->sr_number,
                        'priority' => $sr->priority,
                        'created_at' => $sr->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        return response()->json([
            'role' => 'admin',
            'metrics' => $metrics,
            'incidents_by_status' => [
                ['label' => 'Open', 'count' => $incidentsByStatus['Open'] ?? 0],
                ['label' => 'In Pending', 'count' => $incidentsByStatus['In Pending'] ?? 0],
                ['label' => 'Resolved', 'count' => $incidentsByStatus['Resolved'] ?? 0],
            ],
            'needs_attention' => $needsAttention,
        ]);
    }

    private function staffStats($user)
    {
        $metrics = [
            'my_open_incidents'  => Incident::where('user_id', $user->id)
                ->whereIn('status', ['Open', 'In Pending'])
                ->count(),
            'my_pending_requests' => ServiceRequest::where('requester_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'my_assets' => Asset::whereRaw('LOWER(assigned_to) = ?', [strtolower($user->name)])->count(),
        ];

        $recentTickets = collect()
            ->concat(
                Incident::where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($i) => [
                        'type'       => 'incident',
                        'id'         => $i->id,
                        'title'      => $i->subject,
                        'status'     => strtolower($i->status),
                        'created_at' => $i->created_at,
                    ])
            )
            ->concat(
                ServiceRequest::where('requester_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($sr) => [
                        'type'       => 'service_request',
                        'id'         => $sr->id,
                        'title'      => $sr->request_title,
                        'status'     => $sr->status,
                        'created_at' => $sr->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        return response()->json([
            'role' => 'staff',
            'metrics' => $metrics,
            'recent_tickets' => $recentTickets,
        ]);
    }
}