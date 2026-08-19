<x-filament-panels::page>
    <style>
        /* Transform disabled form fields to match Jodit Editor bordered style */
        .read-only-form .fi-input-wrp, 
        .read-only-form .fi-select-wrp, 
        .read-only-form .fi-ta-wrp,
        .read-only-form .fi-color-picker-wrp {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.5rem !important;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05) !important;
            padding: 0 !important;
            min-height: 2.75rem !important;
            display: flex;
            align-items: center;
        }

        .read-only-form input:disabled, 
        .read-only-form select:disabled, 
        .read-only-form textarea:disabled {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0.5rem 0.75rem !important;
            margin: 0 !important;
            color: #f3f4f6 !important;
            -webkit-text-fill-color: #f3f4f6 !important;
            opacity: 1 !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            width: 100%;
        }

        /* Hide select dropdown arrows and other decorative icons in disabled fields */
        .read-only-form .fi-select-wrp select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .read-only-form .fi-select-wrp svg {
            display: none !important;
        }
    </style>

    <div style="width: 100%; max-width: 100%; margin: 0 auto;">
        {{-- View Card --}}
        <div style="
            width: 100%;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        ">
            {{-- Header --}}
            <div style="
                padding: 1.25rem 2rem;
                border-bottom: 1px solid rgba(255,255,255,0.07);
                background: rgba(16, 185, 129, 0.06);
                display: flex;
                align-items: center;
                gap: 0.875rem;
            ">
                <div style="
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 0.6rem;
                    background: rgba(16, 185, 129, 0.2);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <x-filament::icon icon="heroicon-m-eye" style="width: 1.25rem; height: 1.25rem; color: #34d399;" />
                </div>
                <div>
                    <div style="font-size: 1rem; font-weight: 600; color: inherit; letter-spacing: -0.01em;">Record Details</div>
                    <div style="font-size: 0.75rem; opacity: 0.45; margin-top: 0.15rem;">This record is in read-only mode</div>
                </div>
                <div style="margin-left: auto;">
                    <x-filament::badge color="success">Read Only</x-filament::badge>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding: 2rem; width: 100%;" class="read-only-form">
                {{ $this->form }}
            </div>
        </div>

        {{-- Back Footer --}}
        <div style="
            position: sticky;
            bottom: 1rem;
            z-index: 20;
            padding: 1rem 1.5rem;
            border-radius: 0.875rem;
            background: rgba(15, 20, 35, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        ">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::icon icon="heroicon-m-eye" style="width: 1.1rem; height: 1.1rem; opacity: 0.35;" />
                <span style="font-size: 0.78rem; opacity: 0.45;">You are viewing this record in read-only mode.</span>
            </div>
            <x-filament::button
                color="gray"
                tag="a"
                :href="$this->getBackUrl()"
            >
                <span style="display: flex; align-items: center; gap: 0.4rem;">
                    <x-filament::icon icon="heroicon-m-arrow-left" style="width: 0.9rem; height: 0.9rem;" />
                    Back to List
                </span>
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>