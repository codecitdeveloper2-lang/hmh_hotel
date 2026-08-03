<?php

namespace App\Filament\Pages\Hotels\Traits;

use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Hotels\Overview;
use App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes;
use App\Filament\Pages\Hotels\RoomTypes\CreateRoomType;
use App\Filament\Pages\Hotels\RoomTypes\EditRoomType;
use App\Filament\Pages\Hotels\RoomTypes\ViewRoomType;
use App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets;
use App\Filament\Pages\Hotels\DiningOutlets\CreateDiningOutlet;
use App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet;
use App\Filament\Pages\Hotels\DiningOutlets\ViewDiningOutlet;
use App\Filament\Pages\Hotels\Attractions\ListAttractions;
use App\Filament\Pages\Hotels\Attractions\CreateAttraction;
use App\Filament\Pages\Hotels\Attractions\EditAttraction;
use App\Filament\Pages\Hotels\Attractions\ViewAttraction;
use App\Filament\Pages\Hotels\Amenities\ListAmenities;
use App\Filament\Pages\Hotels\Amenities\CreateAmenity;
use App\Filament\Pages\Hotels\Amenities\EditAmenity;
use App\Filament\Pages\Hotels\Amenities\ViewAmenity;
use App\Filament\Pages\Hotels\FAQs\ListFaqs;
use App\Filament\Pages\Hotels\FAQs\CreateFaq;
use App\Filament\Pages\Hotels\FAQs\EditFaq;
use App\Filament\Pages\Hotels\FAQs\ViewFaq;

trait HasHotelTabs
{
    public $record;

    public function mountHasHotelTabs($record): void
    {
        $this->record = $record;
    }

    public function getSubNavigation(): array
    {
        return [
            NavigationItem::make('Overview')
                ->icon('heroicon-o-information-circle')
                ->url(Overview::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(Overview::getRouteName())),

            NavigationItem::make('Room Types')
                ->icon('heroicon-o-home-modern')
                ->url(ListRoomTypes::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(
                    ListRoomTypes::getRouteName(),
                    CreateRoomType::getRouteName(),
                    EditRoomType::getRouteName(),
                    ViewRoomType::getRouteName()
                )),

            NavigationItem::make('Dining Outlets')
                ->icon('heroicon-o-cake')
                ->url(ListDiningOutlets::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(
                    ListDiningOutlets::getRouteName(),
                    CreateDiningOutlet::getRouteName(),
                    EditDiningOutlet::getRouteName(),
                    ViewDiningOutlet::getRouteName()
                )),

            NavigationItem::make('Attractions')
                ->icon('heroicon-o-map-pin')
                ->url(ListAttractions::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(
                    ListAttractions::getRouteName(),
                    CreateAttraction::getRouteName(),
                    EditAttraction::getRouteName(),
                    ViewAttraction::getRouteName()
                )),

            NavigationItem::make('Amenities')
                ->icon('heroicon-o-sparkles')
                ->url(ListAmenities::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(
                    ListAmenities::getRouteName(),
                    CreateAmenity::getRouteName(),
                    EditAmenity::getRouteName(),
                    ViewAmenity::getRouteName()
                )),

            NavigationItem::make('FAQs')
                ->icon('heroicon-o-question-mark-circle')
                ->url(ListFaqs::getUrl(['record' => $this->record]))
                ->isActiveWhen(fn () => request()->routeIs(
                    ListFaqs::getRouteName(),
                    CreateFaq::getRouteName(),
                    EditFaq::getRouteName(),
                    ViewFaq::getRouteName()
                )),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
