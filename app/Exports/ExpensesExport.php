<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Expense::with(['currency', 'category', 'paymentType'])->orderBy('paid_date')->get();
    }

    public function headings() : array
    {
        return [
            'Date',
            'Name',
            'Currency',
            'Amount',
            'Category',
            'Payment',
            'Description',
        ];
    }
    public function map($expense) : array
    {
        return [
            $expense->paid_date ? date('d-M-Y', strtotime($expense->paid_date)) : '-',
            $expense->name,
            $expense->currency->code,
            $expense->amount,
            $expense->category?->name,
            $expense->paymentType?->name,
            $expense->description,
        ];
    }
}
