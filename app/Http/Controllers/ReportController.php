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
        return $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'months' => 'nullable|integer|min:1|max:24',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);
    }

    private function getFinancialSummaryData(array $params)
    {
        $query = $this->baseTransactionQuery($params);

        return [
            'balance' => (clone $query)->where('type', 'credit')->sum('amount') -
                        (clone $query)->where('type', 'debit')->sum('amount'),
            'total_credit' => (clone $query)->where('type', 'credit')->sum('amount'),
            'total_debit' => (clone $query)->where('type', 'debit')->sum('amount'),
        ];
    }

    private function getFundAllocationData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        return Fund::where('organization_id', $organization_id)->withSum(['transactions' => function($q) use ($params) {
            $this->applyDateFilters($q, $params);
        }], 'amount')
            ->having('transactions_sum_amount', '>', 0)
            ->orderByDesc('transactions_sum_amount')
            ->get()
            ->map(function ($fund) {
                return [
                    'id' => $fund->id,
                    'name' => $fund->name,
                    'amount' => $fund->transactions_sum_amount,
                ];
            });
    }

    private function getTopDonorsData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        return Donor::where('organization_id', $organization_id)->withSum(['transactions' => function($q) use ($params) {
            $this->applyDateFilters($q, $params);
        }], 'amount')
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
                ];
            });
    }

    private function getTransactionTrendsData(array $params)
    {
        $organization_id = request()->session()->get("organization_id");
        $months = $params['months'] ?? 6;

        return Transaction::where('organization_id', $organization_id)->where('status', 'completed')->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credit,
                SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debit
            ')
            ->when(empty($params['start_date']), function($q) use ($months) {
                $q->where('created_at', '>=', now()->subMonths($months));
            })
            ->when(!empty($params['start_date']), function($q) use ($params) {
                $this->applyDateFilters($q, $params);
            })
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function baseTransactionQuery(array $params)
    {
        $organization_id = request()->session()->get("organization_id");

        $query = Transaction::where('organization_id', $organization_id)->where('status', 'completed');
        $this->applyDateFilters($query, $params);
        return $query;
    }

    private function applyDateFilters($query, array $params)
    {
        if (!empty($params['start_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($params['start_date']));
        }

        if (!empty($params['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($params['end_date']));
        }
    }

    public function export(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Implement your export logic here
        // Return proper download response
    }
}
