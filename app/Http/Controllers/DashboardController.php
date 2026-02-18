<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtoupper($user->role);
        $position = strtolower($user->position);

        // For debugging
        \Log::info('Dashboard access', [
            'user_id' => $user->id,
            'role' => $role,
            'position' => $position,
            'route' => $request->fullUrl(),
        ]);

        /**
         * 1. Priority: Trainee Check
         */
        if ($position === 'trainee') {
            return Inertia::render('Dashboard/TRAINEE/index', [
                'user' => $user,
                'stats' => [
                    'progress' => 45, // Example static progress
                    'assigned_modules' => 5,
                    'days_remaining' => 12,
                ],
            ]);
        }

        /**
         * 2. Department Check
         */
        return match ($role) {
            'HRM' => $this->handleHrmDashboard($position),
            'SCM' => $this->handleScmDashboard($position),
            'FIN' => $this->handleFinDashboard($position),
            'MAN' => $this->handleManDashboard($position),
            'INV' => $this->handleInvDashboard($position),
            'ORD' => $this->handleOrdDashboard($position),
            'WAR' => $this->handleWarDashboard($position),
            'CRM' => $this->handleCrmDashboard($position),
            'ECO' => $this->handleEcoDashboard($position),
            default => $this->renderDefaultDashboard($user),
        };
    }

    private function handleHrmDashboard(string $position)
    {
        if ($position === 'manager') {
            // Fetch trainees suggested by HRM Staff for promotion
            $suggestedTrainees = User::where('promotion_suggested', true)
                ->orderBy('suggested_at', 'desc')
                ->get();

            return Inertia::render('Dashboard/HRM/Manager/Index', [
                'suggestedTrainees' => $suggestedTrainees,
                'stats' => [
                    'totalEmployees' => User::count(),
                    'activeRecruitment' => 12,
                    'pendingLeaves' => 8,
                    'attendanceRate' => 95,
                ],
            ]);
        }

        // HRM Staff / Employee View: Displays all users from the table
        return Inertia::render('Dashboard/HRM/Employee/Index', [
            'employees' => User::all(), // Fetches all data from users table
            'stats' => [
                'total' => User::count(),
                'present' => User::where('is_active', true)->count(),
                'on_leave' => 0, // Placeholder: Integrate with your leave table logic later
                'assignedTasks' => 4,
                'leaveBalance' => 15,
                'trainingModules' => 2,
            ],
            'user' => Auth::user(),
        ]);
    }

    /**
     * Finalize the promotion of a trainee to staff status.
     * This moves them from 'trainee' to 'staff' and clears the suggestion flag.
     */
    public function finalizePromotion($id)
    {
        $trainee = User::findOrFail($id);

        $trainee->update([
            'position' => 'staff',
            'promotion_suggested' => false,
            'suggested_at' => null,
        ]);

        return redirect()->back()->with('message', "{$trainee->name} has been successfully promoted to Staff.");
    }

    private function handleScmDashboard(string $position)
    {
        if ($position === 'manager') {
            return Inertia::render('Dashboard/SCM/Manager/Index', [
                'stats' => [
                    'totalInventory' => 1250,
                    'pendingOrders' => 8,
                    'completedDeliveries' => 24,
                    'stockAlerts' => 3,
                ],
                'inventoryItems' => [],
                'pendingOrders' => [],
            ]);
        }

        return Inertia::render('Dashboard/SCM/Employee/Index', [
            'stats' => [
                'pendingPickups' => 5,
                'incomingShipments' => 3,
                'inventoryChecks' => 2,
            ],
            'user' => Auth::user(),
        ]);
    }

    private function handleFinDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/FIN/Manager/index' : 'Dashboard/FIN/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'stats' => [
                'totalRevenue' => 0,
                'pendingInvoices' => 0,
                'overduePayments' => 0,
            ],
        ]);
    }

    private function handleManDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/MAN/Manager/index' : 'Dashboard/MAN/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'productionLines' => [],
            'stats' => [
                'activeLines' => 0,
                'dailyOutput' => 0,
                'defectRate' => 0,
            ],
        ]);
    }

    private function handleInvDashboard(string $position)
    {
        if ($position === 'manager') {
            return Inertia::render('Dashboard/INV/Manager/index', [
                'user' => Auth::user(),
                'stockLevels' => [
                    ['id' => 1, 'name' => 'Cotton Fabric', 'sku' => 'CF-001', 'quantity' => 150, 'status' => 'In Stock'],
                    ['id' => 2, 'name' => 'Polyester Blend', 'sku' => 'PB-002', 'quantity' => 85, 'status' => 'In Stock'],
                    ['id' => 3, 'name' => 'Silk Material', 'sku' => 'SM-003', 'quantity' => 25, 'status' => 'Low Stock'],
                    ['id' => 4, 'name' => 'Denim', 'sku' => 'DN-004', 'quantity' => 200, 'status' => 'In Stock'],
                    ['id' => 5, 'name' => 'Wool Blend', 'sku' => 'WB-005', 'quantity' => 8, 'status' => 'Critical'],
                ],
                'stats' => [
                    'totalItems' => 468,
                    'lowStock' => 12,
                    'outOfStock' => 3,
                    'warehouseCapacity' => 85,
                ],
            ]);
        }

        return Inertia::render('Dashboard/INV/Employee/index', [
            'user' => Auth::user(),
            'stockLevels' => [
                ['id' => 1, 'name' => 'Cotton Fabric', 'sku' => 'CF-001', 'quantity' => 150, 'status' => 'In Stock'],
                ['id' => 2, 'name' => 'Polyester Blend', 'sku' => 'PB-002', 'quantity' => 85, 'status' => 'In Stock'],
                ['id' => 3, 'name' => 'Silk Material', 'sku' => 'SM-003', 'quantity' => 25, 'status' => 'Low Stock'],
                ['id' => 4, 'name' => 'Denim', 'sku' => 'DN-004', 'quantity' => 200, 'status' => 'In Stock'],
                ['id' => 5, 'name' => 'Wool Blend', 'sku' => 'WB-005', 'quantity' => 8, 'status' => 'Critical'],
                ['id' => 6, 'name' => 'Linen', 'sku' => 'LN-006', 'quantity' => 45, 'status' => 'In Stock'],
                ['id' => 7, 'name' => 'Satin', 'sku' => 'ST-007', 'quantity' => 60, 'status' => 'In Stock'],
                ['id' => 8, 'name' => 'Velvet', 'sku' => 'VL-008', 'quantity' => 15, 'status' => 'Low Stock'],
            ],
            'stats' => [
                'totalItems' => 468,
                'lowStock' => 12,
                'outOfStock' => 3,
                'warehouseCapacity' => 85,
            ],
        ]);
    }

    private function handleOrdDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/ORD/Manager/index' : 'Dashboard/ORD/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'recentOrders' => [],
            'stats' => [
                'pendingOrders' => 0,
                'completedToday' => 0,
                'totalRevenue' => 0,
            ],
        ]);
    }

    private function handleWarDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/WAR/Manager/index' : 'Dashboard/WAR/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'bins' => [],
            'stats' => [
                'totalBins' => 0,
                'occupiedBins' => 0,
                'availableBins' => 0,
            ],
        ]);
    }

    private function handleCrmDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/CRM/Manager/index' : 'Dashboard/CRM/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'customers' => [],
            'stats' => [
                'totalCustomers' => 0,
                'newThisMonth' => 0,
                'satisfactionRate' => 0,
            ],
        ]);
    }

    private function handleEcoDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/ECO/Manager/index' : 'Dashboard/ECO/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'onlineSales' => [],
            'stats' => [
                'todaySales' => 0,
                'monthlyRevenue' => 0,
                'activeProducts' => 0,
            ],
        ]);
    }

    private function renderDefaultDashboard($user)
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_tasks' => 0,
                'pending_tasks' => 0,
                'completed_tasks' => 0,
            ],
            'user' => $user,
        ]);
    }
}
