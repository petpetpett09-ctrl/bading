<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function audit()
    {
        return Inertia::render('Dashboard/SCM/Manager/audit');
    }
}
