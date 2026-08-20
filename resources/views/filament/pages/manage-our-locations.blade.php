<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-map" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Locations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalItems }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-star" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Featured Locations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ \App\Models\OurLocation::where('featured_on_home', true)->count() }}</h3>
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
                            placeholder="Search locations by city name..."
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>

        @php
            $destinationsMap = \App\Models\Destination::pluck('name', 'id')->toArray();
        @endphp

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 800px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Image</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">City Name</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Destination</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8; text-align: center;">Featured</th>
                            <th style="padding: 1rem; text-align: center; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Display Order</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($locations as $location)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    @if(!empty($location['home_image']))
                                        <img src="{{ url('/uploads/' . $location['home_image']) }}" alt="{{ $location['city_name'] }}" style="height: 3rem; width: 5rem; object-fit: cover; border-radius: 0.5rem;" />
                                    @else
                                        <div style="height: 3rem; width: 5rem; border-radius: 0.5rem; background-color: rgba(128,128,128,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                            <x-filament::icon icon="heroicon-o-photo" style="height: 1.5rem; width: 1.5rem; opacity: 0.5;" />
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ $location['city_name'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ isset($location['destination_id']) ? ($destinationsMap[$location['destination_id']] ?? 'N/A') : 'N/A' }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($location['featured_on_home'])
                                        <x-filament::badge color="warning">Featured</x-filament::badge>
                                    @else
                                        <span style="opacity: 0.5;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center; opacity: 0.8;">
                                    {{ $location['display_order'] }}
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <x-filament::dropdown placement="bottom-end" teleport>
                                        <x-slot name="trigger">
                                            <x-filament::icon-button
                                                icon="heroicon-m-ellipsis-vertical"
                                                color="gray"
                                                label="Actions"
                                            />
                                        </x-slot>
                                        <x-filament::dropdown.list class="bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-lg" style="background-color: #1f2937;">
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-pencil-square"
                                                wire:click="mountAction('editLocation', { id: {{ $location['id'] }} })"
                                            >
                                                Edit
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                wire:click="mountAction('deleteLocation', { id: {{ $location['id'] }} })"
                                            >
                                                Delete
                                            </x-filament::dropdown.list.item>
                                        </x-filament::dropdown.list>
                                    </x-filament::dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No locations found matching the criteria.
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
        </div>

    </div>
</x-filament-panels::page>
