<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <x-filament::icon icon="heroicon-o-newspaper" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Total Articles</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $totalArticles }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <x-filament::icon icon="heroicon-o-check-circle" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Published Articles</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $publishedArticles }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <x-filament::icon icon="heroicon-o-document" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Draft Articles</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $draftArticles }}</h3>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <x-filament::icon icon="heroicon-o-star" style="height: 1.5rem; width: 1.5rem;" />
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; font-weight: 500; opacity: 0.7; margin: 0;">Featured Articles</p>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $featuredArticles }}</h3>
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
                            placeholder="Search by title, category or author..."
                        />
                    </x-filament::input.wrapper>
                </div>
                
                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterCategory">
                            <option value="">All Categories</option>
                            <option value="News">News</option>
                            <option value="Press Release">Press Release</option>
                            <option value="Announcement">Announcement</option>
                            <option value="Blog">Blog</option>
                            <option value="Award">Award</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterStatus">
                            <option value="">All Statuses</option>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filterFeatured">
                            <option value="">All Featured</option>
                            <option value="1">Featured Only</option>
                            <option value="0">Not Featured</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div style="flex: 1 1 150px;">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.live="filterPublishDate"
                            placeholder="Publish Date"
                            title="Publish Date"
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>



        <!-- Table -->
        <x-filament::section>
            <div style="width: 100%; overflow-x: auto;">
                <table style="width: 100%; min-width: 1000px; display: table; border-collapse: collapse; text-align: left;" class="fi-ta-table">
                    <thead style="border-bottom: 1px solid rgba(128,128,128,0.2);">
                        <tr>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Featured Image</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Article Title</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Category</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Author</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Publish Date</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Status</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Featured</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Last Updated</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600; opacity: 0.8;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($articles as $article)
                            <tr style="border-bottom: 1px solid rgba(128,128,128,0.1); transition: background-color 0.15s ease-in-out;">
                                <td style="padding: 1rem;">
                                    <div style="height: 3rem; width: 5rem; border-radius: 0.5rem; background-color: rgba(128,128,128,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <x-filament::icon icon="heroicon-o-photo" style="height: 1.5rem; width: 1.5rem; opacity: 0.5;" />
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-weight: 500;">
                                    {{ $article['title'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $article['category'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $article['author'] }}
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $article['publish_date'] }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($article['status'] === 'Published')
                                        <x-filament::badge color="success">Published</x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">Draft</x-filament::badge>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($article['featured'])
                                        <x-filament::badge color="warning" icon="heroicon-m-star">Featured</x-filament::badge>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 1rem; opacity: 0.8;">
                                    {{ $article['last_updated'] }}
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
                                                tag="a" href="{{ \App\Filament\Pages\NewsAndPress\ViewNewsAndPress::getUrl(['record' => $article['id']]) }}"
                                            >
                                                View
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-pencil-square"
                                                tag="a" href="{{ \App\Filament\Pages\NewsAndPress\EditNewsAndPress::getUrl(['record' => $article['id']]) }}"
                                            >
                                                Edit
                                            </x-filament::dropdown.list.item>
                                            <x-filament::dropdown.list.item
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                wire:click="mountAction('deleteArticle', { id: {{ $article['id'] }} })"
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
                                    No articles found matching the criteria.
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
