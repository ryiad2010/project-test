<?php

namespace App\Filament\Widgets;

use App\Models\BillingRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BillingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = BillingRecord::where('status', 'paid')->sum('amount');
        $pendingAmount = BillingRecord::where('status', 'pending')->sum('amount');
        $overdueCount = BillingRecord::where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pending Payments', '$' . number_format($pendingAmount, 2))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Overdue Invoices', $overdueCount)
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
