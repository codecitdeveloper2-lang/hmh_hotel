<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-envelope" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Enquiries</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">1,248</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-bell-alert" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">New Enquiries</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">24</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Responded</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">985</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <x-filament::icon icon="heroicon-o-clock" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Pending</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">42</h3>
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
                                placeholder="Search ID, Name, Email..."
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
                            <x-filament::input.select wire:model.live="filterType">
                                <option value="">All Types</option>
                                <option value="General Enquiry">General</option>
                                <option value="Hotel Booking">Booking</option>
                                <option value="Group Booking">Group Booking</option>
                                <option value="Meetings & Events">Meetings</option>
                                <option value="Wedding Enquiry">Weddings</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Feedback">Feedback</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterStatus">
                                <option value="">All Statuses</option>
                                <option value="New">New</option>
                                <option value="Pending">Pending</option>
                                <option value="Responded">Responded</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterAssigned">
                                <option value="">All Staff</option>
                                <option value="Unassigned">Unassigned</option>
                                <option value="Sarah Johnson">Sarah Johnson</option>
                                <option value="Michael Chen">Michael Chen</option>
                                <option value="Emma Davis">Emma Davis</option>
                                <option value="James Wilson">James Wilson</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="date"
                                wire:model.live="filterDate"
                                placeholder="Date"
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
                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item icon="heroicon-m-envelope-open" wire:click="bulkAction('read')">Mark as Read</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-check-circle" wire:click="bulkAction('responded')">Mark as Responded</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-user-plus" wire:click="bulkAction('assign')">Assign Staff</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-arrow-down-tray" wire:click="bulkAction('export')">Export CSV</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" wire:click="bulkAction('delete')">Delete Selected</x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
            </div>
        </x-filament::section>

        @php
            $allSubmissions = collect($this->getMockSubmissions())
                ->when($searchQuery, fn($collection) => $collection->filter(function($item) use ($searchQuery) {
                    return stripos($item['submission_id'], $searchQuery) !== false || 
                           stripos($item['customer_name'], $searchQuery) !== false ||
                           stripos($item['email'], $searchQuery) !== false ||
                           stripos($item['phone'], $searchQuery) !== false ||
                           stripos($item['subject'], $searchQuery) !== false;
                }))
                ->when($filterHotel, fn($collection) => $collection->where('hotel', $filterHotel))
                ->when($filterType, fn($collection) => $collection->where('enquiry_type', $filterType))
                ->when($filterStatus, fn($collection) => $collection->where('status', $filterStatus))
                ->when($filterAssigned, fn($collection) => $collection->where('assigned_to', $filterAssigned))
                ->when($filterDate, fn($collection) => $collection->filter(fn($item) => str_starts_with($item['submitted_on'], $filterDate)));

            $totalItems  = $allSubmissions->count();
            $lastPage    = max(1, (int) ceil($totalItems / $perPage));
            $currentPage = max(1, min($currentPage, $lastPage));
            $submissions = $allSubmissions->forPage($currentPage, $perPage);
            $from        = $totalItems > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
            $to          = min($currentPage * $perPage, $totalItems);
        @endphp

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1200px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; width: 40px;">
                                <input type="checkbox" wire:model.live="selectedRows" value="all" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                            </th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">ID</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Customer</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Contact</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Hotel & Type</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Subject</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Submitted</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Assigned</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($submissions as $item)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out; {{ $item['status'] === 'New' ? 'background-color: rgba(59, 130, 246, 0.05);' : '' }}">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" wire:model.live="selectedRows" value="{{ $item['id'] }}" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                                </td>
                                <td style="padding: 1rem; font-weight: 600; font-family: monospace; color: #3b82f6;">
                                    {{ $item['submission_id'] }}
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ $item['customer_name'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    <div>{{ $item['email'] }}</div>
                                    <div style="font-size: 0.75rem;">{{ $item['phone'] }}</div>
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    <div style="font-weight: 500;">{{ $item['hotel'] }}</div>
                                    <div style="font-size: 0.75rem;">{{ $item['enquiry_type'] }}</div>
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ \Illuminate\Support\Str::limit($item['subject'], 30) }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $item['submitted_on'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['status'] === 'New')
                                        <x-filament::badge color="info">New</x-filament::badge>
                                    @elseif($item['status'] === 'Pending')
                                        <x-filament::badge color="warning">Pending</x-filament::badge>
                                    @else
                                        <x-filament::badge color="success">Responded</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    @if($item['assigned_to'] === 'Unassigned')
                                        <span style="opacity: 0.5;">Unassigned</span>
                                    @else
                                        {{ $item['assigned_to'] }}
                                    @endif
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
                                        <x-filament::dropdown.list>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-eye"
                                                tag="a" href="{{ \App\Filament\Pages\ContactSubmissions\ViewContactSubmission::getUrl(['record' => $item['id']]) }}"
                                            >
                                                View Details
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-envelope-open"
                                                wire:click="mountAction('markAsRead')"
                                            >
                                                Mark as Read
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-check-circle"
                                                wire:click="mountAction('markAsResponded')"
                                            >
                                                Mark as Responded
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-user-plus"
                                                wire:click="mountAction('assign')"
                                            >
                                                Assign Staff
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-printer"
                                                wire:click="mountAction('print')"
                                            >
                                                Print
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-document-arrow-down"
                                                wire:click="mountAction('exportPdf')"
                                            >
                                                Export PDF
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
                                <td colspan="10" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No submissions found matching the criteria.
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
                        Showing {{ $from }}–{{ $to }} of {{ $totalItems }} submissions
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <button wire:click="previousPage" @if($currentPage <= 1) disabled @endif style="display:flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:{{ $currentPage <= 1 ? 'not-allowed' : 'pointer' }};opacity:{{ $currentPage <= 1 ? '0.3' : '1' }};transition:all 0.15s;">
                        <x-filament::icon icon="heroicon-m-chevron-left" style="width:1rem;height:1rem;" />
                    </button>
                    @php $start = max(1, $currentPage - 2); $end = min($lastPage, $currentPage + 2); @endphp
                    @if($start > 1)
                        <button wire:click="gotoPage(1)" style="min-width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid rgba(255,255,255,0.1);background:transparent;color:inherit;cursor:pointer;font-size:0.85rem;">1</button>
                        @if($start > 2)<span style="opacity:0.4;font-size:0.85rem;padding:0 0.25rem;">…</span>@endif
                    @endif
                    @for($p = $start; $p <= $end; $p++)
                        <button wire:click="gotoPage({{ $p }})" style="min-width:2.25rem;height:2.25rem;border-radius:0.5rem;border:1px solid {{ $p === $currentPage ? 'rgba(99,102,241,0.6)' : 'rgba(255,255,255,0.1)' }};background:{{ $p === $currentPage ? 'linear-gradient(135deg,#6366f1,#8b5cf6)' : 'transparent' }};color:inherit;cursor:pointer;font-size:0.85rem;font-weight:{{ $p === $currentPage ? '600' : '400' }};box-shadow:{{ $p === $currentPage ? '0 2px 12px rgba(99,102,241,0.35)' : 'none' }};transition:all 0.15s;">{{ $p }}</button>
                    @endfor
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)<span style="opacity:0.4;font-size:0.85rem;padding:0 0.25rem;">…</span>@endif
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
