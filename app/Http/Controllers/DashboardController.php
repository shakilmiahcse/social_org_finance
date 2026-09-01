<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Donor;
use App\Models\Fund;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $organization_id = $request->user()?->organization_id ?? $request->session()->get("organization_id");

        // Recent transactions with eager loading
        $transactions = Transaction::where('organization_id', $organization_id)->where('status', 'completed')
            ->with(['donor', 'fund', 'createdBy'])
            ->latest()
            ->take(5)
            ->get();

        // Base query to clone from
        $baseQuery = Transaction::where('organization_id', $organization_id)->where('status', 'completed');

        // Clone base query for summaries
        $creditQuery = (clone $baseQuery)->where('type', 'credit');
        $debitQuery = (clone $baseQuery)->where('type', 'debit');

        $financialSummary = [
            'balance' => $creditQuery->sum('amount') - $debitQuery->sum('amount'),
            'total_credit' => (clone $creditQuery)->sum('amount'),
            'total_debit' => (clone $debitQuery)->sum('amount'),
            'monthly_credit' => (clone $creditQuery)->whereMonth('created_at', now()->month)->sum('amount'),
            'monthly_debit' => (clone $debitQuery)->whereMonth('created_at', now()->month)->sum('amount'),
        ];

        // Fund allocation summary
        $fundAllocation = Fund::where('organization_id', $organization_id)
            ->whereHas('transactions')
            ->withSum('transactions', 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->get();

        // Top 5 donors
        $topDonors = Donor::where('organization_id', $organization_id)
            ->withSum('transactions', 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->take(5)
            ->get();

        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $yearSql = $driver === 'sqlite' ? "cast(strftime('%Y', created_at) as integer)" : "YEAR(created_at)";
        $monthSql = $driver === 'sqlite' ? "cast(strftime('%m', created_at) as integer)" : "MONTH(created_at)";

        // Transaction trends for the past 6 months
        $transactionTrends = (clone $baseQuery)
            ->selectRaw("
                {$yearSql} as year,
                {$monthSql} as month,
                SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as debit
            ")
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
            'alerts' => $this->getAlerts($financialSummary['balance'], $organization_id),
            'permissions' => [
                'viewTransactions' => auth()->user()->can('transactions.view'),
                'createTransactions' => auth()->user()->can('transactions.create'),
                'viewDonors' => auth()->user()->can('donors.view'),
                'viewFunds' => auth()->user()->can('funds.view'),
                'viewDashboard' => auth()->user()->can('dashboard.view'),
            ],
        ]);
    }


    private function getAlerts($balance, $organization_id = null)
    {
        $organization_id = $organization_id ?? (request()->user()?->organization_id ?? request()->session()->get("organization_id"));
        $alerts = [];

        if ($balance < 5000) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Low balance alert: Current balance is below ৳5,000'
            ];
        }

        $pendingCount = Transaction::where('organization_id', $organization_id)->where('status', 'pending')->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "You have {$pendingCount} pending transactions"
            ];
        }

        return $alerts;
    }
}
