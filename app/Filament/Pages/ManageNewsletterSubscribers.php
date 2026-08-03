<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use BackedEnum;

class ManageNewsletterSubscribers extends Page
{
    protected string $view = 'filament.pages.manage-newsletter-subscribers';

    public $searchQuery = '';
    public $filterStatus = '';
    public $filterCountry = '';
    public $filterSource = '';
    public $filterDate = '';
    public $selectedRows = [];

    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterCountry(): void { $this->currentPage = 1; }
    public function updatedFilterSource(): void { $this->currentPage = 1; }
    public function updatedFilterDate(): void { $this->currentPage = 1; }
    public function updatedPerPage(): void { $this->currentPage = 1; }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Operations';
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-at-symbol';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Newsletter Subscribers';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Newsletter Subscribers';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Newsletter Subscribers';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage newsletter subscribers and marketing communication preferences.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    Notification::make()
                        ->title('Subscriber exported successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewSubscriberAction(): Action
    {
        return Action::make('viewSubscriber')
            ->modalHeading('View Subscriber')
            ->modalWidth('7xl')
            ->form($this->getSubscriberFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockSubscribers()[$arguments['id']] ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function unsubscribeAction(): Action
    {
        return Action::make('unsubscribe')
            ->icon('heroicon-m-no-symbol')
            ->color('warning')
            
            ->url(fn (array $arguments) => \App\Filament\Pages\NewsletterSubscribers\ViewNewsletterSubscriber::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function () {
                Notification::make()
                    ->title('Subscriber unsubscribed successfully.')
                    ->success()
                    ->send();
            });
    }

    public function reactivateAction(): Action
    {
        return Action::make('reactivate')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->action(function () {
                Notification::make()
                    ->title('Subscriber reactivated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function exportSubscriberAction(): Action
    {
        return Action::make('exportSubscriber')
            ->icon('heroicon-m-document-arrow-down')
            ->action(function () {
                Notification::make()
                    ->title('Subscriber exported successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Subscriber deleted.')
                    ->success()
                    ->send();
            });
    }

    public function bulkAction(string $action): void
    {
        if (empty($this->selectedRows)) {
            Notification::make()->title('Please select at least one record.')->warning()->send();
            return;
        }

        $message = match ($action) {
            'export' => 'Subscriber exported successfully.',
            'unsubscribe' => 'Subscriber unsubscribed successfully.',
            'reactivate' => 'Subscriber reactivated successfully.',
            'delete' => 'Subscribers deleted.',
            default => 'Action completed.'
        };

        Notification::make()->title($message)->success()->send();
        $this->selectedRows = [];
    }

    public static function getSubscriberFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Subscriber Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('full_name')->label('Full Name'),
                                TextInput::make('email')->label('Email Address'),
                                TextInput::make('country')->label('Country'),
                                TextInput::make('subscription_source')->label('Subscription Source'),
                                TextInput::make('subscription_date')->label('Subscription Date'),
                                TextInput::make('status')->label('Current Status'),
                            ]),
                        ]),
                        
                    Section::make('Activity')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('last_email_opened')->label('Last Email Opened'),
                                TextInput::make('last_campaign')->label('Last Campaign Received'),
                                TextInput::make('last_updated')->label('Last Updated'),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Marketing Preferences')
                        ->schema([
                            Toggle::make('pref_promotions')->label('Promotions')->default(true),
                            Toggle::make('pref_hotel_offers')->label('Hotel Offers')->default(true),
                            Toggle::make('pref_brand_news')->label('Brand News')->default(true),
                            Toggle::make('pref_events')->label('Events')->default(true),
                            Toggle::make('pref_product_updates')->label('Product Updates')->default(false),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockSubscribers(): array
    {
        return [
            1 => ['id' => 1, 'subscriber_id' => 'NL-8001', 'full_name' => 'Michael Chen', 'email' => 'm.chen@example.com', 'country' => 'Singapore', 'subscription_source' => 'Homepage', 'subscription_date' => '2023-11-15', 'status' => 'Subscribed', 'last_activity' => '2 days ago', 'last_email_opened' => '2023-11-14 10:20', 'last_campaign' => 'Winter Promo 2023', 'last_updated' => '2023-11-15 09:30', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => true, 'pref_events' => false, 'pref_product_updates' => false],
            2 => ['id' => 2, 'subscriber_id' => 'NL-8002', 'full_name' => 'Emma Davis', 'email' => 'emma.davis@example.co.uk', 'country' => 'United Kingdom', 'subscription_source' => 'Footer', 'subscription_date' => '2023-11-14', 'status' => 'Subscribed', 'last_activity' => '1 week ago', 'last_email_opened' => '2023-11-10 14:15', 'last_campaign' => 'Weekend Getaways', 'last_updated' => '2023-11-14 14:15', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => true, 'pref_product_updates' => false],
            3 => ['id' => 3, 'subscriber_id' => 'NL-8003', 'full_name' => 'Ahmed Al Sayed', 'email' => 'a.alsayed@example.ae', 'country' => 'UAE', 'subscription_source' => 'Offers Page', 'subscription_date' => '2023-11-12', 'status' => 'Unsubscribed', 'last_activity' => '2 weeks ago', 'last_email_opened' => '2023-10-25 09:05', 'last_campaign' => 'Summer Recap', 'last_updated' => '2023-11-12 10:05', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            4 => ['id' => 4, 'subscriber_id' => 'NL-8004', 'full_name' => 'Maria Garcia', 'email' => 'mgarcia@example.es', 'country' => 'Spain', 'subscription_source' => 'Membership Page', 'subscription_date' => '2023-11-10', 'status' => 'Subscribed', 'last_activity' => 'Just now', 'last_email_opened' => '2023-11-15 18:45', 'last_campaign' => 'Loyalty Rewards update', 'last_updated' => '2023-11-10 18:45', 'pref_promotions' => true, 'pref_hotel_offers' => false, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => true],
            5 => ['id' => 5, 'subscriber_id' => 'NL-8005', 'full_name' => 'David Wilson', 'email' => 'david.w@example.com', 'country' => 'USA', 'subscription_source' => 'Popup Campaign', 'subscription_date' => '2023-11-09', 'status' => 'Subscribed', 'last_activity' => '3 days ago', 'last_email_opened' => '2023-11-12 08:20', 'last_campaign' => 'Welcome Series 1', 'last_updated' => '2023-11-09 08:20', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => false],
            6 => ['id' => 6, 'subscriber_id' => 'NL-8006', 'full_name' => 'Fatima Noor', 'email' => 'f.noor@example.com', 'country' => 'Saudi Arabia', 'subscription_source' => 'Hotel Detail Page', 'subscription_date' => '2023-11-08', 'status' => 'Subscribed', 'last_activity' => '1 month ago', 'last_email_opened' => '2023-10-15 11:10', 'last_campaign' => 'Autumn Specials', 'last_updated' => '2023-11-08 11:10', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => true, 'pref_product_updates' => false],
            7 => ['id' => 7, 'subscriber_id' => 'NL-8007', 'full_name' => 'Thomas Muller', 'email' => 't.muller@example.de', 'country' => 'Germany', 'subscription_source' => 'Homepage', 'subscription_date' => '2023-11-05', 'status' => 'Unsubscribed', 'last_activity' => '5 days ago', 'last_email_opened' => '2023-11-10 09:15', 'last_campaign' => 'Corporate Updates', 'last_updated' => '2023-11-10 09:15', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            8 => ['id' => 8, 'subscriber_id' => 'NL-8008', 'full_name' => 'Elena Popova', 'email' => 'elena.p@example.ru', 'country' => 'Russia', 'subscription_source' => 'Footer', 'subscription_date' => '2023-11-04', 'status' => 'Subscribed', 'last_activity' => '2 days ago', 'last_email_opened' => '2023-11-13 16:30', 'last_campaign' => 'Winter Promo 2023', 'last_updated' => '2023-11-04 16:30', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => true, 'pref_events' => false, 'pref_product_updates' => false],
            9 => ['id' => 9, 'subscriber_id' => 'NL-8009', 'full_name' => 'James Clarke', 'email' => 'j.clarke@example.com', 'country' => 'Australia', 'subscription_source' => 'Offers Page', 'subscription_date' => '2023-11-03', 'status' => 'Subscribed', 'last_activity' => '1 day ago', 'last_email_opened' => '2023-11-14 10:45', 'last_campaign' => 'Weekend Getaways', 'last_updated' => '2023-11-03 10:45', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            10 => ['id' => 10, 'subscriber_id' => 'NL-8010', 'full_name' => 'Aisha Khan', 'email' => 'akhan@example.com', 'country' => 'Pakistan', 'subscription_source' => 'Membership Page', 'subscription_date' => '2023-11-01', 'status' => 'Subscribed', 'last_activity' => '1 week ago', 'last_email_opened' => '2023-11-08 13:20', 'last_campaign' => 'Loyalty Rewards update', 'last_updated' => '2023-11-01 13:20', 'pref_promotions' => false, 'pref_hotel_offers' => true, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => false],
            11 => ['id' => 11, 'subscriber_id' => 'NL-8011', 'full_name' => 'Robert Taylor', 'email' => 'robert.t@example.com', 'country' => 'USA', 'subscription_source' => 'Popup Campaign', 'subscription_date' => '2023-10-28', 'status' => 'Subscribed', 'last_activity' => '3 weeks ago', 'last_email_opened' => '2023-10-25 08:05', 'last_campaign' => 'Welcome Series 2', 'last_updated' => '2023-10-28 08:05', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            12 => ['id' => 12, 'subscriber_id' => 'NL-8012', 'full_name' => 'Sophie Martin', 'email' => 'smartin@example.fr', 'country' => 'France', 'subscription_source' => 'Hotel Detail Page', 'subscription_date' => '2023-10-25', 'status' => 'Subscribed', 'last_activity' => '4 days ago', 'last_email_opened' => '2023-11-11 19:30', 'last_campaign' => 'Winter Promo 2023', 'last_updated' => '2023-10-25 19:30', 'pref_promotions' => true, 'pref_hotel_offers' => false, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => false],
            13 => ['id' => 13, 'subscriber_id' => 'NL-8013', 'full_name' => 'Mohammed Ali', 'email' => 'm.ali@example.om', 'country' => 'Oman', 'subscription_source' => 'Homepage', 'subscription_date' => '2023-10-20', 'status' => 'Unsubscribed', 'last_activity' => '2 months ago', 'last_email_opened' => '2023-09-15 11:15', 'last_campaign' => 'Autumn Specials', 'last_updated' => '2023-10-20 11:15', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            14 => ['id' => 14, 'subscriber_id' => 'NL-8014', 'full_name' => 'Lisa Wong', 'email' => 'lisa.w@example.sg', 'country' => 'Singapore', 'subscription_source' => 'Footer', 'subscription_date' => '2023-10-18', 'status' => 'Subscribed', 'last_activity' => '2 weeks ago', 'last_email_opened' => '2023-11-01 14:40', 'last_campaign' => 'Brand News Digest', 'last_updated' => '2023-10-18 14:40', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => true, 'pref_events' => false, 'pref_product_updates' => true],
            15 => ['id' => 15, 'subscriber_id' => 'NL-8015', 'full_name' => 'Daniel Kim', 'email' => 'dkim@example.kr', 'country' => 'South Korea', 'subscription_source' => 'Offers Page', 'subscription_date' => '2023-10-15', 'status' => 'Subscribed', 'last_activity' => '1 day ago', 'last_email_opened' => '2023-11-14 07:25', 'last_campaign' => 'Winter Promo 2023', 'last_updated' => '2023-10-15 07:25', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            16 => ['id' => 16, 'subscriber_id' => 'NL-8016', 'full_name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com', 'country' => 'USA', 'subscription_source' => 'Membership Page', 'subscription_date' => '2023-10-10', 'status' => 'Subscribed', 'last_activity' => '3 days ago', 'last_email_opened' => '2023-11-12 10:00', 'last_campaign' => 'Loyalty Rewards update', 'last_updated' => '2023-10-10 10:00', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => false],
            17 => ['id' => 17, 'subscriber_id' => 'NL-8017', 'full_name' => 'Yusuf Ibrahim', 'email' => 'y.ibrahim@example.com', 'country' => 'Egypt', 'subscription_source' => 'Popup Campaign', 'subscription_date' => '2023-10-05', 'status' => 'Subscribed', 'last_activity' => '2 weeks ago', 'last_email_opened' => '2023-11-01 12:30', 'last_campaign' => 'Weekend Getaways', 'last_updated' => '2023-10-05 12:30', 'pref_promotions' => true, 'pref_hotel_offers' => true, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            18 => ['id' => 18, 'subscriber_id' => 'NL-8018', 'full_name' => 'Nina Patel', 'email' => 'nina.p@example.in', 'country' => 'India', 'subscription_source' => 'Hotel Detail Page', 'subscription_date' => '2023-10-01', 'status' => 'Unsubscribed', 'last_activity' => '1 month ago', 'last_email_opened' => '2023-10-10 09:45', 'last_campaign' => 'Corporate Updates', 'last_updated' => '2023-10-15 09:45', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => false, 'pref_events' => false, 'pref_product_updates' => false],
            19 => ['id' => 19, 'subscriber_id' => 'NL-8019', 'full_name' => 'Jean Dupont', 'email' => 'j.dupont@example.fr', 'country' => 'France', 'subscription_source' => 'Homepage', 'subscription_date' => '2023-09-28', 'status' => 'Subscribed', 'last_activity' => '5 days ago', 'last_email_opened' => '2023-11-10 15:20', 'last_campaign' => 'Winter Promo 2023', 'last_updated' => '2023-09-28 15:20', 'pref_promotions' => true, 'pref_hotel_offers' => false, 'pref_brand_news' => true, 'pref_events' => true, 'pref_product_updates' => false],
            20 => ['id' => 20, 'subscriber_id' => 'NL-8020', 'full_name' => 'Oliver Smith', 'email' => 'o.smith@example.co.uk', 'country' => 'United Kingdom', 'subscription_source' => 'Footer', 'subscription_date' => '2023-09-25', 'status' => 'Subscribed', 'last_activity' => '1 day ago', 'last_email_opened' => '2023-11-14 11:10', 'last_campaign' => 'Brand News Digest', 'last_updated' => '2023-09-25 11:10', 'pref_promotions' => false, 'pref_hotel_offers' => false, 'pref_brand_news' => true, 'pref_events' => false, 'pref_product_updates' => true],
        ];
    }
}
