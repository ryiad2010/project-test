<?php

namespace App\Livewire;

use App\Components\TextInput;
use Livewire\Component;
use Illuminate\Support\Str;

class TestForm extends Component
{
    public $email;

    public function render()
    {
        TextInput::configureUsing(function (TextInput $input) {
            $input->maxLength(10);
        });
        TextInput::macro('foo',fn()=>'foo');
        $nameInput = TextInput::make('name')->label('name')
            // ->maxLength(10)
            ->livewire($this);
        $emailInput = TextInput::make('email')->label('email')
            // ->maxLength(10)
            ->livewire($this)
            ->foo();




        return view('livewire.test-form', ['nameInput' => $nameInput, 'emailInput' => $emailInput]);
    }
}
