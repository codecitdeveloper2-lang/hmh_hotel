<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageGroupGallery extends Page
{
    protected string $view = 'filament.pages.manage-group-gallery';

    public $searchQuery = '';
    public $filterCategory = '';
    public $filterDisplayLocation = '';
    public $filterStatus = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterCategory(): void { $this->currentPage = 1; }
    public function updatedFilterDisplayLocation(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
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
        return 'Content Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-photo';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Group Gallery';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Group Gallery';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Group Gallery';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage corporate images, banners and gallery content used across the HMH Hotel Group website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addGalleryItem')
                ->label('Add Gallery Item')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getGalleryItemFormSchema())
                
            ->url(\App\Filament\Pages\GroupGallery\CreateGroupGallery::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Gallery item saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewGalleryItemAction(): Action
    {
        return Action::make('viewGalleryItem')
            ->modalHeading('View Gallery Item')
            ->modalWidth('7xl')
            ->form($this->getGalleryItemFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockGalleryItems()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\GroupGallery\ViewGroupGallery::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editGalleryItemAction(): Action
    {
        return Action::make('editGalleryItem')
            ->modalHeading('Edit Gallery Item')
            ->modalWidth('7xl')
            ->form($this->getGalleryItemFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockGalleryItems()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\GroupGallery\EditGroupGallery::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Gallery item saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteGalleryItemAction(): Action
    {
        return Action::make('deleteGalleryItem')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Gallery item deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getGalleryItemFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('title')
                                    ->label('Gallery Title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required(),
                            ]),
                            Grid::make(2)->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'Homepage' => 'Homepage',
                                        'Corporate' => 'Corporate',
                                        'Brands' => 'Brands',
                                        'Events' => 'Events',
                                        'CSR' => 'CSR',
                                        'Awards' => 'Awards',
                                        'Leadership' => 'Leadership',
                                        'Media' => 'Media',
                                        'Destinations' => 'Destinations',
                                        'Sustainability' => 'Sustainability',
                                    ])
                                    ->required(),
                                TextInput::make('display_location')
                                    ->label('Display Location')
                                    ->required(),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('display_order')
                                    ->label('Display Order')
                                    ->numeric(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Published' => 'Published',
                                        'Draft' => 'Draft',
                                    ])
                                    ->default('Published')
                                    ->required(),
                            ]),
                        ]),

                    Section::make('Content')
                        ->schema([
                            Textarea::make('short_description')
                                ->label('Short Description')
                                ->rows(3),
                            RichEditor::make('detailed_description')
                                ->label('Detailed Description'),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('featured_image')
                                ->label('Featured Image Upload')
                                ->image(),
                            FileUpload::make('gallery_images')
                                ->label('Multiple Gallery Images Upload')
                                ->image()
                                ->multiple(),
                            TextInput::make('image_alt_text')
                                ->label('Image Alt Text'),
                        ]),
                        
                    Section::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                            TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockGalleryItems(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'Homepage Hero Banner', 'category' => 'Homepage', 'slug' => 'homepage-hero-banner', 'display_location' => 'Main Header', 'status' => 'Published', 'display_order' => 1, 'last_updated' => '2023-11-01'],
            2 => ['id' => 2, 'title' => 'About Us Gallery', 'category' => 'Corporate', 'slug' => 'about-us-gallery', 'display_location' => 'About Page Content', 'status' => 'Published', 'display_order' => 2, 'last_updated' => '2023-10-15'],
            3 => ['id' => 3, 'title' => 'Corporate Events', 'category' => 'Events', 'slug' => 'corporate-events', 'display_location' => 'Events Section', 'status' => 'Published', 'display_order' => 3, 'last_updated' => '2023-10-05'],
            4 => ['id' => 4, 'title' => 'Brand Campaign', 'category' => 'Brands', 'slug' => 'brand-campaign', 'display_location' => 'Brands Page Slider', 'status' => 'Published', 'display_order' => 4, 'last_updated' => '2023-09-28'],
            5 => ['id' => 5, 'title' => 'CSR Activities', 'category' => 'CSR', 'slug' => 'csr-activities', 'display_location' => 'CSR Section', 'status' => 'Published', 'display_order' => 5, 'last_updated' => '2023-09-20'],
            6 => ['id' => 6, 'title' => 'Awards & Recognition', 'category' => 'Awards', 'slug' => 'awards-recognition', 'display_location' => 'Awards Page Grid', 'status' => 'Published', 'display_order' => 6, 'last_updated' => '2023-09-15'],
            7 => ['id' => 7, 'title' => 'Leadership Team', 'category' => 'Leadership', 'slug' => 'leadership-team', 'display_location' => 'Leadership Page Grid', 'status' => 'Published', 'display_order' => 7, 'last_updated' => '2023-09-10'],
            8 => ['id' => 8, 'title' => 'Media Coverage', 'category' => 'Media', 'slug' => 'media-coverage', 'display_location' => 'Media Center', 'status' => 'Published', 'display_order' => 8, 'last_updated' => '2023-11-05'],
            9 => ['id' => 9, 'title' => 'Destination Highlights', 'category' => 'Destinations', 'slug' => 'destination-highlights', 'display_location' => 'Destinations Page Slider', 'status' => 'Draft', 'display_order' => 9, 'last_updated' => '2023-11-06'],
            10 => ['id' => 10, 'title' => 'Sustainability Initiatives', 'category' => 'Sustainability', 'slug' => 'sustainability-initiatives', 'display_location' => 'Sustainability Section', 'status' => 'Draft', 'display_order' => 10, 'last_updated' => '2023-11-06'],
        ];
    }
}
