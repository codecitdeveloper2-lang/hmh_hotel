<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        {{-- Dashboard Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <x-filament::icon icon="heroicon-o-document-text" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Activities</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalActivities }}</h3>
                    </div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <x-filament::icon icon="heroicon-o-calendar-days" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Today's Activities</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $todayActivities }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Successful Actions</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $successfulActions }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <x-filament::icon icon="heroicon-o-x-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Failed Actions</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $failedActions }}</h3>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Filters & Search --}}
        <x-filament::section>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <div style="flex: 1 1 200px;">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model.live="searchQuery"
                            placeholder="Search logs by user, record, or action..."
                        />
                    </x-filament::input.wrapper>
                </div>
                
                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterUser">
                            <option value="">All Users</option>
                            <option value="John Doe">John Doe</option>
                            <option value="Jane Smith">Jane Smith</option>
                            <option value="System">System</option>
                            <option value="Alice Johnson">Alice Johnson</option>
                            <option value="Bob Williams">Bob Williams</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterModule">
                            <option value="">All Modules</option>
                            <option value="Hotel Management">Hotel Management</option>
                            <option value="Gallery">Gallery</option>
                            <option value="Authentication">Authentication</option>
                            <option value="Offers">Offers</option>
                            <option value="Reservations">Reservations</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterAction">
                            <option value="">All Actions</option>
                            <option value="Created">Created</option>
                            <option value="Updated">Updated</option>
                            <option value="Deleted">Deleted</option>
                            <option value="Failed Login">Failed Login</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 250px; display: flex; gap: 0.5rem; align-items: center;">
                    <div style="flex: 1;">
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" wire:model.live="filterDateFrom" />
                        </x-filament::input.wrapper>
                    </div>
                    <span style="font-size: 0.875rem; color: #6b7280; white-space: nowrap;">to</span>
                    <div style="flex: 1;">
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" wire:model.live="filterDateTo" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Data Table --}}
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1000px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Log ID</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">User</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Role</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Module</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Action</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Record Name</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">IP Address</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Date & Time</th>
                            <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Status</th>
                            <th style="padding: 1rem 1.5rem; text-align: right;" class="text-sm font-semibold text-gray-950 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 whitespace-nowrap">
                        @forelse($logs as $log)
                            <tr style="transition: background-color 0.15s ease-in-out;" class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">#{{ $log['id'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm font-medium text-gray-950 dark:text-white">{{ $log['user_name'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">{{ $log['role'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">{{ $log['module'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm font-medium">
                                    <span class="@if($log['action'] == 'Created') text-success-600 dark:text-success-400 
                                                @elseif($log['action'] == 'Updated') text-info-600 dark:text-info-400 
                                                @elseif($log['action'] == 'Deleted' || str_contains($log['action'], 'Failed')) text-danger-600 dark:text-danger-400 
                                                @else text-gray-600 dark:text-gray-400 @endif">
                                        {{ $log['action'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400 max-w-[150px] truncate" title="{{ $log['record_name'] }}">
                                    {{ $log['record_name'] }}
                                </td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">{{ $log['ip_address'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">{{ $log['date_time'] }}</td>
                                <td style="padding: 1rem 1.5rem;" class="text-sm">
                                    @if($log['status'] === 'Success')
                                        <x-filament::badge color="success">Success</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">Failed</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right;">
                                    <x-filament::icon-button
                                        icon="heroicon-m-eye"
                                        tag="a" href="{{ \App\Filament\Pages\ViewActivityLog::getUrl(['record' => $log['id']]) }}"
                                        color="gray"
                                        tooltip="View Details"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 2rem 1rem; text-align: center;" class="text-gray-500 dark:text-gray-400">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
            
            {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-white/10">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.8rem; opacity: 0.55; white-space: nowrap;">Rows per page:</span>
                        <x-filament::input.wrapper style="width: 80px;">
                            <x-filament::input.select wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                    <span style="font-size: 0.8rem; opacity: 0.55;">
                        Showing {{ $from }}–{{ $to }} of {{ $totalItems }} records
                    </span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <button wire:click="previousPage" @if($currentPage <= 1) disabled @endif style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: 1px solid rgba(128,128,128,0.2); background: transparent; color: inherit; cursor: {{ $currentPage <= 1 ? 'not-allowed' : 'pointer' }}; opacity: {{ $currentPage <= 1 ? '0.3' : '1' }}; transition: all 0.15s;">
                        <x-filament::icon icon="heroicon-m-chevron-left" class="h-5 w-5" />
                    </button>
                    
                    @php $pgStart = max(1, $currentPage - 2); $pgEnd = min($lastPage, $currentPage + 2); @endphp
                    @if($pgStart > 1)
                        <button wire:click="gotoPage(1)" style="width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; font-size: 0.875rem; border: 1px solid rgba(128,128,128,0.2); background: transparent; cursor: pointer; transition: all 0.15s;">1</button>
                        @if($pgStart > 2)<span style="padding: 0 0.25rem; opacity: 0.5;">...</span>@endif
                    @endif
                    
                    @for($p = $pgStart; $p <= $pgEnd; $p++)
                        <button wire:click="gotoPage({{ $p }})" style="width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; font-size: 0.875rem; border: 1px solid {{ $p === $currentPage ? 'rgba(99,102,241,0.5)' : 'rgba(128,128,128,0.2)' }}; background: {{ $p === $currentPage ? 'rgba(99,102,241,0.1)' : 'transparent' }}; color: {{ $p === $currentPage ? '#6366f1' : 'inherit' }}; font-weight: {{ $p === $currentPage ? '600' : 'normal' }}; cursor: pointer; transition: all 0.15s;">
                            {{ $p }}
                        </button>
                    @endfor
                    
                    @if($pgEnd < $lastPage)
                        @if($pgEnd < $lastPage - 1)<span style="padding: 0 0.25rem; opacity: 0.5;">...</span>@endif
                        <button wire:click="gotoPage({{ $lastPage }})" style="width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; font-size: 0.875rem; border: 1px solid rgba(128,128,128,0.2); background: transparent; cursor: pointer; transition: all 0.15s;">{{ $lastPage }}</button>
                    @endif
                    
                    <button wire:click="nextPage({{ $lastPage }})" @if($currentPage >= $lastPage) disabled @endif style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: 1px solid rgba(128,128,128,0.2); background: transparent; color: inherit; cursor: {{ $currentPage >= $lastPage ? 'not-allowed' : 'pointer' }}; opacity: {{ $currentPage >= $lastPage ? '0.3' : '1' }}; transition: all 0.15s;">
                        <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
