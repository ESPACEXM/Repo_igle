<?php

namespace App\Livewire;

use App\Models\Song;
use Livewire\Component;
use Livewire\WithPagination;

class SongManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $songId = null;
    public $title = '';
    public $author = '';
    public $key = '';
    public $bpm = '';
    public $link = '';

    // Estados de UI
    public $showModal = false;
    public $modalMode = 'create'; // 'create' o 'edit'
    public $search = '';
    public $confirmingDelete = null;

    // Mensajes flash
    public $flashMessage = null;
    public $flashType = 'success';

    // Opciones de key musicales
    public $keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

    protected $queryString = ['search'];

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'key' => 'nullable|in:' . implode(',', $this->keys),
            'bpm' => 'nullable|integer|min:1|max:300',
            'link' => 'nullable|url|max:500',
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'title.required' => 'El título de la canción es obligatorio.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'author.max' => 'El autor no puede tener más de 255 caracteres.',
            'key.in' => 'La tonalidad seleccionada no es válida.',
            'bpm.integer' => 'El BPM debe ser un número entero.',
            'bpm.min' => 'El BPM debe ser al menos 1.',
            'bpm.max' => 'El BPM no puede ser mayor a 300.',
            'link.url' => 'El enlace debe ser una URL válida.',
            'link.max' => 'El enlace no puede tener más de 500 caracteres.',
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $songs = Song::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('author', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('title')
            ->paginate(12);

        return view('livewire.song-manager', [
            'songs' => $songs,
        ])->layout('layouts.app');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->songId = null;
        $this->title = '';
        $this->author = '';
        $this->key = '';
        $this->bpm = '';
        $this->link = '';
        $this->resetValidation();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $song = Song::findOrFail($id);
        
        $this->songId = $song->id;
        $this->title = $song->title;
        $this->author = $song->author ?? '';
        $this->key = $song->key ?? '';
        $this->bpm = $song->bpm ?? '';
        $this->link = $song->link ?? '';
        
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'author' => $this->author ?: null,
                'key' => $this->key ?: null,
                'bpm' => $this->bpm ?: null,
                'link' => $this->link ?: null,
            ];

            if ($this->modalMode === 'create') {
                Song::create($data);
                $this->flashMessage = 'Canción creada exitosamente.';
            } else {
                $song = Song::findOrFail($this->songId);
                $song->update($data);
                $this->flashMessage = 'Canción actualizada exitosamente.';
            }

            $this->flashType = 'success';
            $this->showModal = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->flashMessage = 'Error: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = null;
    }

    public function delete()
    {
        try {
            $song = Song::findOrFail($this->confirmingDelete);
            $song->delete();
            $this->flashMessage = 'Canción eliminada exitosamente.';
            $this->flashType = 'success';
            $this->confirmingDelete = null;
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al eliminar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Get a friendly name for the link platform
     */
    public function getLinkPlatform($url)
    {
        if (empty($url)) return null;
        
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'YouTube';
        } elseif (str_contains($url, 'spotify.com')) {
            return 'Spotify';
        } elseif (str_contains($url, 'apple.com') || str_contains($url, 'music.apple.com')) {
            return 'Apple Music';
        } elseif (str_contains($url, 'deezer.com')) {
            return 'Deezer';
        } elseif (str_contains($url, 'tidal.com')) {
            return 'Tidal';
        }
        
        return 'Enlace';
    }
}
