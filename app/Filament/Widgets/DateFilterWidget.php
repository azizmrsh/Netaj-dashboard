<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class DateFilterWidget extends Widget
{
    protected static string $view = 'filament.widgets.date-filter-widget';
    
    protected static ?int $sort = -1; // يظهر في الأعلى
    
    public ?string $start_date = null;
    public ?string $end_date = null;
    
    public function mount(): void
    {
        // تعيين القيم الافتراضية
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }
    
    public function updateDashboard(): void
    {
        // إرسال إشارة لتحديث الويدجت الرئيسي
        $this->dispatch('dateFilterUpdated', [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
        
        // Also dispatch the updateDateFilter event that DashboardOverviewWidget listens to
        $this->dispatch('updateDateFilter', 
            start_date: $this->start_date,
            end_date: $this->end_date
        );
    }
    
    public function resetFilter(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        $this->updateDashboard();
    }
    
    public function setThisMonth(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
        $this->updateDashboard();
    }
    
    public function setLastMonth(): void
    {
        $this->start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
        $this->updateDashboard();
    }
    
    public function setThisYear(): void
    {
        $this->start_date = now()->startOfYear()->format('Y-m-d');
        $this->end_date = now()->endOfYear()->format('Y-m-d');
        $this->updateDashboard();
    }
}