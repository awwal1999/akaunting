<?php

namespace App\Widgets;

use App\Abstracts\Widget;
use App\Models\Banking\Transaction;
use App\Models\Expense\Bill;
use App\Models\Income\Invoice;

class TotalProfit extends Widget
{
    public function show()
    {
        $current_income = $open_invoice = $overdue_invoice = 0;
        $current_expenses = $open_bill = $overdue_bill = 0;

        Transaction::isNotTransfer()->each(function ($transaction) use (&$current_income, &$current_expenses) {
            $amount = $transaction->getAmountConvertedToDefault();

            if ($transaction->type == 'income') {
                $current_income += $amount;
            } else {
                $current_expenses += $amount;
            }
        });

        Invoice::accrued()->notPaid()->each(function ($invoice) use (&$open_invoice, &$overdue_invoice) {
            list($open_tmp, $overdue_tmp) = $this->calculateDocumentTotals($invoice);

            $open_invoice += $open_tmp;
            $overdue_invoice += $overdue_tmp;
        });

        Bill::accrued()->notPaid()->each(function ($bill) use (&$open_bill, &$overdue_bill) {
            list($open_tmp, $overdue_tmp) = $this->calculateDocumentTotals($bill);

            $open_bill += $open_tmp;
            $overdue_bill += $overdue_tmp;
        });

        $current = $current_income - $current_expenses;
        $open = $open_invoice - $open_bill;
        $overdue = $overdue_invoice - $overdue_bill;

        $progress = 100;

        if (!empty($open) && !empty($overdue)) {
            $progress = (int) ($open * 100) / ($open + $overdue);
        }

        $totals = [
            'current'       => $current,
            'open'          => money($open, setting('default.currency'), true),
            'overdue'       => money($overdue, setting('default.currency'), true),
            'progress'      => $progress,
        ];

        return view('widgets.total_profit', [
            'config' => (object) $this->config,
            'totals' => $totals,
        ]);
    }
}
