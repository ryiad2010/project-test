<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class CustomerMetricCard extends Component
{
    public ?Model $record = null;
    public string $metricType;
    public string $icon;
    public $value = 0;

    public function mount(): void
    {
        if (! $this->record) {
            return;
        }

        $this->value = match ($this->metricType) {
            'total_spent' => $this->record->orders()->sum('total_price'),
            'open_tickets' => $this->record->supportTickets()->where('status', 'open')->count(),
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.customer-metric-card');
    }
}
