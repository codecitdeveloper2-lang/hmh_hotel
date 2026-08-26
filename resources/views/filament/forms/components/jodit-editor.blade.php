<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $state = $getState();
        $stateString = is_array($state) ? json_encode($state) : $state;
    @endphp
    @if ($isDisabled())
        <div style="padding: 1rem; border-radius: 0.5rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); color: inherit; min-height: 4rem;">
            {!! $stateString !!}
        </div>
    @else
        <div
            wire:ignore
            x-data="{
                init() {
                    // Ensure Jodit CSS is loaded
                    if (!document.getElementById('jodit-css')) {
                        let link = document.createElement('link');
                        link.id = 'jodit-css';
                        link.rel = 'stylesheet';
                        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.2/jodit.min.css';
                        document.head.appendChild(link);
                    }

                    // Ensure Jodit JS is loaded
                    if (typeof Jodit === 'undefined') {
                        let scriptId = 'jodit-js';
                        let existingScript = document.getElementById(scriptId);
                        
                        if (!existingScript) {
                            let script = document.createElement('script');
                            script.id = scriptId;
                            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.2/jodit.min.js';
                            script.onload = () => {
                                window.dispatchEvent(new Event('jodit-loaded'));
                            };
                            document.head.appendChild(script);
                        }
                        
                        window.addEventListener('jodit-loaded', () => {
                            this.initEditor();
                        }, { once: true });
                    } else {
                        this.initEditor();
                    }
                },
                initEditor() {
                    const editor = Jodit.make(this.$refs.editor, {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                        height: 400,
                        direction: '{{ $getDirection() }}' || '',
                    });
                    
                    editor.events.on('change', () => {
                        this.$refs.editor.value = editor.value;
                        this.$refs.editor.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                }
            }"
        >
            <textarea x-ref="editor" wire:model="{{ $getStatePath() }}">{!! $stateString !!}</textarea>
        </div>
    @endif
</x-dynamic-component>
