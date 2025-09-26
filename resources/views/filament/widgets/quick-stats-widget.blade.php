<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="width: calc(50% - 0.5rem); display: inline-block; vertical-align: top;">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
                <x-heroicon-m-chart-bar class="h-5 w-5 text-gray-500" />
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Quick Statistics</h3>
            </div>
        </div>
        
        @php
            $stats = $this->getStats();
        @endphp
        
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $stats['users_count'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Users</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                    {{ $stats['products_count'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Products</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                    {{ $stats['customers_count'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Customers</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                    {{ $stats['suppliers_count'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Suppliers</div>
            </div>
        </div>
        
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
            System Overview - Total Counts
        </div>
    </div>
</div>