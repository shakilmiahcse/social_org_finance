<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Donor;
use App\Models\Fund;

class DashboardController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['donor', 'fund', 'createdBy'])
            ->latest()
            ->take(5)
            ->get();

        $financialSummary = [
            'balance' => Transaction::where('type', 'credit')->sum('amount') -
                        Transaction::where('type', 'debit')->sum('amount'),
            'total_credit' => Transaction::where('type', 'credit')->sum('amount'),
            'total_debit' => Transaction::where('type', 'debit')->sum('amount'),
            'monthly_credit' => Transaction::where('type', 'credit')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'monthly_debit' => Transaction::where('type', 'debit')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        $fundAllocation = Fund::withSum('transactions', 'amount')
            ->having('transactions_sum_amount', '>', 0)
            ->orderByDesc('transactions_sum_amount')
            ->get();

        $topDonors = Donor::withSum('transactions', 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->take(5)
            ->get();

        $transactionTrends = Transaction::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credit,
                SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debit
            ')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return Inertia::render('Dashboard', [
            'recentTransactions' => $transactions,
            'financialSummary' => $financialSummary,
            'fundAllocation' => $fundAllocation,
            'topDonors' => $topDonors,
            'transactionTrends' => $transactionTrends,
            'alerts' => $this->getAlerts($financialSummary['balance'])
        ]);
    }

    private function getAlerts($balance)
    {
        $alerts = [];

        if ($balance < 5000) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Low balance alert: Current balance is below ৳5,000'
            ];
        }

        $pendingCount = Transaction::where('status', 'pending')->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "You have {$pendingCount} pending transactions"
            ];
        }

        return $alerts;
    }
}
