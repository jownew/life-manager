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
    public function collection(): \Illuminate\Support\Enumerable
    {
        return Expense::with(['currency', 'category', 'paymentType', 'tags'])->orderBy('paid_date')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Name',
            'Currency',
            'Amount',
            'Category',
            'Payment',
            'Tags',
            'Description',
        ];
    }
    public function map($expense): array
    {
        return [
            $expense->paid_date ? date('d-M-Y', strtotime($expense->paid_date)) : '-',
            $expense->name,
            $expense->currency->code,
            $expense->amount,
            $expense->category?->name,
            $expense->paymentType?->name,
            $expense->tags->pluck('name')->join(', '),
            $expense->description,
        ];
    }
}
