<x-filament-panels::page>
    @php
        $log = $this->getLogData();
    @endphp

    @if(!$log)
        <x-filament::section>
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                Log record not found.
            </div>
            <div class="mt-4 text-center">
                <x-filament::button tag="a" href="{{ $this->getBackUrl() }}" color="gray">
                    Back to Logs
                </x-filament::button>
            </div>
        </x-filament::section>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            {{-- Action buttons --}}
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <x-filament::button tag="a" href="{{ $this->getBackUrl() }}" color="gray" icon="heroicon-m-arrow-left">
                    Back to Logs
                </x-filament::button>
            </div>

            {{-- Main Info Section --}}
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                
                {{-- Left Column: User & Event Info --}}
                <div style="flex: 2; min-width: 300px; display: flex; flex-direction: column; gap: 1.5rem;">
                    <x-filament::section>
                        <x-slot name="heading">Event Information</x-slot>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 1rem;">
                            <div>
                                <span class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Module</span>
                                <div class="text-base text-gray-950 dark:text-white font-medium">{{ $log['module'] }}</div>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Action Performed</span>
                                <div class="text-base font-medium">
                                    <span class="@if($log['action'] == 'Created') text-success-600 dark:text-success-400 
                                                @elseif($log['action'] == 'Updated') text-info-600 dark:text-info-400 
                                                @elseif($log['action'] == 'Deleted' || str_contains($log['action'], 'Failed')) text-danger-600 dark:text-danger-400 
                                                @else text-gray-950 dark:text-white @endif">
                                        {{ $log['action'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <span class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Record Name</span>
                                <div class="text-base text-gray-950 dark:text-white">{{ $log['record_name'] }}</div>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Values Changed --}}
                    <x-filament::section>
                        <x-slot name="heading">Changes Details</x-slot>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 1rem;">
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                                <span class="block text-sm font-medium text-danger-600 dark:text-danger-400 mb-2">Old Value</span>
                                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">{{ $log['old_value'] }}</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                                <span class="block text-sm font-medium text-success-600 dark:text-success-400 mb-2">New Value</span>
                                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">{{ $log['new_value'] }}</div>
                            </div>
                        </div>
                    </x-filament::section>
                </div>
                
                {{-- Right Column: User & System Info --}}
                <div style="flex: 1; min-width: 250px; display: flex; flex-direction: column; gap: 1.5rem;">
                    <x-filament::section>
                        <x-slot name="heading">User Details</x-slot>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="height: 2.5rem; width: 2.5rem; border-radius: 9999px; background-color: rgba(99,102,241,0.1); display: flex; align-items: center; justify-content: center; color: #6366f1; font-weight: bold;">
                                    {{ substr($log['user_name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-base font-medium text-gray-950 dark:text-white">{{ $log['user_name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $log['role'] }}</div>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    <x-filament::section>
                        <x-slot name="heading">System Info</x-slot>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
                            <div>
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date & Time</span>
                                <div style="display: flex; align-items: center; gap: 0.5rem;" class="text-sm text-gray-950 dark:text-white">
                                    <x-filament::icon icon="heroicon-o-clock" style="height: 1rem; width: 1rem; color: #9ca3af;" />
                                    {{ $log['date_time'] }}
                                </div>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</span>
                                <div class="mt-1">
                                    @if($log['status'] === 'Success')
                                        <x-filament::badge color="success">Success</x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">Failed</x-filament::badge>
                                    @endif
                                </div>
                            </div>
                            <div style="padding-top: 0.75rem; border-top: 1px solid rgba(128,128,128,0.2);">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">IP Address</span>
                                <div style="display: flex; align-items: center; gap: 0.5rem;" class="text-sm text-gray-950 dark:text-white">
                                    <x-filament::icon icon="heroicon-o-globe-alt" style="height: 1rem; width: 1rem; color: #9ca3af;" />
                                    {{ $log['ip_address'] }}
                                </div>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Browser</span>
                                <div class="text-sm text-gray-950 dark:text-white">{{ $log['browser'] }}</div>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Device</span>
                                <div class="text-sm text-gray-950 dark:text-white">{{ $log['device'] }}</div>
                            </div>
                        </div>
                    </x-filament::section>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
