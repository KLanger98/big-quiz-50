<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PrizeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Prizes/Index');
    }
}
