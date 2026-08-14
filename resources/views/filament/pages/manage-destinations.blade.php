<x-filament-panels::page>
    @php
        $allDestinations = collect($this->getMockDestinations())
            ->when($searchQuery, fn($collection) => $collection->filter(fn($item) => stripos($item['name'], $searchQuery) !== false))
            ->when($filterCountry, fn($collection) => $collection->where('country', $filterCountry))
            ->when($filterStatus, fn($collection) => $collection->where('status', $filterStatus));

        $totalItems  = $allDestinations->count();
        $lastPage    = max(1, (int) ceil($totalItems / max(1, $perPage)));
        $currentPage = max(1, min($currentPage, $lastPage));
        $destinations    = $allDestinations->forPage($currentPage, $perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
        $to          = min($currentPage * $perPage, $totalItems);
        
        $totalDestinationsCount = $totalItems;
        $activeDestinationsCount = $allDestinations->where('status', 'Active')->count();
        $totalHotelsCount = $allDestinations->sum('hotels_count');
        $recentlyUpdated = $allDestinations->sortByDesc('last_updated')->first()['name'] ?? 'N/A';
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-map" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Destinations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalDestinationsCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-badge" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Active Destinations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $activeDestinationsCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-building-office-2" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Hotels</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalHotelsCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(107, 114, 128, 0.1); color: #6b7280;">
                        <x-filament::icon icon="heroicon-o-clock" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Recently Updated</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $recentlyUpdated }}</h3>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Filters & Search -->
        <x-filament::section>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <div style="flex: 1 1 250px;">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model.live="searchQuery"
                            placeholder="Search destinations by name..."
                        />
                    </x-filament::input.wrapper>
                </div>
                
                <div style="flex: 1 1 200px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterCountry">
                            <option value="">All Countries</option>
                            <option value="United Arab Emirates">United Arab Emirates</option>
                            <option value="Saudi Arabia">Saudi Arabia</option>
                            <option value="Bahrain">Bahrain</option>
                            <option value="Oman">Oman</option>
                            <option value="Qatar">Qatar</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 200px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterStatus">
                            <option value="">All Statuses</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 800px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Destination Banner</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Destination Name</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Country</th>
                            <th style="padding: 1rem; text-align: center; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Hotels Count</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; text-align: center; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Display Order</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Last Updated</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($destinations as $destination)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    <div style="height: 3rem; width: 5rem; border-radius: 0.5rem; background-color: rgba(128,128,128,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <x-filament::icon icon="heroicon-o-photo" style="height: 1.5rem; width: 1.5rem; opacity: 0.5;" />
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ $destination['name'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $destination['country'] }}
                                </td>
                                <td style="padding: 1rem; text-align: center; opacity: 0.8;">
                                    {{ $destination['hotels_count'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($destination['status'] === 'Active')
                                        <x-filament::badge color="success">Active</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">Inactive</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center; opacity: 0.8;">
                                    {{ $destination['display_order'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $destination['last_updated'] }}
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <x-filament::dropdown placement="bottom-end">
                                        <x-slot name="trigger">
                                            <x-filament::icon-button
                                                icon="heroicon-m-ellipsis-vertical"
                                                color="gray"
                                                label="Actions"
                                            />
                                        </x-slot>
                                        <x-filament::dropdown.list class="bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-lg" style="background-color: #1f2937;">
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-eye"
                                                tag="a" href="{{ \App\Filament\Pages\Destinations\ViewDestination::getUrl(['record' => $destination['id']]) }}"
                                            >
                                                View
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-pencil-square"
                                                tag="a" href="{{ \App\Filament\Pages\Destinations\EditDestination::getUrl(['record' => $destination['id']]) }}"
                                            >
                                                Edit
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                wire:click="mountAction('deleteDestination', { id: {{ $destination['id'] }} })"
                                            >
                                                Delete
                                            </x-filament::dropdown.list.item>
                                        </x-filament::dropdown.list>
                                    </x-filament::dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No destinations found matching the criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>


        {{-- Pagination --}}
        <x-filament::section>
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
                    <button wire:click="previousPage" @if($currentPage <= 1) disabled @endif style="display:flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:{{ $currentPage <= 1 ? 'not-allowed' : 'pointer' }};opacity:{{ $currentPage <= 1 ? '0.3' : '1' }};transition:all 0.15s;">
                        <x-filament::icon icon="heroicon-m-chevron-left" style="width:1rem;height:1rem;" />
                    </button>
                    @php $pgStart = max(1, $currentPage - 2); $pgEnd = min($lastPage, $currentPage + 2); @endphp
                    @if($pgStart > 1)
                        <button wire:click="gotoPage(1)" style="min-width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:pointer;font-size:0.85rem;">1</button>
                        @if($pgStart > 2)<span style="opacity:0.4;font-size:0.85rem;padding:0 0.25rem;">…</span>@endif
                    @endif
                    @for($p = $pgStart; $p <= $pgEnd; $p++)
                        <button wire:click="gotoPage({{ $p }})" style="min-width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid {{ $p === $currentPage ? 'rgba(99,102,241,0.6)' : 'rgba(255,255,255,0.1)' }};background:{{ $p === $currentPage ? 'linear-gradient(135deg,#6366f1,#8b5cf6)' : 'transparent' }};color:inherit;cursor:pointer;font-size:0.85rem;font-weight:{{ $p === $currentPage ? '600' : '400' }};box-shadow:{{ $p === $currentPage ? '0 2px 12px rgba(99,102,241,0.35)' : 'none' }};transition:all 0.15s;">{{ $p }}</button>
                    @endfor
                    @if($pgEnd < $lastPage)
                        @if($pgEnd < $lastPage - 1)<span style="opacity:0.4;font-size:0.85rem;padding:0 0.25rem;">…</span>@endif
                        <button wire:click="gotoPage({{ $lastPage }})" style="min-width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:pointer;font-size:0.85rem;">{{ $lastPage }}</button>
                    @endif
                    <button wire:click="nextPage({{ $lastPage }})" @if($currentPage >= $lastPage) disabled @endif style="display:flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:{{ $currentPage >= $lastPage ? 'not-allowed' : 'pointer' }};opacity:{{ $currentPage >= $lastPage ? '0.3' : '1' }};transition:all 0.15s;">
                        <x-filament::icon icon="heroicon-m-chevron-right" style="width:1rem;height:1rem;" />
                    </button>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
