<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\RoomEquipment;
use App\Models\UtilityRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private const SERVICE_FEE = 150000;

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $billingMonth = $this->normalizeBillingMonth($request->query('billing_month'));

        $equipmentReports = Equipment::where('tenant_id', $tenantId)
            ->withCount([
                'roomAllocations as using_rooms_count' => fn ($query) => $query->where('quantity', '>', 0),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (Equipment $equipment) {
                return [
                    'id' => $equipment->id,
                    'code' => $equipment->code,
                    'name' => $equipment->name,
                    'unit' => $equipment->unit,
                    'total_quantity' => (int) $equipment->total_quantity,
                    'allocated_quantity' => (int) $equipment->allocated_quantity,
                    'stock_quantity' => $equipment->stock_quantity,
                    'using_rooms_count' => (int) $equipment->using_rooms_count,
                ];
            });

        $allocationReports = RoomEquipment::with(['room.building', 'equipment'])
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        $unpaidQuery = UtilityRecord::with(['room.building', 'room.residents' => function ($query) {
                $query->where('status', 'active');
            }])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('status', '!=', 'paid');

        if ($billingMonth) {
            $unpaidQuery->where('billing_month', $billingMonth);
        }

        $unpaidUtilities = $unpaidQuery
            ->orderByDesc('billing_month')
            ->orderBy('room_id')
            ->get()
            ->map(function (UtilityRecord $record) {
                $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
                $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
                $electricityAmount = $electricityUsage * (int) $record->electricity_price;
                $waterAmount = $waterUsage * (int) $record->water_price;
                $roomAmount = (int) optional($record->room)->price;

                $record->electricity_usage = $electricityUsage;
                $record->water_usage = $waterUsage;
                $record->electricity_amount = $electricityAmount;
                $record->water_amount = $waterAmount;
                $record->service_amount = self::SERVICE_FEE;
                $record->total_amount = $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE;

                return $record;
            });

        $summary = [
            'equipment_total' => $equipmentReports->sum('total_quantity'),
            'equipment_allocated' => $equipmentReports->sum('allocated_quantity'),
            'equipment_stock' => $equipmentReports->sum('stock_quantity'),
            'unpaid_room_count' => $unpaidUtilities->pluck('room_id')->unique()->count(),
            'unpaid_total_amount' => $unpaidUtilities->sum('total_amount'),
        ];

        // Truy vấn danh sách thu chi (Transactions) phục vụ Sổ Quỹ & Báo Cáo Lợi Nhuận
        $transactionsQuery = \App\Models\Transaction::where('tenant_id', $tenantId);
        if ($billingMonth) {
            $transactionsQuery->where('transaction_date', 'like', $billingMonth . '%');
        }
        $transactions = $transactionsQuery->orderByDesc('transaction_date')->orderByDesc('id')->get();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $financialSummary = [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
        ];

        return view('admin.reports.index', compact(
            'billingMonth',
            'equipmentReports',
            'allocationReports',
            'unpaidUtilities',
            'summary',
            'transactions',
            'financialSummary'
        ));
    }

    public function storeTransaction(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        \App\Models\Transaction::create(array_merge($validated, [
            'tenant_id' => $tenantId
        ]));

        return redirect()->route('admin.reports.index')->with('success', 'Ghi nhận giao dịch thu/chi thành công!');
    }

    private function normalizeBillingMonth($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            abort(404, 'Tháng báo cáo không hợp lệ.');
        }

        return $value;
    }
}
