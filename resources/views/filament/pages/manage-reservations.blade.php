<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-calendar-days" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Reservations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">4,892</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-arrow-right-on-rectangle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Today's Check-ins</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">124</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <x-filament::icon icon="heroicon-o-arrow-left-on-rectangle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Today's Check-outs</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">98</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-clock" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Pending Reservations</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">45</h3>
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
                                placeholder="Search Res No, Name, Email..."
                            />
                        </x-filament::input.wrapper>
                    </div>
                    
                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterHotel">
                                <option value="">All Hotels</option>
                                <option value="Coral Beach Resort Sharjah">Coral Beach Resort</option>
                                <option value="Bahi Ajman Palace Hotel">Bahi Ajman Palace</option>
                                <option value="Coral Dubai Deira Hotel">Coral Dubai Deira</option>
                                <option value="ECOS Dubai Hotel">ECOS Dubai</option>
                                <option value="EWA Hotel Apartments">EWA Hotel Apts</option>
                                <option value="Corp Amman Hotel">Corp Amman</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterStatus">
                                <option value="">All Statuses</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Pending">Pending</option>
                                <option value="Checked In">Checked In</option>
                                <option value="Checked Out">Checked Out</option>
                                <option value="Cancelled">Cancelled</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterPaymentStatus">
                                <option value="">All Payments</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                                <option value="Refunded">Refunded</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterSource">
                                <option value="">All Sources</option>
                                <option value="Website">Website</option>
                                <option value="TravelClick">TravelClick</option>
                                <option value="Booking.com">Booking.com</option>
                                <option value="Expedia">Expedia</option>
                                <option value="Agoda">Agoda</option>
                                <option value="Corporate">Corporate</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="date"
                                wire:model.live="filterCheckIn"
                                placeholder="Check-in Date"
                            />
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div>
                    <x-filament::dropdown placement="bottom-end" teleport>
                        <x-slot name="trigger">
                            <x-filament::button color="gray" icon="heroicon-m-chevron-down" icon-position="after">
                                Bulk Actions
                            </x-filament::button>
                        </x-slot>
                        <x-filament::dropdown.list class="bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-lg" style="background-color: #1f2937;">
                            <x-filament::dropdown.list.item icon="heroicon-m-check-circle" color="success" wire:click="bulkAction('confirm')">Mark as Confirmed</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-printer" wire:click="bulkAction('print')">Print Selected</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-document-arrow-down" wire:click="bulkAction('export')">Export Selected</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-x-circle" color="danger" wire:click="bulkAction('cancel')">Cancel Selected</x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
            </div>
        </x-filament::section>

        <!-- Data provided by getViewData() -->

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1400px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; width: 40px;">
                                <input type="checkbox" wire:model.live="selectedRows" value="all" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                            </th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Res Number</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Guest Details</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Hotel & Room</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Stay Dates</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Guests</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Source</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Res Status</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Payment</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($reservations as $item)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" wire:model.live="selectedRows" value="{{ $item['id'] }}" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                                </td>
                                <td style="padding: 1rem; font-weight: 600; font-family: monospace; color: #3b82f6;">
                                    {{ $item['reservation_number'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 500;">{{ $item['guest_name'] }}</div>
                                    <div style="font-size: 0.75rem; opacity: 0.8;">{{ $item['email'] }}</div>
                                    <div style="font-size: 0.75rem; opacity: 0.8;">{{ $item['phone'] }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 500; opacity: 0.9;">{{ $item['hotel'] }}</div>
                                    <div style="font-size: 0.75rem; opacity: 0.7;">{{ $item['room_type'] }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="white-space: nowrap;">In: <span style="font-weight: 500;">{{ $item['check_in_date'] }}</span></div>
                                    <div style="white-space: nowrap;">Out: <span style="font-weight: 500;">{{ $item['check_out_date'] }}</span></div>
                                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 0.25rem;">({{ $item['number_of_nights'] }} nights)</div>
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['number_of_adults'] }} Adults<br>
                                    {{ $item['number_of_children'] }} Children
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['booking_source'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['reservation_status'] === 'Confirmed')
                                        <x-filament::badge color="success">Confirmed</x-filament::badge>
                                    @elseif($item['reservation_status'] === 'Pending')
                                        <x-filament::badge color="warning">Pending</x-filament::badge>
                                    @elseif($item['reservation_status'] === 'Checked In')
                                        <x-filament::badge color="info">Checked In</x-filament::badge>
                                    @elseif($item['reservation_status'] === 'Checked Out')
                                        <x-filament::badge color="gray">Checked Out</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">Cancelled</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['payment_status'] === 'Paid')
                                        <x-filament::badge color="success" size="sm">Paid</x-filament::badge>
                                    @elseif($item['payment_status'] === 'Pending')
                                        <x-filament::badge color="warning" size="sm">Pending</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger" size="sm">Refunded</x-filament::badge>
                                    @endif
                                    <div style="font-size: 0.75rem; margin-top: 0.25rem; font-weight: 500;">
                                        {{ $item['currency'] }} {{ $item['total_amount'] }}
                                    </div>
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
                                                tag="a" href="{{ \App\Filament\Pages\Reservations\ViewReservation::getUrl(['record' => $item['id']]) }}"
                                            >
                                                View Details
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-printer"
                                                wire:click="mountAction('printReservation')"
                                            >
                                                Print Reservation
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-document-arrow-down"
                                                wire:click="mountAction('exportPdf')"
                                            >
                                                Export PDF
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-envelope"
                                                wire:click="mountAction('sendConfirmationEmail')"
                                            >
                                                Send Confirmation
                                            </x-filament::dropdown.list.item>
                                            
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-arrow-right-on-rectangle"
                                                color="success"
                                                wire:click="mountAction('markAsCheckedIn')"
                                            >
                                                Mark Checked In
                                            </x-filament::dropdown.list.item>
                                            
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-arrow-left-on-rectangle"
                                                color="gray"
                                                wire:click="mountAction('markAsCheckedOut')"
                                            >
                                                Mark Checked Out
                                            </x-filament::dropdown.list.item>
                                            
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-x-circle"
                                                color="danger"
                                                wire:click="mountAction('cancelReservation')"
                                            >
                                                Cancel Reservation
                                            </x-filament::dropdown.list.item>
                                        </x-filament::dropdown.list>
                                    </x-filament::dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No reservations found matching the criteria.
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
