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
            Stat::make(__('admin.widget.total_students'), User::count())
                ->description(__('admin.widget.registered_users'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart(User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make(__('admin.widget.total_books'), Book::count())
                ->description(__('admin.widget.books_added_count'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info')
                ->chart(Book::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make(__('admin.widget.total_notes'), DigitalNote::count())
                ->description(__('admin.widget.notes_added_count'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning')
                ->chart(DigitalNote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray()),
            Stat::make(__('admin.widget.pending_books'), Book::where('moderation_status', 'pending')->count())
                ->description(__('admin.widget.pending_review'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
