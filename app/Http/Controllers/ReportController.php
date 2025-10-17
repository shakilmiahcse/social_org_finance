<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Donor;
use App\Models\Fund;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('reports.view')) {
            abort(403, 'You do not have permission to view reports.');
        }
        $validated = $this->validateRequest($request);

        return Inertia::render('Reports/Index', [
            'initialData' => [
                'financialSummary' => $this->getFinancialSummaryData($validated),
                'fundAllocation' => $this->getFundAllocationData($validated),
                'topDonors' => $this->getTopDonorsData($validated),
                'transactionTrends' => $this->getTransactionTrendsData($validated),
                'monthlyComparison' => $this->getMonthlyComparisonData($validated),
                'donationDistribution' => $this->getDonationDistributionData($validated),
            ],
            'filters' => $validated,
            'permissions' => [
                'view' => auth()->user()->can('reports.view'),
                'export' => auth()->user()->can('reports.export'),
                'generate' => auth()->user()->can('reports.generate'),
            ],
        ]);
    }

    private function validateRequest(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'months' => 'nullable|integer|min:1|max:24',
            'limit' => 'nullable|integer|min:1|max:100',
            'chart_type' => 'nullable|in:line,bar,pie',
        ]);

        // Set default date range (last 7 days) if no dates provided
        if (empty($validated['start_date'])) {
            $validated['start_date'] = now()->subDays(7)->format('Y-m-d');
        }
        if (empty($validated['end_date'])) {
            $validated['end_date'] = now()->format('Y-m-d');
        }

        return $validated;
    }

    private function getFinancialSummaryData(array $params)
    {
        $query = $this->baseTransactionQuery($params);

        $totalCredit = (clone $query)->where('type', 'credit')->sum('amount');
        $totalDebit = (clone $query)->where('type', 'debit')->sum('amount');
        $balance = $totalCredit - $totalDebit;

        return [
            'balance' => $balance,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'transaction_count' => $query->count(),
            'avg_transaction' => $query->count() > 0 ? ($totalCredit + $totalDebit) / $query->count() : 0,
            'growth_rate' => $this->calculateGrowthRate($params),
        ];
    }

    private function getFundAllocationData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        $funds = Fund::where('organization_id', $organization_id)
            ->withSum(['transactions' => function($q) use ($params) {
                $this->applyDateFilters($q, $params);
            }], 'amount')
            ->having('transactions_sum_amount', '>', 0)
            ->orderByDesc('transactions_sum_amount')
            ->get();

        $totalAmount = $funds->sum('transactions_sum_amount');

        return $funds->map(function ($fund) use ($totalAmount) {
            return [
                'id' => $fund->id,
                'name' => $fund->name,
                'amount' => $fund->transactions_sum_amount,
                'percentage' => $totalAmount > 0 ? round(($fund->transactions_sum_amount / $totalAmount) * 100, 2) : 0,
                'color' => $this->generateColor($fund->id),
            ];
        });
    }

    private function getTopDonorsData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        return Donor::where('organization_id', $organization_id)
            ->withSum(['transactions' => function($q) use ($params) {
                $this->applyDateFilters($q, $params);
            }], 'amount')
            ->withCount(['transactions' => function($q) use ($params) {
                $this->applyDateFilters($q, $params);
            }])
            ->orderByDesc('transactions_sum_amount')
            ->take($params['limit'] ?? 5)
            ->get()
            ->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'name' => $donor->name,
                    'email' => $donor->email,
                    'phone' => $donor->phone,
                    'amount' => $donor->transactions_sum_amount,
                    'transaction_count' => $donor->transactions_count,
                    'avg_donation' => $donor->transactions_count > 0 ? $donor->transactions_sum_amount / $donor->transactions_count : 0,
                ];
            });
    }

    private function getTransactionTrendsData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        $months = $params['months'] ?? 6;

        $startDate = Carbon::parse($params['start_date'])->subMonths($months);
        $endDate = Carbon::parse($params['end_date']);

        return Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as transaction_count,
                SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credit,
                SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debit,
                AVG(CASE WHEN type = "credit" THEN amount ELSE NULL END) as avg_credit,
                AVG(CASE WHEN type = "debit" THEN amount ELSE NULL END) as avg_debit
            ')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'credit' => (float) $item->credit,
                    'debit' => (float) $item->debit,
                    'transaction_count' => (int) $item->transaction_count,
                    'avg_credit' => (float) $item->avg_credit,
                    'avg_debit' => (float) $item->avg_debit,
                    'net_flow' => (float) $item->credit - (float) $item->debit,
                ];
            });
    }

    private function getMonthlyComparisonData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");

        $currentData = Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('
                SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as current_credit,
                SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as current_debit
            ')
            ->first();

        $previousData = Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->selectRaw('
                SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as previous_credit,
                SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as previous_debit
            ')
            ->first();

        return [
            'current' => [
                'credit' => (float) ($currentData->current_credit ?? 0),
                'debit' => (float) ($currentData->current_debit ?? 0),
            ],
            'previous' => [
                'credit' => (float) ($previousData->previous_credit ?? 0),
                'debit' => (float) ($previousData->previous_debit ?? 0),
            ],
        ];
    }

    private function getDonationDistributionData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");

        return Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->where('type', 'credit')
            ->selectRaw('
                CASE
                    WHEN amount < 1000 THEN "Small (< ৳1k)"
                    WHEN amount BETWEEN 1000 AND 5000 THEN "Medium (৳1k-5k)"
                    WHEN amount BETWEEN 5001 AND 10000 THEN "Large (৳5k-10k)"
                    ELSE "Major (৳10k+)"
                END as `range`,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->whereDate('created_at', '>=', $params['start_date'])
            ->whereDate('created_at', '<=', $params['end_date'])
            ->groupBy('range')
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    private function calculateGrowthRate(array $params)
    {
        $organization_id = request()->session()->get("organization_id");

        $currentPeriod = Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->where('type', 'credit')
            ->whereDate('created_at', '>=', $params['start_date'])
            ->whereDate('created_at', '<=', $params['end_date'])
            ->sum('amount');

        $previousStart = Carbon::parse($params['start_date'])->subDays(7);
        $previousEnd = Carbon::parse($params['end_date'])->subDays(7);

        $previousPeriod = Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed')
            ->where('type', 'credit')
            ->whereDate('created_at', '>=', $previousStart)
            ->whereDate('created_at', '<=', $previousEnd)
            ->sum('amount');

        if ($previousPeriod == 0) return 0;

        return (($currentPeriod - $previousPeriod) / $previousPeriod) * 100;
    }

    private function generateColor($seed)
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
        ];
        return $colors[$seed % count($colors)];
    }

    private function baseTransactionQuery(array $params)
    {
        $organization_id = request()->session()->get("organization_id");

        $query = Transaction::where('organization_id', $organization_id)
            ->where('status', 'completed');
        $this->applyDateFilters($query, $params);
        return $query;
    }

    private function applyDateFilters($query, array $params)
    {
        $query->whereDate('created_at', '>=', $params['start_date'])
              ->whereDate('created_at', '<=', $params['end_date']);
    }

    public function export(Request $request)
    {
        $validated = $this->validateRequest($request);
        // Implement your export logic here
    }
}
