<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use BackedEnum;
use App\Models\Setting;
use App\Http\Controllers\Api\SettingApiController;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-settings';

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 12;
    }

    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\Width | string | null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $defaults = SettingApiController::getDefaultSettings();
        $stored = Setting::getAll();
        $settings = array_merge($defaults, $stored);

        if (isset($settings['maintenance_mode'])) {
            $settings['maintenance_mode'] = filter_var($settings['maintenance_mode'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($settings['enable_search'])) {
            $settings['enable_search'] = filter_var($settings['enable_search'], FILTER_VALIDATE_BOOLEAN);
        }

        $this->form->fill($settings);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('General Settings')
                            ->icon('heroicon-m-cog')
                            ->schema([
                                TextInput::make('website_name')->label('Website Name')->required(),
                                TextInput::make('website_url')->label('Website URL')->url()->required(),
                                TextInput::make('admin_email')->label('Admin Email')->email()->required(),
                                Select::make('time_zone')
                                    ->label('Time Zone')
                                    ->options(['Asia/Dubai' => 'Asia/Dubai', 'UTC' => 'UTC'])
                                    ->required(),
                                Select::make('default_language')
                                    ->label('Default Language')
                                    ->options(['en' => 'English', 'ar' => 'Arabic'])
                                    ->required(),
                                Select::make('date_format')
                                    ->label('Date Format')
                                    ->options(['Y-m-d' => 'YYYY-MM-DD', 'd/m/Y' => 'DD/MM/YYYY'])
                                    ->required(),
                                Select::make('time_format')
                                    ->label('Time Format')
                                    ->options(['H:i' => '24-hour', 'h:i A' => '12-hour'])
                                    ->required(),
                            ])->columns(2),

                        Tabs\Tab::make('Company Information')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                TextInput::make('company_name')->label('Company Name')->required(),
                                TextInput::make('company_registration_number')->label('Company Registration Number'),
                                TextInput::make('company_phone')->label('Phone Number'),
                                TextInput::make('company_email')->label('Email Address')->email(),
                                Textarea::make('company_address')->label('Company Address')->columnSpanFull(),
                                TextInput::make('company_country')->label('Country'),
                                FileUpload::make('company_logo')
                                    ->label('Company Logo')
                                    ->image()
                                    ->disk('uploads')
                                    ->directory('')
                                    ->columnSpan(1),
                                FileUpload::make('footer_logo')
                                    ->label('Footer Logo')
                                    ->image()
                                    ->disk('uploads')
                                    ->directory('')
                                    ->columnSpan(1),
                                FileUpload::make('company_favicon')
                                    ->label('Company Favicon')
                                    ->image()
                                    ->disk('uploads')
                                    ->directory('')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tabs\Tab::make('Website Settings')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('homepage_title')->label('Homepage Title')->columnSpanFull(),
                                Textarea::make('homepage_meta_description')->label('Homepage Meta Description')->columnSpanFull(),
                                FileUpload::make('default_banner_image')->label('Default Banner Image')->image()->disk('uploads')->directory('')->columnSpanFull(),
                                Toggle::make('maintenance_mode')->label('Maintenance Mode'),
                                Toggle::make('enable_search')->label('Enable Search'),
                            ])->columns(2),

                        Tabs\Tab::make('Contact Information')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                Textarea::make('head_office_address')->label('Head Office Address')->columnSpanFull(),
                                TextInput::make('contact_phone_number')->label('Phone Number'),
                                TextInput::make('contact_email_address')->label('Email Address')->email(),
                                TextInput::make('customer_support_email')->label('Customer Support Email')->email(),
                                TextInput::make('google_maps_url')->label('Google Maps URL')->url()->columnSpanFull(),
                            ])->columns(2),

                        Tabs\Tab::make('Social Media')
                            ->icon('heroicon-m-share')
                            ->schema([
                                TextInput::make('facebook_url')->label('Facebook URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('instagram_url')->label('Instagram URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('linkedin_url')->label('LinkedIn URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('twitter_url')->label('X (Twitter) URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('youtube_url')->label('YouTube URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('threads_url')->label('Threads URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('tiktok_url')->label('TikTok URL')->url()->prefixIcon('heroicon-m-link'),
                                TextInput::make('google_map_url')->label('Google Map URL')->url()->prefixIcon('heroicon-m-link'),
                            ])->columns(2),

                        Tabs\Tab::make('Global Sections')
                            ->icon('heroicon-m-squares-2x2')
                            ->schema([
                                TextInput::make('our_brands_title')->label('Our Brands Title')->default('OUR BRANDS')->columnSpanFull(),
                                FileUpload::make('our_brands_background')->label('Our Brands Background Image')->image()->disk('uploads')->directory('')->columnSpanFull(),
                            ])->columns(2),

                        Tabs\Tab::make('SEO Settings')
                            ->icon('heroicon-m-magnifying-glass')
                            ->schema([
                                TextInput::make('default_meta_title')->label('Default Meta Title')->columnSpanFull(),
                                Textarea::make('default_meta_description')->label('Default Meta Description')->columnSpanFull(),
                                TextInput::make('default_meta_keywords')->label('Default Meta Keywords')->columnSpanFull(),
                                Select::make('robots_meta_tag')
                                    ->label('Robots Meta Tag')
                                    ->options(['index, follow' => 'index, follow', 'noindex, nofollow' => 'noindex, nofollow'])
                                    ->default('index, follow'),
                                TextInput::make('google_analytics_id')->label('Google Analytics ID')->placeholder('UA-XXXXX-Y or G-XXXXXXX'),
                                TextInput::make('google_tag_manager_id')->label('Google Tag Manager ID')->placeholder('GTM-XXXXXX'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function getTitle(): string | Htmlable
    {
        return 'Settings';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage the global configuration of the HMH Hotel Group website and CMS.';
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_array($value)) {
                $value = json_encode($value);
            }
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully.')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        Setting::truncate();
        $this->form->fill(SettingApiController::getDefaultSettings());

        Notification::make()
            ->title('Settings reset to defaults.')
            ->info()
            ->send();
    }

    protected function getMockSettings(): array
    {
        return SettingApiController::getDefaultSettings();
    }
}
