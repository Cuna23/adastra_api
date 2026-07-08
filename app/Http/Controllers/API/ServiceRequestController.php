<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['requester', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sr_number', 'like', "%{$search}%")
                  ->orWhere('request_title', 'like', "%{$search}%");
            });
        }

        if (Auth::user()->role === 'staff') {
            $query->where('requester_id', Auth::id());
        }

        return response()->json($query->latest()->get());
    }

    public function show(int $id)
    {
        $serviceRequest = ServiceRequest::with(['requester', 'approver'])->findOrFail($id);
        return response()->json($serviceRequest);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_title' => 'required|string|max:255',
            'request_type' => 'required|in:asset_request,software_installation,account_access,other',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:10',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string',
            'needed_by_date' => 'required|date|after_or_equal:today',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $validated['requester_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['approver_id'] = null; // TODO: resolve HOD logic

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $validated['attachment_name'] = $file->getClientOriginalName();
            $validated['attachment'] = $file->store('service_requests', 'public');
        }

        $serviceRequest = ServiceRequest::create($validated);

        return response()->json($serviceRequest, 201);
    }

    public function update(Request $request, int $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $validated = $request->validate([
            'request_title' => 'sometimes|string|max:255',
            'request_type' => 'sometimes|in:asset_request,software_installation,account_access,other',
            'category' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1|max:10',
            'priority' => 'sometimes|in:low,medium,high',
            'description' => 'sometimes|string',
            'needed_by_date' => 'sometimes|date',
            'attachment' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($serviceRequest->attachment) {
                Storage::disk('public')->delete($serviceRequest->attachment);
            }
            $file = $request->file('attachment');
            $validated['attachment_name'] = $file->getClientOriginalName();
            $validated['attachment'] = $file->store('service_requests', 'public');
        }

        $serviceRequest->update($validated);

        return response()->json($serviceRequest);
    }

    public function approve(int $id)
    {
        // Role check — hanya HOD, admin, super_admin boleh approve
        abort_unless(
            in_array(Auth::user()->role, ['hod', 'admin', 'super_admin']),
            403,
            'You are not authorized to approve this request.'
        );

        $serviceRequest = ServiceRequest::findOrFail($id);

        // Guard — elak double action 
        if ($serviceRequest->status !== 'pending') {
            return response()->json([
                'message' => 'This request has already been reviewed by ' .
                    ($serviceRequest->approver->name ?? 'another approver') . '.',
            ], 409);
        }

        $serviceRequest->update([
            'status' => 'approved',
            'approver_id' => Auth::id(), // sesiapa yang klik, dia jadi approver
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json($serviceRequest->load(['requester', 'approver']));
    }

    public function reject(Request $request, int $id)
    {
        // Role check — hanya HOD, admin, super_admin boleh reject
        abort_unless(
            in_array(Auth::user()->role, ['hod', 'admin', 'super_admin']),
            403,
            'You are not authorized to reject this request.'
        );

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);

        // Guard — elak double action
        if ($serviceRequest->status !== 'pending') {
            return response()->json([
                'message' => 'This request has already been reviewed by ' .
                    ($serviceRequest->approver->name ?? 'another approver') . '.',
            ], 409);
        }

        $serviceRequest->update([
            'status' => 'rejected',
            'approver_id' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json($serviceRequest->load(['requester', 'approver']));
    }
}