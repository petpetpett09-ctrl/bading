<?php

namespace App\Http\Controllers\hrm\employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeShift;
// You will need to create these Models
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function attendance()
    {
        $today = Carbon::today()->toDateString();

        return Inertia::render('Dashboard/HRM/Employee/attendance', [
            // Fetch real attendance for the main table
            'employee_attendance' => User::with(['latestAttendance' => function ($query) use ($today) {
                $query->where('date', $today);
            }, 'currentShift' => function ($query) use ($today) {
                $query->where('effective_date', $today);
            }])->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    // Change this in attendanceController.php:
                    'dept' => $user->role ?? 'N/A', // Corrected from department_code // From your user table
                    'shift' => $user->currentShift->shift_type ?? 'Morning',
                    'schedule' => $user->currentShift->schedule_range ?? '8AM - 5PM',
                    'clock_in' => $user->latestAttendance->clock_in ?? '---',
                    'status' => $user->latestAttendance->status ?? 'Absent',
                ];
            }),

            'attendance_status' => [
                'is_clocked_in' => false, // Logic for the logged-in user
                'last_action' => '08:45 AM',
                'total_hours_today' => '0h 0m',
            ],
        ]);
    }

    public function toggle()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $timeString = $now->toTimeString();

        // 1. Find or create the log for today
        $log = AttendanceLog::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today]
        );

        // 2. Handle Clock In
        if (! $log->clock_in) {
            // Optional: Check against assigned shift to set status
            $shift = EmployeeShift::where('user_id', $user->id)
                ->where('effective_date', $today)
                ->first();

            $status = 'On-Time';
            if ($shift) {
                // Example: If shift starts at 08:00:00 and now is 08:05:00
                $startTime = Carbon::createFromFormat('H:i:s', '08:00:00'); // Replace with actual shift start time
                if ($now->gt($startTime)) {
                    $status = 'Late';
                }
            }

            $log->update([
                'clock_in' => $timeString,
                'status' => $status,
            ]);

            return back()->with('success', "Clocked in at $timeString. Status: $status");
        }

        // 3. Handle Clock Out
        if (! $log->clock_out) {
            $log->update(['clock_out' => $timeString]);

            return back()->with('success', "Clocked out at $timeString. Shift completed.");
        }

        // 4. Already completed for the day
        return back()->with('error', 'You have already completed your shift logs for today.');
    }

    // Function to handle the "Save Schedule Changes" button in your modal
    public function updateShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_type' => 'required|in:Morning,Afternoon,Graveyard',
            'effective_date' => 'required|date',
        ]);

        EmployeeShift::updateOrCreate(
            ['user_id' => $request->user_id, 'effective_date' => $request->effective_date],
            ['shift_type' => $request->shift_type, 'dept_code' => $request->dept_code]
        );

        return back()->with('success', 'Shift updated successfully.');
    }
}
