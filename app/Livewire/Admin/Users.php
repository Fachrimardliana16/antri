<?php

namespace App\Livewire\Admin;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Users extends Component
{
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'operator'; // super_admin, admin, operator
    public ?int $assigned_counter_id = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($this->editingUserId ?? 'NULL') . ',id',
            'role' => 'required|in:super_admin,admin,operator',
            'password' => $this->editingUserId ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function create()
    {
        $this->reset(['editingUserId', 'name', 'email', 'password', 'role', 'assigned_counter_id']);
        $this->role = 'operator';
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->assigned_counter_id = $user->assigned_counter_id;
        $this->password = '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'assigned_counter_id' => $this->assigned_counter_id,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->editingUserId], $data);

        $this->showModal = false;
        session()->flash('success', 'Data Akun Pengguna berhasil disimpan.');
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        $user->delete();
        session()->flash('success', 'Akun berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.users', [
            'users' => User::with('assignedCounter')->orderBy('role', 'asc')->get(),
            'counters' => Counter::all(),
        ]);
    }
}
