<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-filament::icon icon="heroicon-m-bolt" class="text-primary-500" style="width: 1.25rem; height: 1.25rem; color: #6366f1;" />
                <span>Quick Actions</span>
            </div>
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            <!-- Brand -->
            <a href="#" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid rgba(128,128,128,0.2); background-color: rgba(99, 102, 241, 0.05); text-decoration: none; transition: all 0.2s ease-in-out;" onmouseover="this.style.backgroundColor='rgba(99, 102, 241, 0.1)'; this.style.borderColor='rgba(99, 102, 241, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='rgba(99, 102, 241, 0.05)'; this.style.borderColor='rgba(128,128,128,0.2)'; this.style.transform='translateY(0)';">
                <div style="padding: 0.75rem; border-radius: 9999px; background-color: rgba(99, 102, 241, 0.15); color: #6366f1; margin-bottom: 1rem;">
                    <x-filament::icon icon="heroicon-o-briefcase" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <h4 style="font-weight: 600; color: inherit; font-size: 1rem; margin-bottom: 0.25rem;">Add Brand</h4>
                <p style="font-size: 0.75rem; opacity: 0.7; text-align: center; margin: 0;">Register a new hotel brand</p>
            </a>

            <!-- Hotel -->
            <a href="#" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid rgba(128,128,128,0.2); background-color: rgba(16, 185, 129, 0.05); text-decoration: none; transition: all 0.2s ease-in-out;" onmouseover="this.style.backgroundColor='rgba(16, 185, 129, 0.1)'; this.style.borderColor='rgba(16, 185, 129, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='rgba(16, 185, 129, 0.05)'; this.style.borderColor='rgba(128,128,128,0.2)'; this.style.transform='translateY(0)';">
                <div style="padding: 0.75rem; border-radius: 9999px; background-color: rgba(16, 185, 129, 0.15); color: #10b981; margin-bottom: 1rem;">
                    <x-filament::icon icon="heroicon-o-building-office-2" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <h4 style="font-weight: 600; color: inherit; font-size: 1rem; margin-bottom: 0.25rem;">Add Hotel</h4>
                <p style="font-size: 0.75rem; opacity: 0.7; text-align: center; margin: 0;">Create a new property profile</p>
            </a>

            <!-- Offer -->
            <a href="#" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid rgba(128,128,128,0.2); background-color: rgba(245, 158, 11, 0.05); text-decoration: none; transition: all 0.2s ease-in-out;" onmouseover="this.style.backgroundColor='rgba(245, 158, 11, 0.1)'; this.style.borderColor='rgba(245, 158, 11, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='rgba(245, 158, 11, 0.05)'; this.style.borderColor='rgba(128,128,128,0.2)'; this.style.transform='translateY(0)';">
                <div style="padding: 0.75rem; border-radius: 9999px; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; margin-bottom: 1rem;">
                    <x-filament::icon icon="heroicon-o-ticket" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <h4 style="font-weight: 600; color: inherit; font-size: 1rem; margin-bottom: 0.25rem;">Create Offer</h4>
                <p style="font-size: 0.75rem; opacity: 0.7; text-align: center; margin: 0;">Launch a new promotion</p>
            </a>

            <!-- News -->
            <a href="#" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid rgba(128,128,128,0.2); background-color: rgba(236, 72, 153, 0.05); text-decoration: none; transition: all 0.2s ease-in-out;" onmouseover="this.style.backgroundColor='rgba(236, 72, 153, 0.1)'; this.style.borderColor='rgba(236, 72, 153, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='rgba(236, 72, 153, 0.05)'; this.style.borderColor='rgba(128,128,128,0.2)'; this.style.transform='translateY(0)';">
                <div style="padding: 0.75rem; border-radius: 9999px; background-color: rgba(236, 72, 153, 0.15); color: #ec4899; margin-bottom: 1rem;">
                    <x-filament::icon icon="heroicon-o-newspaper" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <h4 style="font-weight: 600; color: inherit; font-size: 1rem; margin-bottom: 0.25rem;">Publish News</h4>
                <p style="font-size: 0.75rem; opacity: 0.7; text-align: center; margin: 0;">Draft a new press release</p>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
