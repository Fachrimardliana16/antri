<?php

namespace App\Livewire\Admin;

use App\Models\Counter;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Counters extends Component
{
    public bool $showModal = false;
    public ?int $editingCounterId = null;

    public string $name = '';
    public int $number = 1;
    public ?int $service_id = null;
    public ?int $current_operator_id = null;
    public string $status = 'closed';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'number' => 'required|integer|unique:counters,number,' . ($this->editingCounterId ?? 'NULL') . ',id',
            'service_id' => 'required|exists:services,id',
            'status' => 'required|in:active,break,closed',
        ];
    }

    public function create()
    {
        $this->reset(['editingCounterId', 'name', 'number', 'service_id', 'current_operator_id', 'status']);
        $maxNum = Counter::max('number') ?? 0;
        $this->number = $maxNum + 1;
        $this->name = "Loket {$this->number}";
        $this->status = 'closed';
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $counter = Counter::findOrFail($id);
        $this->editingCounterId = $counter->id;
        $this->name = $counter->name;
        $this->number = $counter->number;
        $this->service_id = $counter->service_id;
        $this->current_operator_id = $counter->current_operator_id;
        $this->status = $counter->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Counter::updateOrCreate(
            ['id' => $this->editingCounterId],
            [
                'name' => $this->name,
                'number' => $this->number,
                'service_id' => $this->service_id,
                'current_operator_id' => $this->current_operator_id,
                'status' => $this->status,
            ]
        );

        $this->showModal = false;
        session()->flash('success', 'Data Loket berhasil disimpan.');
    }

    public function delete(int $id)
    {
        $counter = Counter::findOrFail($id);
        $counter->delete();
        session()->flash('success', 'Loket berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.counters', [
            'counters' => Counter::with(['service', 'currentOperator', 'currentTicket'])->orderBy('number', 'asc')->get(),
            'services' => Service::where('is_active', true)->get(),
            'operators' => User::where('role', 'operator')->get(),
        ]);
    }
}
