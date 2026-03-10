<?php

namespace App\Livewire;

use App\Models\Song;
use App\Services\ChordFormatterService;
use Livewire\Component;

class SongShow extends Component
{
    public Song $song;
    public $transposeSteps = 0;

    public function mount(Song $song)
    {
        $this->song = $song;
    }

    public function transposeUp()
    {
        $this->transposeSteps = min($this->transposeSteps + 1, 11);
    }

    public function transposeDown()
    {
        $this->transposeSteps = max($this->transposeSteps - 1, -11);
    }

    public function transposeReset()
    {
        $this->transposeSteps = 0;
    }

    public function getFormattedChordsProperty()
    {
        if (!$this->song->chords) {
            return null;
        }

        return app(ChordFormatterService::class)->transpose(
            $this->song->chords,
            $this->transposeSteps
        );
    }

    public function render()
    {
        return view('livewire.song-show')
            ->layout('layouts.app');
    }
}
