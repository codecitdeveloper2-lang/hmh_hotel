<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-users" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Subscribers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">18,452</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Active Subscribers</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">16,120</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-no-symbol" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Unsubscribed</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">2,332</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <x-filament::icon icon="heroicon-o-sparkles" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">New This Month</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">458</h3>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Filters & Search -->
        <x-filament::section>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; flex-grow: 1;">
                    <div style="flex: 1 1 200px;">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model.live="searchQuery"
                                placeholder="Search Name, Email, Country..."
                            />
                        </x-filament::input.wrapper>
                    </div>
                    
                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterStatus">
                                <option value="">All Statuses</option>
                                <option value="Subscribed">Subscribed</option>
                                <option value="Unsubscribed">Unsubscribed</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterCountry">
                                <option value="">All Countries</option>
                                <option value="Singapore">Singapore</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="UAE">UAE</option>
                                <option value="Spain">Spain</option>
                                <option value="USA">USA</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Germany">Germany</option>
                                <option value="Russia">Russia</option>
                                <option value="Australia">Australia</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="France">France</option>
                                <option value="Oman">Oman</option>
                                <option value="Egypt">Egypt</option>
                                <option value="India">India</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterSource">
                                <option value="">All Sources</option>
                                <option value="Homepage">Homepage</option>
                                <option value="Footer">Footer</option>
                                <option value="Offers Page">Offers Page</option>
                                <option value="Membership Page">Membership Page</option>
                                <option value="Hotel Detail Page">Hotel Detail Page</option>
                                <option value="Popup Campaign">Popup Campaign</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="date"
                                wire:model.live="filterDate"
                                placeholder="Subscription Date"
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div>
                    <x-filament::dropdown placement="bottom-end">
                        <x-slot name="trigger">
                            <x-filament::button color="gray" icon="heroicon-m-chevron-down" icon-position="after">
                                Bulk Actions
                            </x-filament::button>
                        </x-slot>
                        <x-filament::dropdown.list class="bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-lg" style="background-color: #1f2937;">
                            <x-filament::dropdown.list.item icon="heroicon-m-document-arrow-down" wire:click="bulkAction('export')">Export Selected</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-no-symbol" color="warning" wire:click="bulkAction('unsubscribe')">Mark as Unsubscribed</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-check-circle" color="success" wire:click="bulkAction('reactivate')">Reactivate</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" wire:click="bulkAction('delete')">Delete Selected</x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
            </div>
        </x-filament::section>

        <!-- Data provided by getViewData() -->

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1100px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; width: 40px;">
                                <input type="checkbox" wire:model.live="selectedRows" value="all" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                            </th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">ID</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Subscriber</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Country</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Source</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Subscribed On</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Last Activity</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($subscribers as $item)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" wire:model.live="selectedRows" value="{{ $item['id'] }}" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                                </td>
                                <td style="padding: 1rem; font-weight: 600; font-family: monospace; color: #3b82f6;">
                                    {{ $item['subscriber_id'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    <div style="font-weight: 500;">{{ $item['full_name'] }}</div>
                                    <div style="font-size: 0.75rem;">{{ $item['email'] }}</div>
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['country'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['subscription_source'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['subscription_date'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['status'] === 'Subscribed')
                                        <x-filament::badge color="success">Subscribed</x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">Unsubscribed</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['last_activity'] }}
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
                                                tag="a" href="{{ \App\Filament\Pages\NewsletterSubscribers\ViewNewsletterSubscriber::getUrl(['record' => $item['id']]) }}"
                                            >
                                                View Details
                                            </x-filament::dropdown.list.item>
                                            @if($item['status'] === 'Subscribed')
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-no-symbol"
                                                    color="warning"
                                                    wire:click="mountAction('unsubscribe')"
                                                >
                                                    Unsubscribe
                                                </x-filament::dropdown.list.item>
                                            @else
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-check-circle"
                                                    color="success"
                                                    wire:click="mountAction('reactivate')"
                                                >
                                                    Reactivate
                                                </x-filament::dropdown.list.item>
                                            @endif
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-document-arrow-down"
                                                wire:click="mountAction('exportSubscriber')"
                                            >
                                                Export Subscriber
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                wire:click="mountAction('delete')"
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
                                    No subscribers found matching the criteria.
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
