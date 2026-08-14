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

    protected function getViewData(): array
    {
        $query = \App\Models\NewsletterSubscriber::query();
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $subscribers = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'subscriber_id' => 'NL-' . $sub->id,
                                'email' => $sub->email,
                                'full_name' => 'Unknown',
                                'country' => 'Unknown',
                                'subscription_source' => 'Website',
                                'subscription_date' => $sub->subscribed_at,
                                'status' => $sub->is_active ? 'Subscribed' : 'Unsubscribed',
                                'last_activity' => 'N/A',
                                'last_email_opened' => 'N/A',
                                'last_campaign' => 'N/A',
                                'last_updated' => $sub->subscribed_at,
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'subscribers', 'from', 'to');
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
            ->fillForm(function (array $arguments) {
                $sub = \App\Models\NewsletterSubscriber::find($arguments['id']);
                if (!$sub) return [];
                return [
                    'email' => $sub->email,
                    'status' => $sub->is_active ? 'Subscribed' : 'Unsubscribed',
                    'subscription_date' => $sub->subscribed_at,
                ];
            })
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
            ->action(function (array $arguments) {
                \App\Models\NewsletterSubscriber::find($arguments['id'])?->update(['is_active' => 0]);
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
            ->action(function (array $arguments) {
                \App\Models\NewsletterSubscriber::find($arguments['id'])?->update(['is_active' => 1]);
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
            ->action(function (array $arguments) {
                \App\Models\NewsletterSubscriber::find($arguments['id'])?->delete();
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

    // Mock Data removed
}
