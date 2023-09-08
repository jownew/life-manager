<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Models\PaymentType;
use Inertia\Inertia;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        if (request()->wantsJson()) {
            return $paymentTypes;
        }

        return Inertia::render('PaymentTypes/Index', [
            'items' => $paymentTypes,
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
     * @param  \App\Http\Requests\StorePaymentTypeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePaymentTypeRequest $request)
    {
        $paymentType = PaymentType::create(
            $request->validate([
                'name' => ['required'],
            ])
        );

        return redirect()->back()->with('message', "$paymentType->name added successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Inertia\Response
     */
    public function show(string $id)
    {
        $expaymentType = PaymentType::find($id);

        if (request()->wantsJson()) {
            return $expaymentType;
        }

        return Inertia::render('Expenses/Show', [
            'item' => $expaymentType
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentType $paymentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaymentTypeRequest  $request
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePaymentTypeRequest $request, string $id)
    {
        $paymentType = PaymentType::find($id);

        $paymentType->update($request->all());

        return redirect()->back()->with('message', "$paymentType->name updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $paymentType = PaymentType::findOrFail($id);

        if ($paymentType->expenses()->count() > 0) {
            return redirect()->back()->with('message', "$paymentType->name delete failed due associated exepnses.");
        }

        if ($paymentType->delete()) {
            return redirect()->back()->with('message', "$paymentType->name deleted successfully");
        }

        return redirect()->back()->with('message', "$paymentType->name delete failed due an error.");
    }
}
