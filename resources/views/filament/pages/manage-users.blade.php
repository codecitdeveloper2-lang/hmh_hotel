<x-filament-panels::page>
    @php
        $totalUsersCount  = \App\Models\User::count();
        $activeUsersCount = \App\Models\User::where('is_active', true)->count();
        $inactiveCount    = \App\Models\User::where('is_active', false)->count();
    @endphp
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-user-group" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Users</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalUsersCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Active Users</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $activeUsersCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <x-filament::icon icon="heroicon-o-no-symbol" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Inactive Users</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $inactiveCount }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <x-filament::icon icon="heroicon-o-bolt" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Registered Today</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ \App\Models\User::whereDate('created_at', today())->count() }}</h3>
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
                                placeholder="Search Name, Email, Emp ID, Username..."
                            />
                        </x-filament::input.wrapper>
                    </div>
                    
                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterRole">
                                <option value="">All Roles</option>
                                <option value="Super Admin">Super Admin</option>
                                <option value="System Admin">System Admin</option>
                                <option value="Hotel Manager">Hotel Manager</option>
                                <option value="Marketing Manager">Marketing Manager</option>
                                <option value="Reservation Manager">Reservation Manager</option>
                                <option value="Content Editor">Content Editor</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterDepartment">
                                <option value="">All Departments</option>
                                <option value="Administration">Administration</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Reservations">Reservations</option>
                                <option value="Operations">Operations</option>
                                <option value="Finance">Finance</option>
                                <option value="Human Resources">Human Resources</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div style="flex: 1 1 130px;">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="filterHotel">
                                <option value="">All Hotels</option>
                                <option value="Global (All Hotels)">Global (All Hotels)</option>
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
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                            </x-filament::input.select>
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
                            <x-filament::dropdown.list.item icon="heroicon-m-check-circle" color="success" wire:click="bulkAction('activate')">Activate Users</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-pause-circle" color="warning" wire:click="bulkAction('suspend')">Suspend Users</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-document-arrow-down" wire:click="bulkAction('export')">Export Users</x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger" wire:click="bulkAction('delete')">Delete Selected</x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
            </div>
        </x-filament::section>

        @php
            $allUsers = \App\Models\User::query()
                ->when($searchQuery, fn($q) => $q->where(function($q2) use ($searchQuery) {
                    $q2->where('name', 'like', "%{$searchQuery}%")
                       ->orWhere('email', 'like', "%{$searchQuery}%");
                }))
                ->when($filterStatus === 'Active',    fn($q) => $q->where('is_active', true))
                ->when($filterStatus === 'Inactive',  fn($q) => $q->where('is_active', false))
                ->when($filterStatus === 'Suspended', fn($q) => $q->where('is_active', false))
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($user) => [
                    'id'             => $user->id,
                    'full_name'      => $user->name,
                    'employee_id'    => 'EMP-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'email'          => $user->email,
                    'phone'          => '—',
                    'department'     => '—',
                    'assigned_hotel' => 'Global (All Hotels)',
                    'role'           => 'User',
                    'username'       => explode('@', $user->email)[0],
                    'last_login'     => $user->updated_at?->format('Y-m-d H:i') ?? '—',
                    'created_date'   => $user->created_at?->format('Y-m-d') ?? '—',
                    'status'         => $user->is_active ? 'Active' : 'Inactive',
                ]);

            $totalUsers  = $allUsers->count();
            $lastPage    = max(1, (int) ceil($totalUsers / $perPage));
            $currentPage = max(1, min($currentPage, $lastPage));
            $users       = $allUsers->forPage($currentPage, $perPage);
            $from        = $totalUsers > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
            $to          = min($currentPage * $perPage, $totalUsers);
        @endphp

        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1300px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; width: 40px;">
                                <input type="checkbox" wire:model.live="selectedRows" value="all" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                            </th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">User Profile</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Contact Information</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Role & Department</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Assigned Hotel</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Last Login</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($users as $item)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    <input type="checkbox" wire:model.live="selectedRows" value="{{ $item['id'] }}" style="border-radius: 0.25rem; border: 1px solid rgba(128,128,128,0.5);" />
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="height: 2.5rem; width: 2.5rem; border-radius: 9999px; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; overflow: hidden; font-weight: bold; color: #3b82f6;">
                                            {{ substr($item['full_name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">{{ $item['full_name'] }}</div>
                                            <div style="font-family: monospace; font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">{{ $item['employee_id'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    <div>{{ $item['email'] }}</div>
                                    <div style="font-size: 0.75rem;">{{ $item['phone'] }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 500;">
                                        @if($item['role'] === 'Super Admin')
                                            <span style="color: #ef4444;">{{ $item['role'] }}</span>
                                        @elseif($item['role'] === 'System Admin')
                                            <span style="color: #8b5cf6;">{{ $item['role'] }}</span>
                                        @else
                                            {{ $item['role'] }}
                                        @endif
                                    </div>
                                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 0.125rem;">{{ $item['department'] }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['assigned_hotel'] === 'Global (All Hotels)')
                                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">Global</span>
                                    @else
                                        <span style="opacity: 0.9;">{{ $item['assigned_hotel'] }}</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; opacity: 0.8; font-size: 0.75rem;">
                                    {{ $item['last_login'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($item['status'] === 'Active')
                                        <x-filament::badge color="success">Active</x-filament::badge>
                                    @elseif($item['status'] === 'Inactive')
                                        <x-filament::badge color="gray">Inactive</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">Suspended</x-filament::badge>
                                    @endif
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
                                                tag="a" href="{{ \App\Filament\Pages\Users\ViewUser::getUrl(['record' => $item['id']]) }}"
                                            >
                                                View
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-pencil-square"
                                                tag="a" href="{{ \App\Filament\Pages\Users\EditUser::getUrl(['record' => $item['id']]) }}"
                                            >
                                                Edit
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-key"
                                                wire:click="mountAction('resetPassword')"
                                            >
                                                Reset Password
                                            </x-filament::dropdown.list.item>
                                            
                                            @if($item['status'] === 'Active' || $item['status'] === 'Inactive')
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-pause-circle"
                                                    color="warning"
                                                    wire:click="mountAction('suspendUser')"
                                                >
                                                    Suspend User
                                                </x-filament::dropdown.list.item>
                                            @else
                                                <x-filament::dropdown.list.item
                                                    icon="heroicon-m-check-circle"
                                                    color="success"
                                                    wire:click="mountAction('activateUser')"
                                                >
                                                    Activate User
                                                </x-filament::dropdown.list.item>
                                            @endif
                                            
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
                                <td colspan="8" style="padding: 2rem; text-align: center; opacity: 0.6;">
                                    No users found matching the criteria.
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

                {{-- Left: Per page + info --}}
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
                        Showing {{ $from }}–{{ $to }} of {{ $totalUsers }} users
                    </span>
                </div>

                {{-- Right: Page controls --}}
                <div style="display: flex; align-items: center; gap: 0.4rem;">

                    {{-- Previous --}}
                    <button
                        wire:click="previousPage"
                        @if($currentPage <= 1) disabled @endif
                        style="
                            display: flex; align-items: center; justify-content: center;
                            width: 2.25rem; height: 2.25rem;
                            border-radius: 0.5rem;
                            border: 1px solid rgba(255,255,255,0.1);
                            background: transparent;
                            color: inherit;
                            cursor: {{ $currentPage <= 1 ? 'not-allowed' : 'pointer' }};
                            opacity: {{ $currentPage <= 1 ? '0.3' : '1' }};
                            transition: all 0.15s;
                        "
                    >
                        <x-filament::icon icon="heroicon-m-chevron-left" style="width: 1rem; height: 1rem;" />
                    </button>

                    {{-- Page numbers --}}
                    @php
                        $start = max(1, $currentPage - 2);
                        $end   = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($start > 1)
                        <button wire:click="gotoPage(1)" style="min-width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: inherit; cursor: pointer; font-size: 0.85rem; transition: all 0.15s;">1</button>
                        @if($start > 2)
                            <span style="opacity: 0.4; font-size: 0.85rem; padding: 0 0.25rem;">…</span>
                        @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        <button
                            wire:click="gotoPage({{ $p }})"
                            style="
                                min-width: 2.25rem; height: 2.25rem;
                                border-radius: 0.5rem;
                                border: 1px solid {{ $p === $currentPage ? 'rgba(99,102,241,0.6)' : 'rgba(255,255,255,0.1)' }};
                                background: {{ $p === $currentPage ? 'linear-gradient(135deg,#6366f1,#8b5cf6)' : 'transparent' }};
                                color: inherit;
                                cursor: pointer;
                                font-size: 0.85rem;
                                font-weight: {{ $p === $currentPage ? '600' : '400' }};
                                box-shadow: {{ $p === $currentPage ? '0 2px 12px rgba(99,102,241,0.35)' : 'none' }};
                                transition: all 0.15s;
                            "
                        >{{ $p }}</button>
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span style="opacity: 0.4; font-size: 0.85rem; padding: 0 0.25rem;">…</span>
                        @endif
                        <button wire:click="gotoPage({{ $lastPage }})" style="min-width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: inherit; cursor: pointer; font-size: 0.85rem; transition: all 0.15s;">{{ $lastPage }}</button>
                    @endif

                    {{-- Next --}}
                    <button
                        wire:click="nextPage({{ $lastPage }})"
                        @if($currentPage >= $lastPage) disabled @endif
                        style="
                            display: flex; align-items: center; justify-content: center;
                            width: 2.25rem; height: 2.25rem;
                            border-radius: 0.5rem;
                            border: 1px solid rgba(255,255,255,0.1);
                            background: transparent;
                            color: inherit;
                            cursor: {{ $currentPage >= $lastPage ? 'not-allowed' : 'pointer' }};
                            opacity: {{ $currentPage >= $lastPage ? '0.3' : '1' }};
                            transition: all 0.15s;
                        "
                    >
                        <x-filament::icon icon="heroicon-m-chevron-right" style="width: 1rem; height: 1rem;" />
                    </button>

                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
