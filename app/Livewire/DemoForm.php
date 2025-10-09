<?php

namespace App\Livewire;

use Ryiad\FilamentToolkit\Forms\Components\ColorPicker;
use Ryiad\FilamentToolkit\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class DemoForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Colors')


                    ->schema([

                                ColorPicker::make('primary')
                                    ->default('#fbbf24')
                                    ->width(100),
                                ColorPicker::make('secondary')

                                    ->width(100)
                                    ->default('#c884fc'),
                                ColorPicker::make('success')

                                    ->width(100)
                                    ->default('#84cc16'),
                                ColorPicker::make('warning')
                                    ->default('#facc15')

                                    ->width(100),
                                ColorPicker::make('danger')
                                    ->default('#ef4444')
                                    ->width(100),
                                ColorPicker::make('gray')
                                    ->default('#a1a1aa')
                                    ->width(100)
                                    ,

                            ])->columns(3),

            ])
            ->statePath('data');
    }

    public function submit()
    {
        $this->data = $this->form->getState();
    }

    public function render()
    {
        return view('livewire.demo-form');
    }
}
