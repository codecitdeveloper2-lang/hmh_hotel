<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem; width: 100%;">
    <div style="display: inline-flex; border-radius: 0.5rem; padding: 0.25rem; background-color: rgba(156, 163, 175, 0.1); border: 1px solid rgba(156, 163, 175, 0.2);">
        <button
            type="button"
            wire:click="$set('activeLocale', 'en')"
            style="padding: 0.375rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: 0.375rem; transition: all 0.2s; cursor: pointer; border: none; {{ $this->activeLocale === 'en' ? 'background-color: var(--primary-600, #4f46e5); color: white; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);' : 'background-color: transparent; color: inherit; opacity: 0.7;' }}"
        >
            EN
        </button>
        <button
            type="button"
            wire:click="$set('activeLocale', 'ar')"
            style="padding: 0.375rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: 0.375rem; transition: all 0.2s; cursor: pointer; border: none; {{ $this->activeLocale === 'ar' ? 'background-color: var(--primary-600, #4f46e5); color: white; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);' : 'background-color: transparent; color: inherit; opacity: 0.7;' }}"
        >
            عربي
        </button>
    </div>
</div>
