<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($isDisabled())
        <div style="padding: 1rem; border-radius: 0.5rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); color: inherit; min-height: 4rem;">
            {!! $getState() !!}
        </div>
    @else
        <div
            x-data="{
                state: $wire.$entangle('{{ $getStatePath() }}'),
                editor: null,
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

                    $watch('state', value => {
                        if (this.editor && this.editor.value !== value) {
                            this.editor.value = value || '';
                        }
                    });
                },
                initEditor() {
                    this.editor = Jodit.make(this.$refs.editor, {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                        height: 400,
                    });
                    
                    this.editor.value = this.state || '';
                    
                    this.editor.events.on('change', () => {
                        this.state = this.editor.value;
                    });
                }
            }"
        >
            <div wire:ignore>
                <textarea x-ref="editor"></textarea>
            </div>
        </div>
    @endif
</x-dynamic-component>
