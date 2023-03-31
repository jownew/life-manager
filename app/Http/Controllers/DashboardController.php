<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Index', [
            'expenses' => Expense::with(['Categories', 'Currency', 'PaymentType'])
                ->where('transaction_date', '>=', date('Y-m-01'))
                ->where('transaction_date', '<', date('Y-m-t +1 day'))
                ->orderByDesc('transaction_date')->get(),
        ]);
    }
}
