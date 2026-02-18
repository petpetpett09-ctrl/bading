<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SourcingController extends Controller
{
    public function sourcing()
    {
        return Inertia::render('Dashboard/SCM/Manager/sourcing');
    }
}
