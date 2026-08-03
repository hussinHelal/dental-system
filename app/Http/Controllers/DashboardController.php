<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Doctor;
use App\Models\InventoryItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $todaysAppointments = \App\Models\Appointment::forDate($today)
            ->whereNotIn('status', ['cancelled'])
            ->with(['patient', 'doctor', 'room'])
            ->orderBy('start_time')
            ->get();

        $lowStockItems = InventoryItem::lowStock()->get();
        $activeDoctorsCount = Doctor::active()->count();

        $financials = null;
        if ($request->user()->isDoctor()) {
            $todaysRevenue = Payment::whereDate('payment_date', $today)->sum('amount_paid');
            $pendingPayments = Payment::whereIn('status', ['pending', 'overdue'])->sum('remaining_balance');
            $installmentTotals = Payment::where('status', 'installment')->sum('remaining_balance');

            $financials = [
                'todays_revenue' => $todaysRevenue,
                'pending_payments' => $pendingPayments,
                'installment_totals' => $installmentTotals,
            ];
        }

        $lastBackup = Backup::latest('generated_at')->first();

        // Small weekly revenue sparkline (last 7 days), Doctor dashboard only.
        $weeklyRevenue = [];
        if ($request->user()->isDoctor()) {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $weeklyRevenue[] = [
                    'label' => $date->format('D'),
                    'amount' => (float) Payment::whereDate('payment_date', $date)->sum('amount_paid'),
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
