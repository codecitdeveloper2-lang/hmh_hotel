<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-m-clock" class="text-primary-500" style="width: 1.25rem; height: 1.25rem; color: #6366f1;" />
                    <span>Recent Activity Stream</span>
                </div>
                <x-filament::badge color="primary">Live</x-filament::badge>
            </div>
        </x-slot>

        <div style="display: flex; flex-direction: column;">
            @php
                $activities = [
                    ['user' => 'John Doe', 'action' => 'created a new reservation for', 'target' => 'Grand Hotel', 'time' => '10 minutes ago', 'icon' => 'heroicon-m-calendar-days', 'color' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.15)'],
                    ['user' => 'Jane Smith', 'action' => 'updated brand details for', 'target' => 'HMH Luxury', 'time' => '1 hour ago', 'icon' => 'heroicon-m-building-office', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.15)'],
                    ['user' => 'Admin', 'action' => 'published a new offer', 'target' => 'Summer Special', 'time' => '3 hours ago', 'icon' => 'heroicon-m-ticket', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.15)'],
                    ['user' => 'Michael Brown', 'action' => 'resolved contact enquiry from', 'target' => 'Alice Johnson', 'time' => '5 hours ago', 'icon' => 'heroicon-m-envelope-open', 'color' => '#64748b', 'bg' => 'rgba(100, 116, 139, 0.15)'],
                    ['user' => 'System', 'action' => 'sent newsletter to', 'target' => '3,580 subscribers', 'time' => '1 day ago', 'icon' => 'heroicon-m-paper-airplane', 'color' => '#0ea5e9', 'bg' => 'rgba(14, 165, 233, 0.15)'],
                ];
            @endphp

            @foreach($activities as $index => $activity)
                <div style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.25rem 0; {{ $index !== count($activities) - 1 ? 'border-bottom: 1px solid rgba(128, 128, 128, 0.15);' : '' }}">
                    <div style="background-color: {{ $activity['bg'] }}; border-radius: 50%; padding: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-filament::icon
                            icon="{{ $activity['icon'] }}"
                            style="width: 1.25rem; height: 1.25rem; color: {{ $activity['color'] }};"
                        />
                    </div>
                    <div style="flex: 1; font-size: 0.875rem;">
                        <div style="margin-bottom: 0.25rem;">
                            <strong style="font-weight: 600; color: inherit;">{{ $activity['user'] }}</strong>
                            <span style="opacity: 0.8; margin: 0 0.25rem;">{{ $activity['action'] }}</span>
                            <span style="font-weight: 600; color: inherit;">{{ $activity['target'] }}</span>
                        </div>
                        <div style="font-size: 0.75rem; opacity: 0.5; display: flex; align-items: center; gap: 0.25rem;">
                            <x-filament::icon icon="heroicon-m-clock" style="width: 0.75rem; height: 0.75rem;" />
                            {{ $activity['time'] }}
                        </div>
                    </div>
                    <div>
                        <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" size="sm" />
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
