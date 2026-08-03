<?php
/**
 * Fix the 5 skipped blade files that use a different data loading pattern.
 * These were: manage-attractions, manage-brands, manage-dining-outlets, 
 *             manage-faqs, manage-hotels, manage-room-types
 */

$paginationUi = <<<'BLADE'

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
BLADE;

// These blades use direct variable assignment in @php without collect()
// e.g., $brands = array_filter(...) or $brands = $this->getMockBrands()
$files = [
    'resources/views/filament/pages/manage-attractions.blade.php'  => 'attractions',
    'resources/views/filament/pages/manage-brands.blade.php'       => 'brands',
    'resources/views/filament/pages/manage-dining-outlets.blade.php' => 'diningOutlets',
    'resources/views/filament/pages/manage-faqs.blade.php'         => 'faqs',
    'resources/views/filament/pages/manage-hotels.blade.php'       => 'hotels',
    'resources/views/filament/pages/manage-room-types.blade.php'   => 'roomTypes',
];

foreach ($files as $path => $varName) {
    if (!file_exists($path)) { echo "Not found: $path\n"; continue; }

    $c = file_get_contents($path);

    // Skip if already done
    if (str_contains($c, 'wire:model.live="perPage"')) {
        echo "Already done: $path\n";
        continue;
    }

    $allVar = 'all' . ucfirst($varName);

    // ── Find the @php block that sets $varName (any pattern) ──
    // Look for "$varName" assignment and rename it to "$allVar"
    // Pattern: $varName = ... until ;
    $c = preg_replace(
        '/\$(' . preg_quote($varName, '/') . ')\s*=/',
        "\$$allVar =",
        $c,
        1
    );

    // ── In @forelse use $varName (paginated), so rename collection reference back ──
    // We need to add the pagination vars after the @php block's last statement
    // Insert before @endphp
    $paginationVars = <<<PHP

            \$totalItems  = collect(\$$allVar)->count();
            \$lastPage    = max(1, (int) ceil(\$totalItems / \$perPage));
            \$currentPage = max(1, min(\$currentPage, \$lastPage));
            \$$varName    = collect(\$$allVar)->forPage(\$currentPage, \$perPage);
            \$from        = \$totalItems > 0 ? (\$currentPage - 1) * \$perPage + 1 : 0;
            \$to          = min(\$currentPage * \$perPage, \$totalItems);
PHP;

    // Insert before first @endphp after the @php block
    $c = preg_replace('/( {8})@endphp/', $paginationVars . "\n        @endphp", $c, 1);

    // ── Inject pagination UI before closing </div></x-filament-panels::page> ──
    $c = preg_replace(
        '/(\n    <\/div>\n<\/x-filament-panels::page>)/s',
        "\n{$paginationUi}\n\n    </div>\n</x-filament-panels::page>",
        $c,
        1
    );

    file_put_contents($path, $c);
    echo "Fixed: $path\n";
}

echo "Done!\n";
