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

        $query = Incident::with(['user', 'assignedUser']);

        if ($user->role === 'staff') {
            $query->where('user_id', $user->id);
        }

        $incidents = $query->latest()->get();

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
            'status'      => 'Open'
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
}   