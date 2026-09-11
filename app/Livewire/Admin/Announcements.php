<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class Announcements extends Component
{
    use WithFileUploads;

    public $announcements;
    public $title = '';
    public $content = '';
    public $icon_color = 'blue';
    public $order = 0;
    public $is_active = true;
    public $media_type = 'text';
    public $media_file;
    public $youtube_url = '';
    public $editingId = null;
    public $showModal = false;

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:100',
            'content' => 'nullable|string|max:500',
            'icon_color' => 'required|in:blue,green,amber,red,purple',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'media_type' => 'required|in:text,image,video,youtube',
            'youtube_url' => 'nullable|url',
        ];

        if ($this->media_type === 'image' && !$this->editingId) {
            $rules['media_file'] = 'required|image|max:5120'; // 5MB max
        } elseif ($this->media_type === 'image' && $this->media_file) {
            $rules['media_file'] = 'image|max:5120';
        }

        if ($this->media_type === 'video' && !$this->editingId) {
            $rules['media_file'] = 'required|mimes:mp4,webm,ogg|max:51200'; // 50MB max
        } elseif ($this->media_type === 'video' && $this->media_file) {
            $rules['media_file'] = 'mimes:mp4,webm,ogg|max:51200';
        }

        return $rules;
    }

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
        $this->reset(['title', 'content', 'icon_color', 'order', 'is_active', 'editingId', 'media_type', 'media_file', 'youtube_url']);
        $this->icon_color = 'blue';
        $this->media_type = 'text';
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
        $this->media_type = $announcement->media_type ?? 'text';
        $this->youtube_url = $announcement->youtube_url ?? '';
        $this->media_file = null;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'icon_color' => $this->icon_color,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'media_type' => $this->media_type,
            'youtube_url' => $this->media_type === 'youtube' ? $this->youtube_url : null,
        ];

        // Handle file upload
        if ($this->media_file && in_array($this->media_type, ['image', 'video'])) {
            $path = $this->media_file->store('announcements', 'public');
            $data['media_path'] = $path;
        }

        if ($this->editingId) {
            $announcement = Announcement::findOrFail($this->editingId);

            // Delete old file if new one uploaded
            if ($this->media_file && $announcement->media_path) {
                \Storage::disk('public')->delete($announcement->media_path);
            }

            $announcement->update($data);
            session()->flash('success', 'Pengumuman berhasil diperbarui.');
        } else {
            Announcement::create($data);
            session()->flash('success', 'Pengumuman berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->loadAnnouncements();
    }

    public function delete($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Delete associated file
        if ($announcement->media_path) {
            \Storage::disk('public')->delete($announcement->media_path);
        }

        $announcement->delete();
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
        $this->reset(['title', 'content', 'icon_color', 'order', 'is_active', 'editingId', 'media_type', 'media_file', 'youtube_url']);
    }

    public function render()
    {
        return view('livewire.admin.announcements');
    }
}
