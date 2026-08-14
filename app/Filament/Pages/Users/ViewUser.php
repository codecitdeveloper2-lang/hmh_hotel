<?php
namespace App\Filament\Pages\Users;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewUser extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-users/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $user = \App\Models\User::findOrFail($this->record);
        $this->form->fill([
            'full_name'     => $user->name,
            'email'         => $user->email,
            'employee_id'   => 'EMP-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
            'status'        => $user->is_active ? 'Active' : 'Inactive',
            'created_date'  => $user->created_at?->format('Y-m-d'),
            'last_login'    => $user->updated_at?->format('Y-m-d H:i'),
        ]);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageUsers::getAddUserFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageUsers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
