<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Get all incidents
     */
    public function index()
    {
        $incidents = Incident::with([
            'user',
            'assignedUser'
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $incidents
        ]);
    }

    /**
     * Create new incident
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required',
            'priority' => 'required',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request
                ->file('attachment')
                ->store('incident_attachments', 'public');
        }

        $incident = Incident::create([
            'ticket_no' => '',
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'attachment' => $attachmentPath,
            'status' => 'Open'
        ]);

        // Generate Ticket Number
        $incident->update([
            'ticket_no' => 'INC-' .
                date('Y') . '-' .
                str_pad($incident->id, 5, '0', STR_PAD_LEFT)
        ]);

        // Create Log
        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => 'Ticket submitted'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident created successfully',
            'data' => $incident
        ], 201);
    }

    /**
     * Show single incident
     */
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
            'data' => $incident
        ]);
    }

    /**
     * Update incident
     */
    public function update(Request $request, $id)
    {
        $incident = Incident::find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found'
            ], 404);
        }

        $incident->update($request->only([
            'subject',
            'description',
            'category',
            'priority',
            'status',
            'assigned_to',
            'resolution'
        ]));

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => 'Incident updated'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident updated successfully',
            'data' => $incident
        ]);
    }

    /**
     * Delete incident
     */
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