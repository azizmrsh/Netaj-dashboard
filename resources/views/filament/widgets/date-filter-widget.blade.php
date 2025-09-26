<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="width: calc(50% - 0.5rem); display: inline-block; vertical-align: top; margin-right: 1rem;">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
                <x-heroicon-m-calendar-days class="h-5 w-5 text-gray-500" />
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Range Filter</h3>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input 
                    type="date" 
                    id="start_date" 
                    wire:model.live="start_date"
                    wire:change="updateDashboard"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input 
                    type="date" 
                    id="end_date" 
                    wire:model.live="end_date"
                    wire:change="updateDashboard"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2 mt-4">
            <button 
                wire:click="setThisMonth"
                class="px-3 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100 dark:text-primary-400 dark:bg-primary-900/20 dark:hover:bg-primary-900/30"
            >
                This Month
            </button>
            <button 
                wire:click="setLastMonth"
                class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
                Last Month
            </button>
            <button 
                wire:click="setThisYear"
                class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
                This Year
            </button>
        </div>
        
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            Selected period: {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    // Listen for date filter updates and refresh dashboard widgets
    Livewire.on('dateFilterUpdated', (data) => {
        // Dispatch event to update dashboard widgets
        Livewire.dispatch('updateDateFilter', {
            start_date: data.start_date,
            end_date: data.end_date
        });
    });
});
</script>