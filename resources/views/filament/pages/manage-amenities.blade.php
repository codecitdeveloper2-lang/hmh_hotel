<x-filament-panels::page>
    <style>
        .custom-select option {
            background-color: #ffffff;
            color: #111827;
        }
        .dark .custom-select option {
            background-color: #111827;
            color: #ffffff;
        }
    </style>
    <div class="flex flex-col gap-4">
        <!-- Toolbar for search, filter, and view toggle -->
        <div style="position:relative; background:linear-gradient(135deg,rgba(99,102,241,0.06) 0%,rgba(139,92,246,0.04) 100%); border-radius:1rem; border:1px solid rgba(99,102,241,0.15); overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);" class="dark:bg-gray-900/80">


            <div style="padding:1.25rem 1.5rem; display:flex; flex-wrap:wrap; align-items:center; gap:1rem;">

                {{-- Search --}}
                <div style="position:relative; flex:2; min-width:200px;">
                    <div style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); pointer-events:none; color:rgba(99,102,241,0.7);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live="searchQuery"
                        placeholder="Search images…"
                        style="width:100%; padding:0.6rem 0.85rem 0.6rem 2.4rem; border-radius:0.6rem; border:1px solid rgba(99,102,241,0.2); background:rgba(255,255,255,0.06); color:inherit; font-size:0.875rem; outline:none; transition:border-color 0.2s, box-shadow 0.2s;"
                        onfocus="this.style.borderColor='rgba(99,102,241,0.5)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)'"
                        onblur="this.style.borderColor='rgba(99,102,241,0.2)';this.style.boxShadow='none'"
                    />
                </div>



                {{-- Category filter --}}
                <div style="position:relative; flex:1; min-width:160px;">
                    <div style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); pointer-events:none; color:rgba(139,92,246,0.7);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/></svg>
                    </div>
                    <select wire:model.live="filterCategory" class="custom-select" style="width:100%; padding:0.6rem 2rem 0.6rem 2.4rem; border-radius:0.6rem; border:1px solid rgba(99,102,241,0.2); background:rgba(255,255,255,0.06); color:inherit; font-size:0.875rem; outline:none; appearance:none; cursor:pointer; transition:border-color 0.2s;" onfocus="this.style.borderColor='rgba(99,102,241,0.5)'" onblur="this.style.borderColor='rgba(99,102,241,0.2)'">
                        <option value="">All Categories</option>
                        <option value="Hotel Exterior">Hotel Exterior</option>
                        <option value="Lobby">Lobby</option>
                        <option value="Guest Rooms">Guest Rooms</option>
                        <option value="Suites">Suites</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Swimming Pool">Swimming Pool</option>
                        <option value="Gym">Gym</option>
                        <option value="Spa">Spa</option>
                        <option value="Conference Hall">Conference Hall</option>
                        <option value="Events">Events</option>
                    </select>
                    <div style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); pointer-events:none; color:rgba(139,92,246,0.6);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>

                {{-- Status filter --}}
                <div style="position:relative; flex:0 0 auto; min-width:140px;">
                    <div style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); pointer-events:none; color:rgba(139,92,246,0.7);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <select wire:model.live="filterStatus" class="custom-select" style="width:100%; padding:0.6rem 2rem 0.6rem 2.4rem; border-radius:0.6rem; border:1px solid rgba(99,102,241,0.2); background:rgba(255,255,255,0.06); color:inherit; font-size:0.875rem; outline:none; appearance:none; cursor:pointer; transition:border-color 0.2s;" onfocus="this.style.borderColor='rgba(99,102,241,0.5)'" onblur="this.style.borderColor='rgba(99,102,241,0.2)'">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <div style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); pointer-events:none; color:rgba(139,92,246,0.6);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>


            </div>
        </div>

        @php
            $hotelName = \App\Filament\Pages\ManageHotels::getMockHotels()[$this->record]['name'] ?? '';
            $allAmenities = collect($this->getMockAmenities())
                ->filter(fn($item) => $item['hotel'] === $hotelName)
                ->when($searchQuery, fn($collection) => $collection->filter(fn($item) => stripos($item['title'], $searchQuery) !== false))
                ->when($filterCategory, fn($collection) => $collection->where('category', $filterCategory))
                ->when($filterStatus, fn($collection) => $collection->where('status', $filterStatus));

            $totalItems  = $allAmenities->count();
            $lastPage    = max(1, (int) ceil($totalItems / $perPage));
            $currentPage = max(1, min($currentPage, $lastPage));
            $amenities    = $allAmenities->forPage($currentPage, $perPage);
            $from        = $totalItems > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
            $to          = min($currentPage * $perPage, $totalItems);
        @endphp

        @if($viewType === 'table')
            <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-visible">
                <div style="width: 100%; min-width: 100%;" class="divide-y divide-gray-200 dark:divide-white/10 overflow-visible">
                    <table style="width: 100%; min-width: 100%; display: table;" class="fi-ta-table text-start divide-y divide-gray-200 dark:divide-white/5 overflow-visible">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Thumbnail</th>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Image Title</th>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Hotel Name</th>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Category</th>
                                <th style="padding: 1rem 1.5rem; text-align: center;" class="text-sm font-semibold text-gray-950 dark:text-white">Display Order</th>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Status</th>
                                <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Last Updated</th>
                                <th style="padding: 1rem 1.5rem; text-align: right;" class="text-sm font-semibold text-gray-950 dark:text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @forelse($amenities as $amenity)
                                <tr class="transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td style="padding: 1rem 1.5rem;">
                                        <div class="h-12 w-16 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 dark:bg-gray-800 dark:text-gray-500 overflow-hidden">
                                            <x-filament::icon icon="heroicon-o-sparkles" class="h-6 w-6" />
                                        </div>
                                    </td>
                                    <td style="padding: 1rem 1.5rem;" class="text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $amenity['title'] }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $amenity['hotel'] }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $amenity['category'] ?? '' }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: center;" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $amenity['display_order'] }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem;">
                                        @if($amenity['status'] === 'Active')
                                            <x-filament::badge color="success">Active</x-filament::badge>
                                        @else
                                            <x-filament::badge color="danger">Inactive</x-filament::badge>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $amenity['last_updated'] }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: right;">
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
                                                    tag="a" href="{{ \App\Filament\Pages\Hotels\Amenities\ViewAmenity::getUrl(['record' => $this->record, 'amenity_id' => $amenity['id']]) }}"
                                                >
                                                    View
                                                </x-filament::dropdown.list.item>
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-pencil-square"
                                                    tag="a" href="{{ \App\Filament\Pages\Hotels\Amenities\EditAmenity::getUrl(['record' => $this->record, 'amenity_id' => $amenity['id']]) }}"
                                                >
                                                    Edit
                                                </x-filament::dropdown.list.item>
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-trash"
                                                    color="danger"
                                                    wire:click="mountAction('deleteAmenity', { id: {{ $amenity['id'] }} })"
                                                >
                                                    Delete
                                                </x-filament::dropdown.list.item>
                                            </x-filament::dropdown.list>
                                        </x-filament::dropdown>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No images found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($amenities as $amenity)
                    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden flex flex-col">
                        <div class="h-40 bg-gray-200 dark:bg-gray-800 flex items-center justify-center relative group">
                            <x-filament::icon icon="heroicon-o-sparkles" class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <x-filament::icon-button
                                    icon="heroicon-m-eye"
                                    color="white"
                                    tag="a" href="{{ \App\Filament\Pages\Hotels\Amenities\ViewAmenity::getUrl(['record' => $this->record, 'amenity_id' => $amenity['id']]) }}"
                                />
                                <x-filament::icon-button
                                    icon="heroicon-m-pencil-square"
                                    color="white"
                                    tag="a" href="{{ \App\Filament\Pages\Hotels\Amenities\EditAmenity::getUrl(['record' => $this->record, 'amenity_id' => $amenity['id']]) }}"
                                />
                            </div>
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="text-sm font-semibold text-gray-950 dark:text-white truncate" title="{{ $amenity['title'] }}">
                                {{ $amenity['title'] }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate" title="{{ $amenity['hotel'] }}">
                                {{ $amenity['hotel'] }}
                            </p>
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 px-2 py-1 bg-gray-100 dark:bg-white/5 rounded-md">
                                    {{ $amenity['category'] ?? 'Amenity' }}
                                </span>
                                @if($amenity['status'] === 'Active')
                                    <div class="h-2.5 w-2.5 rounded-full bg-success-500" title="Active"></div>
                                @else
                                    <div class="h-2.5 w-2.5 rounded-full bg-danger-500" title="Inactive"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        No images found matching the criteria.
                    </div>
                @endforelse
            </div>
        @endif

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
