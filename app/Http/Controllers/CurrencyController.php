<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Currencies/Index', [
            'items' => Currency::all()
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
     * @param  \App\Http\Requests\StoreCurrencyRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCurrencyRequest $request)
    {
        $currency = Currency::create(
            $request->validate([
                'name' => ['required'],
                'code' => ['required'],
                'symbol' => '',
            ])
        );

        return redirect()->back()->with('message', "$currency->name added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Inertia\Response
     */
    public function show(string $id)
    {
        $currency = Currency::findOrFail($id);

        if (request()->wantsJson()) {
            return $currency;
        }

        return Inertia::render('Currencies/Show', [
            'item' => $currency
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCurrencyRequest  $request
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCurrencyRequest $request, string $id)
    {
        $currency = Currency::findOrFail($id);
        
        $currency->update($request->all());

        return redirect()->back()->with('message', "$currency->name updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $currency = Currency::findOrFail($id);

        if ($currency->expenses()->count() > 0) {
            return redirect()->back()->with('message', "$currency->name delete failed due associated exepnses.");
        }

        if ($currency->delete()) {
            return redirect()->back()->with('message', "$currency->name deleted successfully");
        }
        
        return redirect()->back()->with('message', "$currency->name delete failed due an error.");
    }
}
