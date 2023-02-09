<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\PaymentType;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Expenses/Index', [
            'items' => Expense::with(['Currency', 'PaymentType'])->orderByDesc('transaction_date')->paginate(),
            'currencies' => Currency::all(),
            'paymentTypes' => PaymentType::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpenseRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create(
            $request->validate([
                'name' => ['required'],
                'amount' => ['required'],
                'transaction_date' => ['required'],
                'currency_id' => ['required'],
                'payment_type_id' => ['required'],
            ])
        );

        return redirect()->back()->with('message', "$expense->name added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Inertia\Response
     */
    public function show(Expense $expense, string $id)
    {
        $expense = Expense::find($id);

        if (request()->wantsJson()) {
            return $expense;
        }

        return Inertia::render('Expenses/Show', [
            'item' => $expense
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpenseRequest  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateExpenseRequest $request, string $id)
    {
        $expense = Expense::find($id);
        
        $expense->update($request->all());

        return redirect()->back()->with('message', "$expense->name updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Expense $expense, string $id)
    {
        $expense = Expense::find($id);

        if ($expense->delete()) {
            return redirect()->back()->with('message', "$expense->name deleted successfully");
        }
        
        return redirect()->back()->with('message', "$expense->name delete failed due an error.");
    }
}
