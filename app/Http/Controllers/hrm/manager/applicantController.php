<?php

namespace App\Http\Controllers\hrm\manager;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants.
     */
    public function index()
    {
        return Inertia::render('Dashboard/HRM/Applicants/application', [
            'applicants' => Applicant::with('interview')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'first_name' => $a->first_name,
                    'last_name' => $a->last_name,
                    'email' => $a->email,
                    'phone_number' => $a->phone_number,
                    'position_applied' => $a->position_applied,
                    'status' => $a->status,
                    'created_at' => $a->created_at,
                    'notice_period' => $a->notice_period,
                    'street_address' => $a->street_address,
                    'city' => $a->city,
                    'state_province' => $a->state_province,
                    'postal_zip_code' => $a->postal_zip_code,
                    'sss_file_url' => $a->sss_file ? Storage::url($a->sss_file) : null,
                    'philhealth_file_url' => $a->philhealth_file ? Storage::url($a->philhealth_file) : null,
                    'pagibig_file_url' => $a->pagibig_file ? Storage::url($a->pagibig_file) : null,
                    'has_interview' => $a->interview !== null,
                ]),
        ]); 
    }

    /**
     * Store a newly created applicant and their interview schedule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email',
            'phone_number' => 'required|string',
            'position_applied' => 'required|string',
            'expected_salary' => 'nullable|numeric',
            'notice_period' => 'required|string|in:immediate,15_days,30_days',
            'street_address' => 'required|string',
            'street_address_line2' => 'nullable|string',
            'city' => 'required|string',
            'state_province' => 'required|string',
            'postal_zip_code' => 'required|string',
            'textile_experience' => 'required|string|in:yes,no',
            'status' => 'nullable|string',
            'sss_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'philhealth_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pagibig_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'scheduled_at' => 'nullable|date',
            'interview_type' => 'nullable|string|in:phone,technical,behavioral,onsite,video',
            'duration' => 'nullable|integer|in:15,30,45,60',
            'interviewer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $data = $request->except([
                'sss_file', 'philhealth_file', 'pagibig_file',
                'scheduled_at', 'interview_type', 'duration',
                'interviewer', 'location', 'notes',
            ]);

            if (! isset($data['status'])) {
                $data['status'] = 'Pending';
            }

            if ($request->hasFile('sss_file')) {
                $data['sss_file'] = $request->file('sss_file')->store('applicants/ids', 'public');
            }
            if ($request->hasFile('philhealth_file')) {
                $data['philhealth_file'] = $request->file('philhealth_file')->store('applicants/ids', 'public');
            }
            if ($request->hasFile('pagibig_file')) {
                $data['pagibig_file'] = $request->file('pagibig_file')->store('applicants/ids', 'public');
            }

            $applicant = Applicant::create($data);

            if ($request->filled('scheduled_at')) {
                Interview::create([
                    'applicant_id' => $applicant->id,
                    'scheduled_at' => $request->scheduled_at,
                    'interview_type' => $request->interview_type,
                    'duration' => $request->duration ?? 45,
                    'interviewer' => $request->interviewer,
                    'location' => $request->location,
                    'notes' => $request->notes,
                ]);
            }

            return back()->with('success', 'Applicant registered successfully.');
        });
    }

    /**
     * Update the stage/status of an applicant.
     */
    public function updateStage(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        // Map frontend pipeline stages to database status values
        $statusMap = [
            'FOR INTERVIEW' => 'Interview',
            'FINAL INTERVIEW' => 'final',
            'ONBOARDING' => 'onboard',
            'Rejected' => 'Rejected',
        ];

        if (isset($validated['status']) && array_key_exists($validated['status'], $statusMap)) {
            $validated['status'] = $statusMap[$validated['status']];
        }

        $applicant->update($validated);

        return back()->with('success', 'Applicant stage updated.');
    }

    /**
     * Create a system User account for the applicant (Promote to Trainee).
     */
    public function createUser(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $request->validate([
            'role' => 'required|string',
            'position' => 'required|string',
        ]);

        return DB::transaction(function () use ($applicant, $request) {
            // Create the official User account
            $year = now()->year;
            User::create([
                'name' => $applicant->first_name.' '.$applicant->last_name,
                'email' => $applicant->email,
                'password' => Hash::make('password'), // Set a default or generate one
                'role' => $request->role,
                'position' => $request->position, // Usually 'trainee'
                'department' => $request->role, // Assuming role matches department
                'join_date' => now(),
                'is_active' => true,    
                'employee_id' => 'MONTI-'.$year.'-'.$request->role.'-'.str_pad(User::where('role', $request->role)->count() + 1, 4, '0', STR_PAD_LEFT),
            ]);

            // Mark applicant as completed so they vanish from the pipeline
            $applicant->update(['status' => 'Account Created']);

            return back()->with('success', 'User account created successfully.');
        });
    }

    /**
     * Schedule an interview for an existing applicant.
     */
    public function scheduleInterview(Request $request, Applicant $applicant)
    {
        $request->validate([
            'scheduled_at' => 'required|date',
            'interview_type' => 'required|string|in:phone,technical,behavioral,onsite,video',
            'duration' => 'nullable|integer|in:15,30,45,60',
            'interviewer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $applicant->update(['status' => 'Interview']);

        Interview::create([
            'applicant_id' => $applicant->id,
            'scheduled_at' => $request->scheduled_at,
            'interview_type' => $request->interview_type,
            'duration' => $request->duration ?? 45,
            'interviewer' => $request->interviewer,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Interview scheduled successfully.');
    }
}
