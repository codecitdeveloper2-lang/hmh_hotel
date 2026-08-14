<x-filament-panels::page>
    <div style="width: 100%; margin-bottom: 2rem; background-color: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
        
        {{-- Banner Section --}}
        <div style="position: relative; width: 100%; height: 350px; background-image: url('https://images.unsplash.com/photo-1542314831-c6a4d14d8835?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center; display: flex; align-items: center; padding-left: 4rem;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);"></div>
            <h1 style="position: relative; color: #ffffff; font-size: 3.5rem; font-weight: 700; z-index: 10; letter-spacing: 1px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                VIEW OFFERS
            </h1>
        </div>

        {{-- Highlighted Section --}}
        <div style="text-align: center; padding: 4rem 2rem; background: #ffffff;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2 style="color: #003366; font-size: 2.2rem; font-weight: 500; letter-spacing: 2px; margin-bottom: 1.5rem; text-transform: uppercase;">
                    GREAT OFFERS ARE JUST A CLICK
                    <div style="width: 60px; height: 3px; background-color: #f59e0b; margin: 1rem auto 0;"></div>
                </h2>
                <p style="color: #4b5563; font-size: 1.2rem; margin-bottom: 0.75rem; font-weight: 500;">
                    Unbeatable packages for your holidays
                </p>
                <p style="color: #6b7280; font-size: 1rem; line-height: 1.6;">
                    Elevate your stay with exclusive offers designed to enhance every moment of your journey.
                </p>
            </div>
        </div>

    </div>

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
                    <div style="font-size: 1rem; font-weight: 600; color: inherit; letter-spacing: -0.01em;">Create Offer Form</div>
                    <div style="font-size: 0.75rem; opacity: 0.45; margin-top: 0.15rem;">Fill in all required fields marked with *</div>
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
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        ">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <x-filament::icon icon="heroicon-m-information-circle" style="width: 1rem; height: 1rem; opacity: 0.35;" />
                <span style="font-size: 0.78rem; opacity: 0.45;">Changes are saved as mock data only.</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::button
                    color="gray"
                    tag="a"
                    :href="$this->getBackUrl()"
                >
                    Cancel
                </x-filament::button>
                <x-filament::button
                    type="submit"
                    style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; box-shadow: 0 4px 20px rgba(99,102,241,0.45);"
                >
                    <span style="display: flex; align-items: center; gap: 0.4rem;">
                        <x-filament::icon icon="heroicon-m-check" style="width: 0.9rem; height: 0.9rem;" />
                        Create Offer
                    </span>
                </x-filament::button>
            </div>
        </div>

    </form>
</x-filament-panels::page>
