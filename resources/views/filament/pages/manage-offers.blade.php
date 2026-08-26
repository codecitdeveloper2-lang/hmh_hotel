<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-tag" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Offers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">6</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Active Offers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">4</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <x-filament::icon icon="heroicon-o-x-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Expired Offers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">1</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(107, 114, 128, 0.1); color: #6b7280;">
                        <x-filament::icon icon="heroicon-o-document-text" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Draft Offers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">1</h3>
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
                            placeholder="Search by title, hotel, or promo code..."
                        />
                    </x-filament::input.wrapper>
                </div>
                
                <div style="flex: 1 1 200px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterHotel">
                            <option value="">All Hotels</option>
                            <option value="Coral Beach Resort Sharjah">Coral Beach Resort Sharjah</option>
                            <option value="Coral Dubai Deira Hotel">Coral Dubai Deira Hotel</option>
                            <option value="ECOS Dubai Hotel">ECOS Dubai Hotel</option>
                            <option value="EWA Hotel Apartments">EWA Hotel Apartments</option>
                            <option value="Opera Hotel">Opera Hotel</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterType">
                            <option value="">All Offer Types</option>
                            <option value="Seasonal">Seasonal</option>
                            <option value="Weekend">Weekend</option>
                            <option value="Family">Family</option>
                            <option value="Corporate">Corporate</option>
                            <option value="Honeymoon">Honeymoon</option>
                            <option value="Long Stay">Long Stay</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterStatus">
                            <option value="">All Statuses</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Expired">Expired</option>
                            <option value="Draft">Draft</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.live="filterDateFrom"
                            placeholder="Date From"
                            title="Date From"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.live="filterDateTo"
                            placeholder="Date To"
                            title="Date To"
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>

        @php
            $allOffers = collect($this->getDatabaseOffers())
                ->when($searchQuery, fn($collection) => $collection->filter(function($item) use ($searchQuery) {
                    $title = is_array($item['title']) ? ($item['title']['en'] ?? '') : $item['title'];
                    return stripos($title, $searchQuery) !== false || 
                           stripos($item['hotel'], $searchQuery) !== false || 
                           stripos($item['promo_code'], $searchQuery) !== false;
                }))
                ->when($filterHotel, fn($collection) => $collection->where('hotel', $filterHotel))
                ->when($filterType, fn($collection) => $collection->where('offer_type', $filterType))
                ->when($filterStatus, fn($collection) => $collection->where('status', $filterStatus))
                ->when($filterDateFrom, fn($collection) => $collection->where('valid_from', '>=', $filterDateFrom))
                ->when($filterDateTo, fn($collection) => $collection->where('valid_until', '<=', $filterDateTo));
        @endphp

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1000px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Offer Banner</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Offer Title</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Hotel Name</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Offer Type</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Valid From</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Valid Until</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Last Updated</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($offers as $offer)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    @if(!empty($offer['image_url']))
                                        <div class="rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-800" style="width: 100px; height: 64px; min-width: 100px;">
                                            <img src="{{ $offer['image_url'] }}" alt="{{ is_array($offer['title']) ? ($offer['title']['en'] ?? 'Offer Banner') : $offer['title'] }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                        </div>
                                    @else
                                        <div class="rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 dark:bg-gray-800 dark:text-gray-500" style="width: 100px; height: 64px; min-width: 100px;">
                                            <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ is_array($offer['title']) ? ($offer['title']['en'] ?? '') : $offer['title'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $offer['hotel'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $offer['offer_type'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $offer['valid_from'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $offer['valid_until'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($offer['status'] === 'Active')
                                        <x-filament::badge color="success">Active</x-filament::badge>
                                    @elseif($offer['status'] === 'Draft')
                                        <x-filament::badge color="gray">Draft</x-filament::badge>
                                    @elseif($offer['status'] === 'Expired')
                                        <x-filament::badge color="danger">Expired</x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning">Inactive</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $offer['last_updated'] }}
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
                                                icon="heroicon-m-eye"
                                                tag="a" href="{{ \App\Filament\Pages\Offers\ViewOffer::getUrl(['record' => $offer['id']]) }}"
                                            >
                                                View
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-pencil-square"
                                                tag="a" href="{{ \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => $offer['id']]) }}"
                                            >
                                                Edit
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                wire:click="mountAction('deleteOffer', { id: {{ $offer['id'] }} })"
                                            >
                                                Delete
                                            </x-filament::dropdown.list.item>
                                        </x-filament::dropdown.list>
                                    </x-filament::dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No offers found matching the criteria.
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
