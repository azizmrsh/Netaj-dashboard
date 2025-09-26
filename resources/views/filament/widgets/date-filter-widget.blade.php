<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <!-- نموذج فلتر التاريخ -->
            <form wire:submit.prevent="updateDashboard">
                {{ $this->form }}
            </form>
            
            <!-- أزرار سريعة للفترات المحددة مسبقاً -->
            <div class="flex flex-wrap gap-2 mt-4">
                <x-filament::button
                    wire:click="setThisMonth"
                    size="sm"
                    color="primary"
                    outlined
                >
                    This Month
                </x-filament::button>
                
                <x-filament::button
                    wire:click="setLastMonth"
                    size="sm"
                    color="info"
                    outlined
                >
                    Last Month
                </x-filament::button>
                
                <x-filament::button
                    wire:click="setThisYear"
                    size="sm"
                    color="success"
                    outlined
                >
                    This Year
                </x-filament::button>
                
                <x-filament::button
                    wire:click="resetFilter"
                    size="sm"
                    color="gray"
                    outlined
                >
                    Reset
                </x-filament::button>
            </div>
            
            <!-- عرض الفترة المحددة حالياً -->
            @if($start_date && $end_date)
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    <strong>Current Period:</strong> 
                    {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} - 
                    {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>