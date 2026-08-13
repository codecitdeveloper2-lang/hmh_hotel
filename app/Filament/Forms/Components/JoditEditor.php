<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class JoditEditor extends Field
{
    protected string $view = 'filament.forms.components.jodit-editor';
    
    protected string $direction = '';

    public function direction(string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
