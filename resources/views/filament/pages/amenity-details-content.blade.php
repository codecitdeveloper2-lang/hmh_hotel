<x-filament-panels::page>
    <form wire:submit.prevent="save" style="width: 100%;">

        {{-- Form Card --}}
        <div style="
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        ">
            {{-- Card Header --}}
            <div style="
                padding: 1.25rem 2rem;
                border-bottom: 1px solid rgba(255,255,255,0.07);
                background: rgba(99, 102, 241, 0.06);
                display: flex;
                align-items: center;
                gap: 0.875rem;
            ">
                <div style="
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 0.6rem;
                    background: rgba(99, 102, 241, 0.2);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                ">
                    <x-filament::icon icon="heroicon-m-document-text" style="width: 1.25rem; height: 1.25rem; color: #818cf8;" />
                </div>
                <div>
                    <div style="font-size: 1rem; font-weight: 600; color: inherit; letter-spacing: -0.01em;">Details Content</div>
                    <div style="font-size: 0.75rem; opacity: 0.45; margin-top: 0.15rem;">Manage the frontend content displayed on the Amenity Details page.</div>
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding: 2rem;">
                {{ $this->form }}
            </div>
        </div>

        {{-- Sticky Footer Actions --}}
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
            justify-content: flex-end;
            gap: 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        ">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::button
                    color="gray"
                    tag="a"
                    :href="$this->getBackUrl()"
                >
                    Back to Amenity
                </x-filament::button>
                <x-filament::button
                    type="submit"
                    style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; box-shadow: 0 4px 20px rgba(99,102,241,0.45);"
                >
                    <span style="display: flex; align-items: center; gap: 0.4rem;">
                        <x-filament::icon icon="heroicon-m-check" style="width: 0.9rem; height: 0.9rem;" />
                        Save Content
                    </span>
                </x-filament::button>
            </div>
        </div>

    </form>
</x-filament-panels::page>
