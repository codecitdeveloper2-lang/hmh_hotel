<?php
namespace App\Filament\Pages\Users;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateUser extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-users/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageUsers::getAddUserFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        \App\Models\User::create([
            'name'      => $data['full_name'] ?? 'Unknown',
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
            'is_active' => ($data['status'] ?? 'Active') === 'Active',
        ]);

        \Filament\Notifications\Notification::make()->title('User created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageUsers::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageUsers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
