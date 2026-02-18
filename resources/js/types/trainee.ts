/**
 * Trainee Module - TypeScript Interfaces and Types
 * Provides type-safe data structures for all trainee-related pages
 */

/**
 * User Interface
 */
export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  position: string;
  employee_id: string;
  department: string;
  join_date: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

/**
 * Dashboard Types
 */
export interface AttendanceRecord {
  date: string;
  clockIn: string | null;
  clockOut: string | null;
  status: 'present' | 'absent' | 'late' | 'on_leave';
}

export interface Holiday {
  id: number;
  date: string;
  name: string;
  type: string;
}

export interface Payroll {
  id: number;
  gross_pay: number;
  net_pay: number;
  status: 'pending' | 'approved' | 'rejected' | 'paid';
}

export interface LeaveBalance {
  total: number;
  used: number;
  remaining: number;
}

export interface AttendanceStatistics {
  present: number;
  late: number;
  absent: number;
  onLeave: number;
}

export interface DashboardProps {
  user: User;
  attendancePercentage: number;
  currentPayroll: Payroll | null;
  upcomingHolidays: Holiday[];
  recentAttendance: AttendanceRecord[];
  leaveBalance: LeaveBalance;
  attendanceStats: AttendanceStatistics;
}

/**
 * Time Keeping Types
 */
export type AttendanceStatus = 'not_clocked_in' | 'clocked_in' | 'clocked_out';

export interface DailyRecord {
  id: number;
  date: string;
  dayOfWeek: string;
  clockIn: string | null;
  clockOut: string | null;
  duration: string | null;
  status: string;
}

export interface TimeKeepingProps {
  user: User;
  todayAttendance: AttendanceRecord | null;
  currentStatus: AttendanceStatus;
  weeklyRecords: DailyRecord[];
}

/**
 * Attendance Page Types
 */
export interface CalendarDay {
  day: number;
  date: string;
  dayOfWeek: string;
  status: string | null;
  statusClass: string;
  hasRecord: boolean;
}

export interface AttendanceStats {
  totalDays: number;
  present: number;
  late: number;
  absent: number;
  onLeave: number;
  attendanceRate: number;
}

export interface DetailedAttendanceRecord {
  id: number;
  date: string;
  clockIn: string | null;
  clockOut: string | null;
  duration: string | null;
  status: string;
  statusBadge: {
    label: string;
    color: string;
  };
}

export interface AttendancePageProps {
  user: User;
  currentMonth: number;
  currentYear: number;
  calendarDays: CalendarDay[];
  statistics: AttendanceStats;
  detailedRecords: DetailedAttendanceRecord[];
}

/**
 * Payslip Types
 */
export interface PayslipListItem {
  id: number;
  period: string;
  grossPay: number;
  netPay: number;
  date: string;
  status: string;
  statusBadge: {
    label: string;
    color: string;
  };
}

export interface PayslipDeductions {
  sss: number;
  philhealth: number;
  pagibig: number;
  taxWithheld: number;
  sssLoan: number;
  pfLoan: number;
  totalDeductions: number;
}

export interface PayslipDetails {
  id: number;
  period: string;
  date: string;
  employeeName: string;
  employeeId: string;
  role: string;
  baseSalary: number;
  daysWorked: number;
  dailyRate: number;
  totalDaysAmt: number;
  nightHours: number;
  nightRate: number;
  nightAmt: number;
  overtimeHours: number;
  otRate: number;
  otAmt: number;
  sundayRestdayHours: number;
  sunSpRate: number;
  sunSpAmt: number;
  holidayAmt: number;
  lateMinutes: number;
  lateRateMin: number;
  lateTotalDeduction: number;
  grossPay: number;
  deductions: PayslipDeductions;
  netPay: number;
  status: string;
}

export interface PayslipPageProps {
  user: User;
  payslips: PayslipListItem[];
  payslipDetails: PayslipDetails | null;
  currentMonth: number;
  currentYear: number;
}

/**
 * Form Validation Types
 */
export interface ClockInOutPayload {
  action: 'in' | 'out';
}

/**
 * Status Badge Types
 */
export interface StatusBadge {
  label: string;
  color: 'success' | 'warning' | 'danger' | 'info' | 'secondary';
}
