<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\DigitalNote;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الطلاب', User::count())
                ->description('عدد المستخدمين المسجلين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart(User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make('إجمالي الكتب', Book::count())
                ->description('عدد الكتب المضافة')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info')
                ->chart(Book::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make('إجمالي الملاحظات الرقمية', DigitalNote::count())
                ->description('عدد الملاحظات المضافة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning')
                ->chart(DigitalNote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make('كتب معلقة', Book::where('moderation_status', 'pending')->count())
                ->description('كتب بانتظار المراجعة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
