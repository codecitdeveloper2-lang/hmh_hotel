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

    protected function getViewData(): array
    {
        $query = \App\Models\GalleryItem::query();

        if ($this->searchQuery) {
            $query->where('caption', 'like', "%{$this->searchQuery}%");
        }

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $galleryItems = $query->orderBy('sort_order')
            ->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->caption ?? 'Untitled',
                    'category' => 'General',
                    'slug' => \Illuminate\Support\Str::slug($item->caption ?? 'item-' . $item->id),
                    'display_location' => 'Website',
                    'status' => 'Published',
                    'display_order' => $item->sort_order,
                    'last_updated' => $item->updated_at?->format('Y-m-d') ?? '',
                ];
            });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'galleryItems', 'from', 'to');
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
            ->fillForm(function (array $arguments) {
                $item = \App\Models\GalleryItem::find($arguments['id']);
                if (!$item) return [];
                return [
                    'title' => $item->caption,
                    'slug' => \Illuminate\Support\Str::slug($item->caption ?? ''),
                    'display_order' => $item->sort_order,
                    'status' => 'Published',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $item = \App\Models\GalleryItem::find($arguments['id']);
                if (!$item) return [];
                return [
                    'title' => $item->caption,
                    'slug' => \Illuminate\Support\Str::slug($item->caption ?? ''),
                    'display_order' => $item->sort_order,
                    'status' => 'Published',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\GroupGallery\EditGroupGallery::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                $item = \App\Models\GalleryItem::find($arguments['id']);
                if ($item) {
                    $item->caption = $data['title'] ?? $item->caption;
                    $item->sort_order = $data['display_order'] ?? $item->sort_order;
                    $item->save();
                }
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
            ->action(function (array $arguments) {
                \App\Models\GalleryItem::find($arguments['id'])?->delete();
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
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
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

    // Mock Data removed
}
