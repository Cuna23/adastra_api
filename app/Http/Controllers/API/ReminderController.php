<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'date' => 'required|date',
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'note' => $validated['note'] ?? null,
            'date' => $validated['date'],
        ]);

        return response()->json(['message' => 'Reminder added', 'data' => $reminder], 201);
    }

    public function update(Request $request, string $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'note' => 'nullable|string|max:1000',
            'date' => 'sometimes|date',
        ]);

        $reminder->update($validated);

        return response()->json(['message' => 'Reminder updated', 'data' => $reminder]);
    }

    public function destroy(string $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);
        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted']);
    }
}