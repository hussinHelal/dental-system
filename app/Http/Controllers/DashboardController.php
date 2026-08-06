<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Doctor;
use App\Models\InventoryItem;
use App\Models\Payment;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $todaysAppointments = Appointment::forDate($today)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_NO_SHOW])
            ->with(['patient', 'doctor', 'room'])
            ->orderBy('start_time')
            ->limit(50)
            ->get();

        $lowStockItems      = InventoryItem::lowStock()->get();
        $activeDoctorsCount = Doctor::active()->count();

        $financials = null;
        if ($request->user()->isDoctor()) {
            $todaysRevenue   = Payment::whereDate('payment_date', $today)->sum('amount_paid');
            $pendingPayments = Payment::whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_OVERDUE])->sum('remaining_balance');
            $installmentTotals = Payment::where('status', Payment::STATUS_INSTALLMENT)->sum('remaining_balance');

            $financials = [
                'todays_revenue'    => $todaysRevenue,
                'pending_payments'  => $pendingPayments,
                'installment_totals'=> $installmentTotals,
            ];
        }

        /* BUG FIX: eager-load generator to prevent N+1 */
        $lastBackup = Backup::with('generator')->latest('generated_at')->first();

        /* OPTIMIZATION: single GROUP BY query instead of 7 separate sum() queries */
        $weeklyRevenue = [];
        if ($request->user()->isDoctor()) {
            $start = Carbon::today()->subDays(6)->startOfDay();
            $end   = Carbon::today()->endOfDay();

            $rows = Payment::whereBetween('payment_date', [$start, $end])
                ->selectRaw('DATE(payment_date) as date, SUM(amount_paid) as amount')
                ->groupByRaw('DATE(payment_date)')
                ->pluck('amount', 'date');

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $weeklyRevenue[] = [
                    'label'  => $date->format('D'),
                    'amount' => (float) ($rows[$date->toDateString()] ?? 0),
                ];
            }
        }

        return view('dashboard.index', compact(
            'todaysAppointments',
            'lowStockItems',
            'activeDoctorsCount',
            'financials',
            'lastBackup',
            'weeklyRevenue',
        ));
    }
}