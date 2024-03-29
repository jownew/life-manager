<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Expense;
use Inertia\Inertia;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FRequest;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Expenses/Index', [
            'filters' => FRequest::all(['search', 'trashed']),
            'items' => Expense::with(['category', 'Currency', 'PaymentType'])
                ->where('user_id', $request->user()->id)
                ->orderByDesc('transaction_date')
                ->filter(FRequest::only(['search', 'trashed']))
                ->paginate()
                ->withQueryString(),
            'currencies' => Currency::all(),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpenseRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreExpenseRequest $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'description' => [],
            'amount' => ['required'],
            'transaction_date' => ['required'],
            'currency_id' => ['required'],
            'category_id' => ['required'],
            'payment_type_id' => ['required'],
            'payment_to_id' => ['different:payment_type_id'],
        ],
        [
            'name.required' => 'Please enter a valid item name.',
            'amount.required' => 'Please enter a valid amount.',
            'category_id.required' => 'Please select a category.',
            'payment_type_id.required' => 'Please select a payment source.',
            'payment_to_id.different' => 'Select a different source or destination.',
        ]);

        $validated['user_id'] = $request->user()->id;

        $expense = Expense::create($validated);

        return redirect()->back()->with('message', "$expense->name added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Inertia\Response|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|array<\Illuminate\Database\Eloquent\Builder>|null returnedPHP(PHP0408)
     */
    public function show(Expense $expense, string $id)
    {
        $expense = Expense::with('category')->findOrFail($id);

        if (request()->wantsJson()) {
            return $expense;
        }

        return Inertia::render('Expenses/Show', [
            'item' => $expense
        ]);
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
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required'],
            'description' => [],
            'amount' => ['required'],
            'transaction_date' => ['required'],
            'currency_id' => ['required'],
            'category_id' => ['required'],
            'payment_type_id' => ['required'],
            'payment_type_id.required' => 'Please select a payment source.',
            'payment_to_id' => ['different:payment_type_id'],
        ],
        [
            'name.required' => 'Please enter a valid item name.',
            'amount.required' => 'Please enter a valid amount.',
            'category_id.required' => 'Please select a category.',
            'payment_to_id.different' => 'Select a different source or destination.',
        ]);


        $validated['user_id'] = $request->user()->id;

        $expense->update($validated);

        Category::addCategory($expense->id, $request->category);

        return redirect()->back()->with('message', "$expense->name updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->delete()) {
            return redirect()->back()->with('message', "$expense->name deleted successfully");
        }

        return redirect()->back()->with('message', "$expense->name delete failed due an error.");
    }

    /**
     * Remove the selected resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMany(Request $request)
    {
        if (Expense::whereIn('id', $request->items)->delete()) {
            return redirect()->back()->with('message', "Selected items deleted successfully");
        }

        return redirect()->back()->with('message', "There was an error in deleting the selected items");
    }

    /**
     * Export the expenses
     */
    public function export()
    {
        return Excel::download(new ExpensesExport, 'expenses.xlsx');
    }
}
