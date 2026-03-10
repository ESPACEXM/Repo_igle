<?php

namespace App\Livewire;

use App\Models\Song;
use App\Models\Tag;
use App\Services\LaCuerdaParserService;
use Livewire\Component;
use Livewire\WithPagination;

class SongManager extends Component
{
    use WithPagination;

    // Form fields
    public $songId = null;
    public $title = '';
    public $artist = '';
    public $key = '';
    public $tempo = '';
    public $duration = '';
    public $lyrics = '';
    public $chords = '';
    public $youtube_url = '';
    public $spotify_url = '';
    public $lyrics_url = '';
    public $chords_url = '';
    public $selectedTags = [];

    // La Cuerda import
    public $laCuerdaContent = '';
    public $showLaCuerdaImport = false;

    // Search and filters
    public $search = '';
    public $filterTag = '';
    public $filterKey = '';

    // Modal state
    public $showModal = false;
    public $modalMode = 'create';
    public $keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    public $transposeSteps = 0;

    // View song
    public $showViewModal = false;
    public $viewingSong = null;
    public $viewTransposeSteps = 0;

    // Flash messages
    public $flashMessage = '';
    public $flashType = 'success';

    // Delete confirmation
    public $confirmingDeleteId = null;

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'key' => 'nullable|in:' . implode(',', $this->keys),
            'tempo' => 'nullable|integer|min:1|max:300',
            'duration' => 'nullable|integer|min:1',
            'lyrics' => 'nullable|string',
            'chords' => 'nullable|string',
            'youtube_url' => 'nullable|url|max:500',
            'spotify_url' => 'nullable|url|max:500',
            'lyrics_url' => 'nullable|url|max:500',
            'chords_url' => 'nullable|url|max:500',
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'title.required' => 'El título de la canción es obligatorio.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'artist.max' => 'El artista no puede tener más de 255 caracteres.',
            'key.in' => 'La tonalidad seleccionada no es válida.',
            'tempo.integer' => 'El tempo debe ser un número entero.',
            'tempo.min' => 'El tempo debe ser al menos 1.',
            'tempo.max' => 'El tempo no puede ser mayor a 300.',
            'youtube_url.url' => 'El enlace de YouTube debe ser una URL válida.',
            'spotify_url.url' => 'El enlace de Spotify debe ser una URL válida.',
            'lyrics_url.url' => 'El enlace de letra debe ser una URL válida.',
            'lyrics_url.max' => 'El enlace de letra no puede tener más de 500 caracteres.',
            'chords_url.url' => 'El enlace de acordes debe ser una URL válida.',
            'chords_url.max' => 'El enlace de acordes no puede tener más de 500 caracteres.',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Escapa caracteres especiales de LIKE para prevenir SQL injection
     */
    protected function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    public function render()
    {
        $songs = Song::query()
            ->with('tags')
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->escapeLike($this->search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                        ->orWhere('artist', 'like', $searchTerm);
                });
            })
            ->when($this->filterTag, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->where('tags.id', $this->filterTag);
                });
            })
            ->when($this->filterKey, function ($query) {
                $query->where('key', $this->filterKey);
            })
            ->orderBy('title')
            ->paginate(12);

        $tags = Tag::orderBy('type')->orderBy('name')->get();
        $tagsByType = $tags->groupBy('type');

        return view('livewire.song-manager', [
            'songs' => $songs,
            'tags' => $tags,
            'tagsByType' => $tagsByType,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->songId = null;
        $this->title = '';
        $this->artist = '';
        $this->key = '';
        $this->tempo = '';
        $this->duration = '';
        $this->lyrics = '';
        $this->chords = '';
        $this->youtube_url = '';
        $this->spotify_url = '';
        $this->lyrics_url = '';
        $this->chords_url = '';
        $this->selectedTags = [];
        $this->transposeSteps = 0;
        $this->resetValidation();
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $song = Song::with('tags')->findOrFail($id);

        $this->songId = $song->id;
        $this->title = $song->title;
        $this->artist = $song->artist ?? '';
        $this->key = $song->key ?? '';
        $this->tempo = $song->tempo ?? '';
        $this->duration = $song->duration ?? '';
        $this->lyrics = $song->lyrics ?? '';
        $this->chords = $song->chords ?? '';
        $this->youtube_url = $song->youtube_url ?? '';
        $this->spotify_url = $song->spotify_url ?? '';
        $this->lyrics_url = $song->lyrics_url ?? '';
        $this->chords_url = $song->chords_url ?? '';
        $this->selectedTags = $song->tags->pluck('id')->toArray();
        $this->transposeSteps = 0;

        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'artist' => $this->artist ?: null,
                'key' => $this->key ?: null,
                'tempo' => $this->tempo ?: null,
                'duration' => $this->duration ?: null,
                'lyrics' => $this->lyrics ?: null,
                'chords' => $this->chords ?: null,
                'youtube_url' => $this->youtube_url ?: null,
                'spotify_url' => $this->spotify_url ?: null,
                'lyrics_url' => $this->lyrics_url ?: null,
                'chords_url' => $this->chords_url ?: null,
            ];

            if ($this->modalMode === 'create') {
                $song = Song::create($data);
                $song->tags()->sync($this->selectedTags);
                $this->flashMessage = 'Canción creada exitosamente.';
            } else {
                $song = Song::findOrFail($this->songId);
                $song->update($data);
                $song->tags()->sync($this->selectedTags);
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
        $this->confirmingDeleteId = $id;
    }

    public function delete()
    {
        try {
            $song = Song::findOrFail($this->confirmingDeleteId);
            $song->delete();
            $this->flashMessage = 'Canción eliminada exitosamente.';
            $this->flashType = 'success';
            $this->confirmingDeleteId = null;
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al eliminar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function dismissFlash()
    {
        $this->flashMessage = '';
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->filterTag = '';
        $this->filterKey = '';
        $this->resetPage();
    }

    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_diff($this->selectedTags, [$tagId]);
        } else {
            $this->selectedTags[] = $tagId;
        }
    }

    public function transposeUp()
    {
        $this->transposeSteps = min($this->transposeSteps + 1, 11);
    }

    public function transposeDown()
    {
        $this->transposeSteps = max($this->transposeSteps - 1, -11);
    }

    public function resetTranspose()
    {
        $this->transposeSteps = 0;
    }

    public function show($id)
    {
        return redirect()->route('songs.show', ['song' => $id]);
    }

    public function closeView()
    {
        $this->showViewModal = false;
        $this->viewingSong = null;
        $this->viewTransposeSteps = 0;
    }

    public function viewTransposeUp()
    {
        $this->viewTransposeSteps = min($this->viewTransposeSteps + 1, 11);
    }

    public function viewTransposeDown()
    {
        $this->viewTransposeSteps = max($this->viewTransposeSteps - 1, -11);
    }

    public function viewTransposeReset()
    {
        $this->viewTransposeSteps = 0;
    }

    public function toggleLaCuerdaImport()
    {
        $this->showLaCuerdaImport = !$this->showLaCuerdaImport;
        if (!$this->showLaCuerdaImport) {
            $this->laCuerdaContent = '';
        }
    }

    public function parseLaCuerda()
    {
        if (empty($this->laCuerdaContent)) {
            return;
        }

        // Validar límite de tamaño (50KB máximo)
        if (strlen($this->laCuerdaContent) > 51200) {
            $this->flashMessage = 'El contenido es demasiado largo (máximo 50KB)';
            $this->flashType = 'error';
            return;
        }

        $parser = new LaCuerdaParserService();
        $parsed = $parser->parse($this->laCuerdaContent);

        // Solo actualizar si se detectaron valores
        if (!empty($parsed['title'])) {
            $this->title = $parsed['title'];
        }
        if (!empty($parsed['artist'])) {
            $this->artist = $parsed['artist'];
        }
        if (!empty($parsed['chords'])) {
            // Asegurar que los saltos de línea se preservan correctamente
            $this->chords = str_replace("\r\n", "\n", $parsed['chords']);
        }

        // Cerrar el modal de importación
        $this->showLaCuerdaImport = false;
        $this->laCuerdaContent = '';

        // Mostrar mensaje de éxito
        $this->flashMessage = 'Contenido de La Cuerda importado exitosamente.';
        $this->flashType = 'success';
    }

    public function updatedLaCuerdaContent()
    {
        // Validar límite de tamaño (50KB máximo)
        if (strlen($this->laCuerdaContent) > 51200) {
            $this->flashMessage = 'El contenido es demasiado largo (máximo 50KB)';
            $this->flashType = 'error';
            $this->laCuerdaContent = substr($this->laCuerdaContent, 0, 51200);
            return;
        }

        // Auto-detectar si es formato de La Cuerda y parsear automáticamente
        if (!empty($this->laCuerdaContent)) {
            $parser = new LaCuerdaParserService();
            if ($parser->isLaCuerdaFormat($this->laCuerdaContent)) {
                // Esperar un poco para que el usuario termine de pegar
                // El parsing se hará manualmente con el botón
            }
        }
    }
}
