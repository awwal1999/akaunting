<?php

namespace App\Widgets;

use App\Abstracts\Widget;
use App\Models\Banking\Transaction;

class LatestExpenses extends Widget
{
    public function show()
    {
        $transactions = Transaction::with('category')->type('expense')->orderBy('paid_at', 'desc')->isNotTransfer()->take(5)->get();

        return view('widgets.latest_expenses', [
            'config' => (object) $this->config,
            'transactions' => $transactions,
        ]);
    }
}