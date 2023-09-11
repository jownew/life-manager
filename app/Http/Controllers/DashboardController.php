<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index(Request $request, $date = null, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);

        if ($date == null || $d && $d->format($format) != $date) {
            $date = date('Y-m-01');
        }

        return Inertia::render('Dashboard/Index', [
            'expenses' => Expense::with(['category', 'Currency', 'PaymentType'])
                ->where('user_id', $request->user()->id)
                ->where('transaction_date', '>=', date('Y-m-01', strtotime($date)))
                ->where('transaction_date', '<', date('Y-m-t', strtotime($date . ' +1 day')))
                ->orderByDesc('transaction_date')->get(),
            'categories' => Category::all(),
            'date' => $date,
            'previousMonth' => date('Y-m-01', strtotime($date . ' -1 month')),
            'nextMonth' => date('Y-m-01', strtotime($date . ' +1 month')),
        ]);
    }
}
