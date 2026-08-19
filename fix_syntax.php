<?php
$f = 'app/Filament/Pages/ManageDestinations.php';
$c = file_get_contents($f);
$c = str_replace("->extraInputAttributes(['dir' => 'rtl', 'style' => 'text-align: right;']),", "->extraInputAttributes(['dir' => 'rtl', 'style' => 'text-align: right;'])", $c);
file_put_contents($f, $c);

$f2 = 'app/Filament/Pages/ManageOffers.php';
$c2 = file_get_contents($f2);

// Fix duplicated ->action() block at line 159
$c2 = str_replace("                ->url(\App\Filament\Pages\Offers\CreateOffer::getUrl())
                ->action(function (array \$data) {
                
            ->url(\App\Filament\Pages\Offers\CreateOffer::getUrl())
            ->action(function (array \$data) {
                    \App\Models\Offer::create([", "            ->url(\App\Filament\Pages\Offers\CreateOffer::getUrl())
            ->action(function (array \$data) {
                    \App\Models\Offer::create([", $c2);

// Fix duplicated ->action() block at line 195
$c2 = str_replace("            ->url(fn(array \$arguments) => \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => \$arguments['id'] ?? 0]))
            ->action(function (array \$data) {
            ->fillForm(function (array \$arguments) {
                \$offer = \App\Models\Offer::find(\$arguments['id']);", "            ->url(fn(array \$arguments) => \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => \$arguments['id'] ?? 0]))
            ->action(function (array \$data, array \$arguments) {
                \$offer = \App\Models\Offer::find(\$arguments['id']);", $c2);

// Fix missing comma after hiddenLabel()
$c2 = str_replace("                            \Filament\Forms\Components\ToggleButtons::make('activeLocale')
                                ->hiddenLabel()
                                
                    \Filament\Schemas\Components\Tabs::make('Tabs')", "                            \Filament\Forms\Components\ToggleButtons::make('activeLocale')
                                ->hiddenLabel(),
                        ]),
                    \Filament\Schemas\Components\Tabs::make('Tabs')", $c2);

// Fix missing select at line 379
$c2 = str_replace("                        ])->disabled(fn(\Livewire\Component \$livewire) => \$livewire instanceof \App\Filament\Pages\Offers\ViewOffer),
                        
                                ->default('Active')
                                ->required(),", "                        ])->disabled(fn(\Livewire\Component \$livewire) => \$livewire instanceof \App\Filament\Pages\Offers\ViewOffer),
                            \Filament\Forms\Components\Select::make('status')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                ])
                                ->default('Active')
                                ->required(),", $c2);

file_put_contents($f2, $c2);
