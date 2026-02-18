<?php

namespace App\Http\Controllers\eco\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CreditController extends Controller
{
    public function credit()
    {
        return Inertia::render('Dashboard/ECO/Manager/credit');
    }
}
