<?php
namespace App\Filament\Pages\Users;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditUser extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-users/{record}/edit';
    

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
        ]);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageUsers::getAddUserFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = \App\Models\User::findOrFail($this->record);

        $updateData = [
            'name'      => $data['full_name'] ?? $user->name,
            'email'     => $data['email'] ?? $user->email,
            'is_active' => ($data['status'] ?? 'Active') === 'Active',
        ];
        if (!empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }
        $user->update($updateData);

        \Filament\Notifications\Notification::make()->title('User updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageUsers::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageUsers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
