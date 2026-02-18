<?php

namespace App\Http\Controllers\eco\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class BookController extends Controller
{
    public function book()
    {
        return Inertia::render('Dashboard/ECO/Manager/book');
    }
}
