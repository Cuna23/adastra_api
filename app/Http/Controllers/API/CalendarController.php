<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // ── Service request deadlines ──
        $deadlineQuery = ServiceRequest::whereBetween('needed_by_date', [$start->toDateString(), $end->toDateString()]);

        if ($user->role === 'staff') {
            $deadlineQuery->where('requester_id', $user->id);
        } else {
            // admin/super_admin: only pending ones — belum ditindak
            $deadlineQuery->where('status', 'pending');
        }

        $deadlines = $deadlineQuery->get()->map(fn ($sr) => [
            'id' => $sr->id,
            'type' => 'deadline',
            'title' => $sr->request_title,
            'subtitle' => $sr->sr_number,
            'date' => $sr->needed_by_date->format('Y-m-d'),
            'overdue' => $sr->needed_by_date->isPast() && !$sr->needed_by_date->isToday(),
        ]);

        // ── Personal reminders ──
        $reminders = Reminder::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'type' => 'reminder',
                'title' => $r->title,
                'subtitle' => $r->note,
                'date' => $r->date->format('Y-m-d'),
                'overdue' => false,
            ]);

        return response()->json(['events' => $deadlines->concat($reminders)->values()]);
    }
}