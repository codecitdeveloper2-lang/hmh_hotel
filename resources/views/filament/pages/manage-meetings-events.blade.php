<x-filament-panels::page>
    <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div style="width: 100%; min-width: 100%;" class="divide-y divide-gray-200 overflow-x-auto dark:divide-white/10">
            <table style="width: 100%; min-width: 100%; display: table;" class="fi-ta-table text-start divide-y divide-gray-200 dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Event Image</th>
                        <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Event Title</th>
                        <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Subtitle / Category</th>
                        <th style="padding: 1rem 1.5rem; text-align: left;" class="text-sm font-semibold text-gray-950 dark:text-white">Status</th>
                        <th style="padding: 1rem 1.5rem; text-align: right;" class="text-sm font-semibold text-gray-950 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @forelse($events as $event)
                        <tr class="transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                            <td style="padding: 1rem 1.5rem;">
                                <div class="rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 dark:bg-gray-800 dark:text-gray-500 overflow-hidden" style="width: 80px; height: 50px; flex-shrink: 0;">
                                    @if(isset($event['image_url']) && $event['image_url'])
                                        <img src="{{ $event['image_url'] }}" class="object-cover" style="width: 100%; height: 100%;" alt="{{ $event['title'] }}" />
                                    @else
                                        <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem;" class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $event['title'] }}
                            </td>
                            <td style="padding: 1rem 1.5rem;" class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $event['subtitle'] }}
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                @if($event['status'] === 'Active')
                                    <x-filament::badge color="success">Active</x-filament::badge>
                                @else
                                    <x-filament::badge color="danger">Inactive</x-filament::badge>
                                @endif
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
                                            icon="heroicon-m-document-text"
                                            tag="a" href="{{ \App\Filament\Pages\Hotels\MeetingsEvents\ManageEventDetails::getUrl(['record' => $this->record, 'event_id' => $event['id']]) }}"
                                        >
                                            Event Details Page
                                        </x-filament::dropdown.list.item>

                                        <x-filament::dropdown.list.item
                                            icon="heroicon-m-pencil-square"
                                            wire:click="mountAction('editEvent', { id: {{ $event['id'] }} })"
                                        >
                                            Edit Basic Info
                                        </x-filament::dropdown.list.item>
                                        
                                        <x-filament::dropdown.list.item
                                            icon="heroicon-m-trash"
                                            color="danger"
                                            wire:click="mountAction('deleteEvent', { id: {{ $event['id'] }} })"
                                        >
                                            Delete
                                        </x-filament::dropdown.list.item>
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center;" class="text-sm text-gray-500">
                                No meetings or events found for this hotel. Click "Add Event" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
