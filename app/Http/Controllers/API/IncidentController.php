<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        \Log::info('Incident index check', [
        'auth_id'   => $user->id,
        'auth_role' => $user->role,
        ]);

        $query = Incident::with(['user', 'assignedUser']);

        if ($user->role === 'staff') {
            $query->where('user_id', $user->id);
        }

        $incidents = $query->latest()->get();
        \Log::info('Incidents found', ['count' => $incidents->count()]);

        return response()->json([
            'success' => true,
            'data'    => $incidents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required',
            'priority'    => 'required',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $safeName       = time() . '_' . $attachmentName;

            $attachmentPath = $file->storeAs(
                'incident_attachments',
                $safeName,
                'public'
            );
        }

        $incident = Incident::create([
            'ticket_no'   => '',
            'user_id'     => Auth::id(),
            'subject'     => $request->subject,
            'description' => $request->description,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'attachment'  => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status'      => 'In Pending', // default to In Pending for staff review
            'assigned_to'     => \App\Models\User::where('role', 'super_admin')->value('id')
        ]);

        $incident->update([
            'ticket_no' => 'INC-' . date('Y') . '-' .
                str_pad($incident->id, 5, '0', STR_PAD_LEFT)
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => Auth::id(),
            'action'      => 'Created',
            'description' => 'Ticket submitted'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident created successfully',
            'data'    => $incident
        ], 201);
    }

    public function show($id)
    {
        $incident = Incident::with([
            'user',
            'assignedUser',
            'logs.user'
        ])->find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $incident
        ]);
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found'
            ], 404);
        }

        // ── Staff adding a note — NEVER touches incident fields ───────────
        if ($request->has('note')) {
            $note = trim($request->input('note'));

            if (empty($note)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note cannot be empty'
                ], 422);
            }

            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => Auth::id(),
                'action'      => 'Note',
                'description' => $note
            ]);

            // Return fresh incident with logs
            return response()->json([
                'success' => true,
                'message' => 'Note added',
                'data'    => $incident->fresh(['user', 'assignedUser', 'logs.user'])
            ]);
        }

        // ── Admin/super_admin updating incident fields ────────────────────
        $incident->update($request->only([
            'subject',
            'category',
            'priority',
            'status',
            'assigned_to',
            'resolution'
            // 'description' intentionally excluded — cannot be edited after submit
        ]));

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => Auth::id(),
            'action'      => 'Updated',
            'description' => 'Incident updated'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident updated successfully',
            'data'    => $incident->fresh(['user', 'assignedUser', 'logs.user'])
        ]);
    }
    
        public function updateLog(Request $request, $incidentId, $logId)
    {
        $log = IncidentLog::where('id', $logId)
            ->where('incident_id', $incidentId)
            ->where('user_id', Auth::id())  // only own notes
            ->where('action', 'Note')
            ->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Log not found'], 404);
        }

        $note = trim($request->input('note', ''));
        if (empty($note)) {
            return response()->json(['success' => false, 'message' => 'Note cannot be empty'], 422);
        }

        $log->update(['description' => $note]);

        $incident = Incident::with(['user', 'assignedUser', 'logs.user'])->find($incidentId);

        return response()->json(['success' => true, 'message' => 'Note updated', 'data' => $incident]);
    }

    // ── Delete a note log ─────────────────────────────────────────────────────
    public function destroyLog($incidentId, $logId)
    {
        $log = IncidentLog::where('id', $logId)
            ->where('incident_id', $incidentId)
            ->where('user_id', Auth::id())  // only own notes
            ->where('action', 'Note')
            ->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Log not found'], 404);
        }

        $log->delete();

        $incident = Incident::with(['user', 'assignedUser', 'logs.user'])->find($incidentId);

        return response()->json(['success' => true, 'message' => 'Note deleted', 'data' => $incident]);
    } 

    public function destroy($id)
    {
        $incident = Incident::find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found'
            ], 404);
        }

        $incident->delete();

        return response()->json([
            'success' => true,
            'message' => 'Incident deleted successfully'
        ]);
    }

    //graph
    public function weeklyStats(Request $request)
    {
        $days = (int) $request->query('days', 7);
        $days = in_array($days, [7, 14, 30, 60]) ? $days : 7;

        $start = now()->subDays($days - 1)->startOfDay();
        $end   = now()->endOfDay();

        $rows = Incident::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn ($inc) => $inc->created_at->format('Y-m-d'));

        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key  = $date->format('Y-m-d');

            $data[] = [
                'day'   => $days <= 7 ? $date->format('D') : $date->format('j/n'), // "Mon" vs "17/6"
                'date'  => $key,
                'count' => $rows->get($key)?->count() ?? 0,
            ];
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}   