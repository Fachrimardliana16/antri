<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Announcements extends Component
{
    public $announcements;
    public $title = '';
    public $content = '';
    public $icon_color = 'blue';
    public $order = 0;
    public $is_active = true;
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'title' => 'required|string|max:100',
        'content' => 'required|string|max:500',
        'icon_color' => 'required|in:blue,green,amber,red,purple',
        'order' => 'required|integer|min:0',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->loadAnnouncements();
    }

    public function loadAnnouncements()
    {
        $this->announcements = Announcement::orderBy('order', 'asc')->get();
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'content', 'icon_color', 'order', 'is_active', 'editingId']);
        $this->icon_color = 'blue';
        $this->order = $this->announcements->max('order') + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->editingId = $id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->icon_color = $announcement->icon_color;
        $this->order = $announcement->order;
        $this->is_active = $announcement->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $announcement = Announcement::findOrFail($this->editingId);
            $announcement->update([
                'title' => $this->title,
                'content' => $this->content,
                'icon_color' => $this->icon_color,
                'order' => $this->order,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Pengumuman berhasil diperbarui.');
        } else {
            Announcement::create([
                'title' => $this->title,
                'content' => $this->content,
                'icon_color' => $this->icon_color,
                'order' => $this->order,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Pengumuman berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadAnnouncements();
    }

    public function delete($id)
    {
        Announcement::findOrFail($id)->delete();
        session()->flash('success', 'Pengumuman berhasil dihapus.');
        $this->loadAnnouncements();
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
        $this->loadAnnouncements();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content', 'icon_color', 'order', 'is_active', 'editingId']);
    }

    public function render()
    {
        return view('livewire.admin.announcements');
    }
}
