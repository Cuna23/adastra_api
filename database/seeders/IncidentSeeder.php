<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $users   = User::all();
        $itStaff = User::whereIn('role', ['admin', 'super_admin'])->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Run UserSeeder first.');
            return;
        }

        $incidents = [
            [
                'subject'     => 'Internet connection slow in meeting room',
                'description' => 'The internet connection in meeting room B3 has been extremely slow since this morning. Video calls keep dropping and file uploads are timing out.',
                'category'    => 'Network',
                'priority'    => 'High',
                'status'      => 'Open',
            ],
            [
                'subject'     => 'Laptop screen flickering',
                'description' => 'My laptop screen has been flickering intermittently for the past two days. It happens more frequently when running multiple applications.',
                'category'    => 'Hardware',
                'priority'    => 'Medium',
                'status'      => 'In Pending',
            ],
            [
                'subject'     => 'Microsoft Teams crashes on startup',
                'description' => 'Microsoft Teams keeps crashing immediately after opening. Already tried reinstalling but the issue persists. Unable to join any meetings.',
                'category'    => 'Software',
                'priority'    => 'Medium',
                'status'      => 'Resolved',
            ],
            [
                'subject'     => 'VPN disconnects frequently',
                'description' => 'VPN connection drops every 15-20 minutes while working from home. Have to manually reconnect each time which is disrupting productivity.',
                'category'    => 'Network',
                'priority'    => 'Medium',
                'status'      => 'Open',
            ],
            [
                'subject'     => 'Cannot install required software',
                'description' => 'Need to install AutoCAD 2024 for a project but getting an "Insufficient permissions" error. Requires IT admin approval to proceed.',
                'category'    => 'Software',
                'priority'    => 'High',
                'status'      => 'Review',
            ],
        ];

        $logTemplates = [
            'Open' => [
                ['action' => 'Created', 'description' => 'Ticket submitted'],
            ],
            'In Pending' => [
                ['action' => 'Created', 'description' => 'Ticket submitted'],
                ['action' => 'Updated', 'description' => 'Ticket assigned to IT support'],
                ['action' => 'Updated', 'description' => 'Status changed to In Pending — awaiting user response'],
            ],
            'Review' => [
                ['action' => 'Created', 'description' => 'Ticket submitted'],
                ['action' => 'Updated', 'description' => 'Ticket assigned to IT support'],
                ['action' => 'Updated', 'description' => 'Status changed to Review — awaiting approval'],
            ],
            'Resolved' => [
                ['action' => 'Created',  'description' => 'Ticket submitted'],
                ['action' => 'Updated',  'description' => 'Ticket assigned to IT support'],
                ['action' => 'Updated',  'description' => 'Status changed to In Pending — issue identified'],
                ['action' => 'Resolved', 'description' => 'Status changed to Resolved — issue confirmed fixed'],
            ],
        ];

        foreach ($incidents as $data) {
            // ── Stable match key: subject ONLY ──────────────────────────
            // Reporter/assignee/timestamps are deterministic per subject too,
            // so re-running the seeder updates the same row instead of duplicating.
            $reporter   = $users[$users->search(fn ($u) => true) % $users->count()] ?? $users->first();
            $reporter   = $users->get(crc32($data['subject']) % $users->count());
            $assignedTo = $itStaff->isNotEmpty()
                ? $itStaff->get(crc32($data['subject'] . 'assign') % $itStaff->count())
                : null;
            $createdAt  = now()->subDays((crc32($data['subject']) % 14) + 1);

            $incident = Incident::updateOrCreate(
                ['subject' => $data['subject']],   // match key
                [
                    'ticket_no'   => 'TEMP-' . crc32($data['subject']), 
                    'user_id'     => $reporter->id,
                    'assigned_to' => $data['status'] !== 'Open' ? ($assignedTo?->id) : null,
                    'description' => $data['description'],
                    'category'    => $data['category'],
                    'priority'    => $data['priority'],
                    'status'      => $data['status'],
                    'attachment'  => null,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]
            );

            if (str_starts_with($incident->ticket_no, 'TEMP-')) {
                $incident->update([
                    'ticket_no' => 'INC-' . date('Y') . '-' . str_pad($incident->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
            // Clear old logs for this incident before re-inserting, to avoid duplicate logs too
            IncidentLog::where('incident_id', $incident->id)->delete();

            $logTime = $createdAt;

            foreach ($logTemplates[$data['status']] as $log) {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'user_id'     => $log['action'] === 'Created'
                        ? $reporter->id
                        : ($assignedTo?->id ?? $reporter->id),
                    'action'      => $log['action'],
                    'description' => $log['description'],
                    'created_at'  => $logTime,
                    'updated_at'  => $logTime,
                ]);

                $logTime = $logTime->copy()->addHours(rand(1, 8));
            }
        }

        $this->command->info('Incident seeder completed — 5 incidents created/updated.');
    }
}