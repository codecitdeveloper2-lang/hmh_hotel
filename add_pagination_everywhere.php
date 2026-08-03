<?php
/**
 * add_pagination_everywhere.php
 * Adds pagination to ALL Manage*.php + manage-*.blade.php files that have tables.
 * Skips files that already have pagination.
 */

// ─── 1. PHP CLASSES ───────────────────────────────────────────────────────────
$phpFiles = glob('app/Filament/Pages/Manage*.php');
$skipPhp  = ['ManageSettings']; // no table
$phpDone  = 0;

foreach ($phpFiles as $file) {
    $basename = basename($file, '.php');
    if (in_array($basename, $skipPhp)) continue;

    $c = file_get_contents($file);

    // Skip if already has pagination
    if (str_contains($c, 'public int $perPage')) {
        echo "PHP already done: $file\n";
        continue;
    }

    // Find public $xxx = '' properties and collect filter var names for updated hooks
    preg_match_all('/    public \$(\w+) = [\'"][\'"];/', $c, $propMatches);
    $filterProps = $propMatches[1] ?? [];
    // Remove non-filter props (selectedRows, etc.)
    $filterProps = array_filter($filterProps, fn($p) => str_starts_with($p, 'filter') || str_starts_with($p, 'search'));

    // Build updated hooks
    $updatedHooks = '';
    foreach ($filterProps as $prop) {
        $ucProp = ucfirst($prop);
        $updatedHooks .= "\n    public function updated{$ucProp}(): void { \$this->currentPage = 1; }";
    }

    // Pagination properties & methods block
    $paginationBlock = <<<PHP

    public int \$perPage = 10;
    public int \$currentPage = 1;
{$updatedHooks}
    public function updatedPerPage(): void { \$this->currentPage = 1; }

    public function nextPage(int \$lastPage): void
    {
        if (\$this->currentPage < \$lastPage) \$this->currentPage++;
    }

    public function previousPage(): void
    {
        if (\$this->currentPage > 1) \$this->currentPage--;
    }

    public function gotoPage(int \$page): void
    {
        \$this->currentPage = \$page;
    }
PHP;

    // Insert after the last `public $xxx` property block
    // We look for the last line matching `    public $xxx = ...;` followed by a blank line
    $c = preg_replace(
        '/(    public \$selectedRows\s*=\s*\[\];)(\s*\n)/s',
        "$1\n{$paginationBlock}\n",
        $c,
        1
    );

    // If selectedRows not present, insert after last `public $xxx = '';`
    if (!str_contains($c, 'public int $perPage')) {
        // fallback: insert before first public function
        $c = preg_replace(
            '/(    public static function|    public function|    protected function)/',
            "{$paginationBlock}\n\n    $1",
            $c,
            1
        );
    }

    file_put_contents($file, $c);
    echo "PHP done: $file\n";
    $phpDone++;
}

echo "\n--- PHP: patched $phpDone files ---\n\n";

// ─── 2. BLADE FILES ───────────────────────────────────────────────────────────
$bladeFiles = glob('resources/views/filament/pages/manage-*.blade.php');
$skipBlade  = ['manage-settings']; // no table
$bladeDone  = 0;

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

foreach ($bladeFiles as $file) {
    $basename = basename($file, '.blade.php');
    foreach ($skipBlade as $skip) {
        if (str_contains($basename, $skip)) continue 2;
    }

    $c = file_get_contents($file);

    // Skip if already has pagination
    if (str_contains($c, 'wire:model.live="perPage"')) {
        echo "Blade already done: $file\n";
        continue;
    }

    // ── Step A: Find the collection variable name ──
    // Matches: $varName = collect($this->getMock*())
    if (!preg_match('/\$(\w+)\s*=\s*collect\(\$this->get\w+\(\)\)/', $c, $varMatch)) {
        echo "Blade SKIP (no collect found): $file\n";
        continue;
    }
    $varName    = $varMatch[1];                           // e.g. "offers"
    $allVarName = 'all' . ucfirst($varName);             // e.g. "allOffers"

    // ── Step B: Rename $varName = collect(...) to $allVarName = collect(...)
    $c = preg_replace(
        '/(\$)(' . preg_quote($varName, '/') . ')(\s*=\s*collect\(\$this->get\w+\(\)\))/',
        '$1' . $allVarName . '$3',
        $c,
        1
    );

    // ── Step C: After the ->when chain ending with `;`, inject pagination computation
    // We look for `    @endphp` that closes the filter @php block
    $paginationVars = <<<PHP

            \$totalItems  = \$$allVarName->count();
            \$lastPage    = max(1, (int) ceil(\$totalItems / \$perPage));
            \$currentPage = max(1, min(\$currentPage, \$lastPage));
            \$$varName    = \$$allVarName->forPage(\$currentPage, \$perPage);
            \$from        = \$totalItems > 0 ? (\$currentPage - 1) * \$perPage + 1 : 0;
            \$to          = min(\$currentPage * \$perPage, \$totalItems);
PHP;

    // Insert pagination vars before `@endphp` (only the first @endphp after the collect block)
    $c = preg_replace('/( {8})@endphp/', $paginationVars . "\n        @endphp", $c, 1);

    // ── Step D: Add pagination UI before final </div></x-filament-panels::page>
    $c = preg_replace(
        '/(\n    <\/div>\n<\/x-filament-panels::page>)/s',
        "\n{$paginationUi}\n\n    </div>\n</x-filament-panels::page>",
        $c,
        1
    );

    file_put_contents($file, $c);
    echo "Blade done: $file  [var=\$$varName]\n";
    $bladeDone++;
}

echo "\n--- Blade: patched $bladeDone files ---\n";
echo "\nAll done!\n";
