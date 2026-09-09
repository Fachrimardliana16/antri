<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Services extends Component
{
    public bool $showModal = false;
    public ?int $editingServiceId = null;

    public string $name = '';
    public string $code = '';
    public string $prefix = '';
    public string $description = '';
    public int $estimated_time_minutes = 5;
    public string $color = '#2563eb';
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:services,code,' . ($this->editingServiceId ?? 'NULL') . ',id',
            'prefix' => 'required|string|max:10',
            'estimated_time_minutes' => 'required|integer|min:1|max:120',
            'color' => 'required|string|max:20',
        ];
    }

    public function create()
    {
        $this->reset(['editingServiceId', 'name', 'code', 'prefix', 'description', 'estimated_time_minutes', 'color', 'is_active']);
        $this->color = '#2563eb';
        $this->estimated_time_minutes = 5;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $service = Service::findOrFail($id);
        $this->editingServiceId = $service->id;
        $this->name = $service->name;
        $this->code = $service->code;
        $this->prefix = $service->prefix;
        $this->description = $service->description ?? '';
        $this->estimated_time_minutes = $service->estimated_time_minutes;
        $this->color = $service->color;
        $this->is_active = $service->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Service::updateOrCreate(
            ['id' => $this->editingServiceId],
            [
                'name' => $this->name,
                'code' => strtoupper($this->code),
                'prefix' => strtoupper($this->prefix ?: $this->code),
                'description' => $this->description,
                'estimated_time_minutes' => $this->estimated_time_minutes,
                'color' => $this->color,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        session()->flash('success', 'Data Layanan berhasil disimpan.');
    }

    public function toggleActive(int $id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => ! $service->is_active]);
    }

    public function delete(int $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        session()->flash('success', 'Layanan berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.services', [
            'services' => Service::withCount('counters')->orderBy('code', 'asc')->get(),
        ]);
    }
}
