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
                'status'      => 'In Progress',
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
                'status'      => 'In Progress',
            ],
        ];

        $logTemplates = [
            'Open' => [
                ['action' => 'Created', 'description' => 'Ticket submitted'],
            ],
            'In Progress' => [
                ['action' => 'Created', 'description' => 'Ticket submitted'],
                ['action' => 'Updated', 'description' => 'Ticket assigned to IT support'],
                ['action' => 'Updated', 'description' => 'Status changed to In Progress — currently investigating'],
            ],
            'Resolved' => [
                ['action' => 'Created',  'description' => 'Ticket submitted'],
                ['action' => 'Updated',  'description' => 'Ticket assigned to IT support'],
                ['action' => 'Updated',  'description' => 'Status changed to In Progress — issue identified'],
                ['action' => 'Resolved', 'description' => 'Status changed to Resolved — issue confirmed fixed'],
            ],
        ];

        foreach ($incidents as $data) {
            $reporter   = $users->random();
            $assignedTo = $itStaff->isNotEmpty() ? $itStaff->random() : null;
            $createdAt  = now()->subDays(rand(1, 14))->subHours(rand(0, 23));

            $incident = Incident::create([
                'ticket_no'   => '',
                'user_id'     => $reporter->id,
                'assigned_to' => $data['status'] !== 'Open'
                    ? ($assignedTo?->id)
                    : null,
                'subject'     => $data['subject'],
                'description' => $data['description'],
                'category'    => $data['category'],
                'priority'    => $data['priority'],
                'status'      => $data['status'],
                'attachment'  => null,
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ]);

            $incident->update([
                'ticket_no' => 'INC-' . date('Y') . '-' . str_pad($incident->id, 5, '0', STR_PAD_LEFT),
            ]);

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

        $this->command->info('Incident seeder completed — 5 incidents created.');
    }
}