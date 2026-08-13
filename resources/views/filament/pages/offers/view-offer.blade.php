<x-filament-panels::page>
    <style>
        .view-field-label {
            font-size: 0.8rem;
            font-weight: 600;
            opacity: 0.5;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.35rem;
        }
        .view-field-value {
            font-size: 1rem;
            font-weight: 500;
            color: #f3f4f6;
            min-height: 1.5rem;
        }
        .view-field-value.empty {
            opacity: 0.3;
            font-style: italic;
        }
        .view-section {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .view-section-title {
            font-size: 0.9rem;
            font-weight: 700;
            opacity: 0.7;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .locale-toggle {
            display: flex;
            gap: 0.25rem;
            background: rgba(0,0,0,0.2);
            border-radius: 0.5rem;
            padding: 0.2rem;
        }
        .locale-btn {
            padding: 0.35rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
            background: transparent;
            color: inherit;
            opacity: 0.5;
        }
        .locale-btn.active {
            background: #6366f1;
            opacity: 1;
            color: #fff;
            box-shadow: 0 2px 8px rgba(99,102,241,0.4);
        }
        .badge-active {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-inactive {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .banner-thumb {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid rgba(255,255,255,0.08);
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .banner-thumb:hover {
            opacity: 0.8;
        }
        
        /* Lightbox Styles */
        .lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .lightbox-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .lightbox-overlay.open .lightbox-content {
            transform: scale(1);
        }
        .lightbox-image {
            display: block;
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }
        .lightbox-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2.5rem;
            height: 2.5rem;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            z-index: 1001;
        }
        .lightbox-close:hover {
            background: rgba(0, 0, 0, 0.8);
        }
    </style>


    @php
        $o          = $this->offer;
        $locale     = $this->activeLocale;
        $isAr       = $locale === 'ar';
        $dir        = $isAr ? 'rtl' : 'ltr';
        $align      = $isAr ? 'right' : 'left';

        $emptyValue  = '—';
        $translation = function ($model, string $attribute) use ($locale): string {
            $value = $model->getTranslation($attribute, $locale, false);
            return filled($value) ? (string) $value : '';
        };

        $name        = $translation($o, 'name');
        $description = $translation($o, 'description');
    @endphp

    <div style="max-width: 100%;" x-data="{ isLightboxOpen: false, lightboxImage: '' }">

        {{-- Header Card --}}
        <div style="
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        ">
            {{-- Card Header --}}
            <div style="
                padding: 1.25rem 2rem;
                border-bottom: 1px solid rgba(255,255,255,0.07);
                background: rgba(16, 185, 129, 0.06);
                display: flex;
                align-items: center;
                gap: 0.875rem;
            ">
                <div style="width:2.5rem;height:2.5rem;border-radius:0.6rem;background:rgba(16,185,129,0.2);display:flex;align-items:center;justify-content:center;">
                    <x-filament::icon icon="heroicon-m-eye" style="width:1.25rem;height:1.25rem;color:#34d399;" />
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:600;">Record Details</div>
                    <div style="font-size:0.75rem;opacity:0.45;margin-top:0.15rem;">This record is in read-only mode</div>
                </div>
                <div style="margin-left:auto;">
                    <x-filament::badge color="success">Read Only</x-filament::badge>
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding: 2rem;" dir="{{ $dir }}">

                {{-- Two-column layout --}}
                <div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; align-items: start;">

                    {{-- LEFT: Basic Information --}}
                    <div>
                        {{-- Section header with locale toggle --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-direction: {{ $isAr ? 'row-reverse' : 'row' }};">
                            <div style="font-size: 1rem; font-weight: 700;">Basic Information</div>
                            <div class="locale-toggle" dir="ltr">
                                <button
                                    type="button"
                                    class="locale-btn {{ $locale === 'en' ? 'active' : '' }}"
                                    wire:click="switchLocale('en')"
                                >EN</button>
                                <button
                                    type="button"
                                    class="locale-btn {{ $locale === 'ar' ? 'active' : '' }}"
                                    wire:click="switchLocale('ar')"
                                >عربي</button>
                            </div>
                        </div>

                        {{-- Offer Name --}}
                        <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                            <div class="view-field-label">{{ $isAr ? 'اسم العرض' : 'Offer Title' }}</div>
                            <div class="view-field-value {{ blank($name) ? 'empty' : '' }}">{{ filled($name) ? $name : $emptyValue }}</div>
                        </div>

                        {{-- Hotel & Type --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                                <div class="view-field-label">{{ $isAr ? 'الفندق' : 'Hotel' }}</div>
                                <div class="view-field-value">{{ $o->hotel ?: $emptyValue }}</div>
                            </div>
                            <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                                <div class="view-field-label">{{ $isAr ? 'نوع العرض' : 'Offer Type' }}</div>
                                <div class="view-field-value">{{ $o->offer_type ?: $emptyValue }}</div>
                            </div>
                        </div>

                        {{-- Date & Duration --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                                <div class="view-field-label">{{ $isAr ? 'صالح من' : 'Valid From' }}</div>
                                <div class="view-field-value">{{ $o->valid_from ? $o->valid_from->format('Y-m-d') : $emptyValue }}</div>
                            </div>
                            <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                                <div class="view-field-label">{{ $isAr ? 'صالح حتى' : 'Valid Until' }}</div>
                                <div class="view-field-value">{{ $o->valid_to ? $o->valid_to->format('Y-m-d') : $emptyValue }}</div>
                            </div>
                            <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                                <div class="view-field-label">{{ $isAr ? 'فترة الحجز' : 'Booking Period' }}</div>
                                <div class="view-field-value">{{ $o->booking_period ?: $emptyValue }}</div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                            <div class="view-field-label">{{ $isAr ? 'وصف قصير' : 'Short Description' }}</div>
                            <div class="view-field-value" style="line-height:1.6;">
                                {!! filled($description) ? $description : '<span class="empty">' . $emptyValue . '</span>' !!}
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="view-section" dir="{{ $dir }}" style="text-align: {{ $align }};">
                            <div class="view-field-label">{{ $isAr ? 'الحالة' : 'Status' }}</div>
                            <div class="view-field-value">
                                @if($o->status === 'Active')
                                    <span class="badge-active">{{ $isAr ? 'نشط' : 'Active' }}</span>
                                @elseif($o->status === 'Draft')
                                    <span class="badge-inactive" style="color: #9ca3af; background: rgba(156, 163, 175, 0.15);">{{ $isAr ? 'مسودة' : 'Draft' }}</span>
                                @else
                                    <span class="badge-inactive">{{ $isAr ? 'منتهي' : $o->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Media + SEO --}}
                    <div>
                        {{-- Media Section --}}
                        <div class="view-section">
                            <div class="view-section-title">{{ $isAr ? 'الوسائط' : 'Media' }}</div>
                            <div class="view-field-label" style="margin-bottom: 0.75rem;">
                                {{ $isAr ? 'صورة البانر' : 'Card Image' }}
                            </div>
                            @if($o->banner_image)
                                <img src="{{ asset('storage/' . $o->banner_image) }}" alt="Banner" class="banner-thumb" @click="lightboxImage = '{{ asset('storage/' . $o->banner_image) }}'; isLightboxOpen = true" />
                            @else
                                <div style="height:120px; background:rgba(128,128,128,0.08); border-radius:0.5rem; display:flex; align-items:center; justify-content:center; opacity:0.4;">
                                    <x-filament::icon icon="heroicon-o-photo" style="width:2rem;height:2rem;" />
                                </div>
                            @endif
                        </div>

                        {{-- SEO Section --}}
                        <div class="view-section">
                            <div class="view-section-title">SEO</div>
                            <div style="margin-bottom: 1rem;">
                                <div class="view-field-label">Meta Title</div>
                                <div class="view-field-value {{ !$o->meta_title ? 'empty' : '' }}">
                                    {{ $o->meta_title ?: '—' }}
                                </div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <div class="view-field-label">Meta Description</div>
                                <div class="view-field-value {{ !$o->meta_description ? 'empty' : '' }}">
                                    {{ $o->meta_description ?: '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="view-field-label">Meta Keywords</div>
                                <div class="view-field-value {{ !$o->meta_keywords ? 'empty' : '' }}">
                                    {{ $o->meta_keywords ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <x-filament::icon icon="heroicon-m-eye" style="width:1.1rem;height:1.1rem;opacity:0.35;" />
                <span style="font-size:0.78rem;opacity:0.45;">You are viewing this record in read-only mode.</span>
            </div>
            <x-filament::button color="gray" tag="a" :href="$this->getBackUrl()">
                <span style="display:flex;align-items:center;gap:0.4rem;">
                    <x-filament::icon icon="heroicon-m-arrow-left" style="width:0.9rem;height:0.9rem;" />
                    Back to List
                </span>
            </x-filament::button>
        </div>

    {{-- Lightbox Modal --}}
    <div class="lightbox-overlay" :class="{ 'open': isLightboxOpen }" @click.self="isLightboxOpen = false" x-cloak>
        <button type="button" class="lightbox-close" @click="isLightboxOpen = false">
            <x-filament::icon icon="heroicon-m-x-mark" style="width: 1.5rem; height: 1.5rem;" />
        </button>
        <div class="lightbox-content">
            <img :src="lightboxImage" alt="Enlarged Image" class="lightbox-image" />
        </div>
    </div>

    </div>
</x-filament-panels::page>
