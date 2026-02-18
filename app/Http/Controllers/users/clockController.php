<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\EmployeeShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClockController extends Controller
{
    public function clock()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        return Inertia::render('Dashboard/USERS/clock', [
            // Get today's log for this specific user
            'today_log' => AttendanceLog::where('user_id', $user->id)
                ->where('date', $today)
                ->first(),

            // Get today's assigned shift (Assigned by HR Staff)
            'assigned_shift' => EmployeeShift::where('user_id', $user->id)
                ->where('effective_date', $today)
                ->first(),

            // Get last 5 logs for history
            'history' => AttendanceLog::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get(),
        ]);
    }

    public function toggle()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $log = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $log) {
            // Clock In: Create new record
            AttendanceLog::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => $now,
                'status' => $this->determineStatus($user->id, $today, $now),
            ]);
        } elseif ($log && ! $log->clock_out) {
            // Clock Out: Update existing record
            $log->update([
                'clock_out' => $now,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Optional helper to determine if the user is late based on assigned shift
     */
    private function determineStatus($userId, $date, $clockInTime)
    {
        $shift = EmployeeShift::where('user_id', $userId)
            ->where('effective_date', $date)
            ->first();

        if (! $shift) {
            return 'On-Time';
        }

        // Example logic: if Morning shift starts at 08:00:00
        $startTime = ($shift->shift_type === 'Morning') ? '08:00:00' : '14:00:00';

        return ($clockInTime > $startTime) ? 'Late' : 'On-Time';
    }
}
