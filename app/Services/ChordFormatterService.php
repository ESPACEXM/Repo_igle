<?php

namespace App\Services;

class ChordFormatterService
{
    /**
     * Lista de acordes válidos (notas musicales)
     */
    protected array $validChords = [
        // Básicos
        'C', 'D', 'E', 'F', 'G', 'A', 'B',
        // Sostenidos
        'C#', 'D#', 'F#', 'G#', 'A#',
        'Db', 'Eb', 'Gb', 'Ab', 'Bb',
        // Con modificadores
        'Cm', 'Dm', 'Em', 'Fm', 'Gm', 'Am', 'Bm',
        'C#m', 'D#m', 'F#m', 'G#m', 'A#m',
        'Dbm', 'Ebm', 'Gbm', 'Abm', 'Bbm',
        // Mayores con 7
        'C7', 'D7', 'E7', 'F7', 'G7', 'A7', 'B7',
        'C#7', 'D#7', 'F#7', 'G#7', 'A#7',
        // Menores con 7
        'Cm7', 'Dm7', 'Em7', 'Fm7', 'Gm7', 'Am7', 'Bm7',
        // Mayores con maj7
        'Cmaj7', 'Dmaj7', 'Emaj7', 'Fmaj7', 'Gmaj7', 'Amaj7', 'Bmaj7',
        // Suspensos
        'Csus', 'Csus4', 'Dsus', 'Dsus4', 'Esus', 'Esus4',
        'Fsus', 'Fsus4', 'Gsus', 'Gsus4', 'Asus', 'Asus4', 'Bsus', 'Bsus4',
        // Disminuidos
        'Cdim', 'Ddim', 'Edim', 'Fdim', 'Gdim', 'Adim', 'Bdim',
        // Aumentados
        'Caug', 'C+', 'Daug', 'D+', 'Eaug', 'E+', 'Faug', 'F+',
        'Gaug', 'G+', 'Aaug', 'A+', 'Baug', 'B+',
        // Quintas
        'C5', 'D5', 'E5', 'F5', 'G5', 'A5', 'B5',
        // Con add
        'Cadd9', 'Dadd9', 'Eadd9', 'Fadd9', 'Gadd9', 'Aadd9', 'Badd9',
        // Con 6
        'C6', 'D6', 'E6', 'F6', 'G6', 'A6', 'B6',
        // Con 9
        'C9', 'D9', 'E9', 'F9', 'G9', 'A9', 'B9',
        // Con /bass
        'C/G', 'G/B', 'D/F#', 'C/E', 'Am/G', 'Dm/C',
    ];

    /**
     * Detecta si una línea contiene acordes
     */
    public function isChordLine(string $line): bool
    {
        $line = trim($line);
        if (empty($line)) {
            return false;
        }

        // Dividir por espacios
        $tokens = preg_split('/\s+/', $line);
        $chordCount = 0;
        $totalTokens = count($tokens);

        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) {
                continue;
            }

            // Verificar si es un acorde válido
            if ($this->isValidChord($token)) {
                $chordCount++;
            } elseif (!$this->isPossibleChord($token)) {
                return false;
            }
        }

        // Si al menos 50% son acordes reconocidos
        return $totalTokens > 0 && ($chordCount / $totalTokens) >= 0.5;
    }

    /**
     * Verifica si un token es un acorde válido
     */
    public function isValidChord(string $token): bool
    {
        $token = trim($token);

        // Verificar en lista de acordes conocidos
        if (in_array($token, $this->validChords, true)) {
            return true;
        }

        // Patrón regex para acordes
        $pattern = '/^[A-Ga-g][#b]?(m|min|maj|M|dim|aug|\+|-|sus|add|[0-9])*(\/[A-Ga-g][#b]?)?$/';

        return preg_match($pattern, $token) === 1 && strlen($token) <= 10;
    }

    /**
     * Verifica si un token podría ser un acorde
     */
    protected function isPossibleChord(string $token): bool
    {
        // Si empieza con nota musical y tiene longitud razonable
        if (preg_match('/^[A-Ga-g]/', $token) && strlen($token) <= 12) {
            return true;
        }

        return false;
    }

    /**
     * Formatea acordes pegados en líneas separadas
     */
    public function formatChords(string $text): string
    {
        $lines = explode("\n", $text);
        $result = [];

        foreach ($lines as $line) {
            $line = rtrim($line);

            if ($this->isChordLine($line)) {
                // Es una línea de acordes, formatearla
                $formatted = $this->formatChordLine($line);
                $result[] = $formatted;
            } else {
                // Es una línea de letra, dejarla como está
                $result[] = $line;
            }
        }

        return implode("\n", $result);
    }

    /**
     * Formatea una línea de acordes
     */
    protected function formatChordLine(string $line): string
    {
        // Detectar acordes y sus posiciones
        $chords = $this->extractChordsWithPositions($line);

        if (empty($chords)) {
            return $line;
        }

        // Reconstruir línea con acordes resaltados
        $result = '';
        $lastPos = 0;

        foreach ($chords as $chord) {
            $result .= substr($line, $lastPos, $chord['position'] - $lastPos);
            $result .= '[' . $chord['chord'] . ']';
            $lastPos = $chord['position'] + strlen($chord['chord']);
        }

        $result .= substr($line, $lastPos);

        return $result;
    }

    /**
     * Extrae acordes con sus posiciones
     */
    protected function extractChordsWithPositions(string $line): array
    {
        $chords = [];
        $tokens = preg_split('/(\s+)/', $line, -1, PREG_SPLIT_OFFSET_CAPTURE);

        foreach ($tokens as $token) {
            $text = $token[0];
            $position = $token[1];

            if ($this->isValidChord(trim($text))) {
                $chords[] = [
                    'chord' => trim($text),
                    'position' => $position,
                ];
            }
        }

        return $chords;
    }

    /**
     * Detecta la estructura de una canción (intro, verso, coro, etc.)
     */
    public function detectStructure(string $text): array
    {
        $lines = explode("\n", $text);
        $structure = [];
        $currentSection = 'Intro';

        $sectionPatterns = [
            'intro' => '/^\s*(intro|INTRO|Intro)\s*[:\-]?\s*$/i',
            'verse' => '/^\s*(verso|VERSO|Verso|verse|VERSE|V\d+)\s*[:\-]?\s*$/i',
            'chorus' => '/^\s*(coro|CORO|Coro|chorus|CHORUS|Pre-coro|PRE-CORO)\s*[:\-]?\s*$/i',
            'bridge' => '/^\s*(puente|PUENTE|Puente|bridge|BRIDGE)\s*[:\-]?\s*$/i',
            'outro' => '/^\s*(outro|OUTRO|final|FINAL|coda|CODA)\s*[:\-]?\s*$/i',
        ];

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);

            foreach ($sectionPatterns as $type => $pattern) {
                if (preg_match($pattern, $trimmed)) {
                    $currentSection = $type;
                    $structure[$index] = [
                        'type' => $type,
                        'label' => $trimmed,
                    ];
                    break;
                }
            }
        }

        return $structure;
    }

    /**
     * Formatea acordes con HTML para visualización
     */
    public function formatChordsHtml(string $text): string
    {
        $lines = explode("\n", $text);
        $html = [];

        foreach ($lines as $line) {
            $line = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');

            if ($this->isChordLine($line)) {
                // Línea de acordes - resaltar cada acorde
                $formatted = preg_replace_callback(
                    '/([A-G][#b]?(?:m|min|maj|M|dim|aug|\+|-|sus|add|[0-9])*(?:\/[A-G][#b]?)?)/',
                    function ($matches) {
                        $chord = $matches[1];
                        if ($this->isValidChord($chord)) {
                            return '<span class="chord font-bold text-church-600 bg-church-100 px-1 rounded">' . $chord . '</span>';
                        }
                        return $chord;
                    },
                    $line
                );
                $html[] = '<div class="chord-line font-mono text-sm my-1">' . $formatted . '</div>';
            } else {
                // Línea de letra
                $html[] = '<div class="lyric-line my-1">' . $line . '</div>';
            }
        }

        return implode("\n", $html);
    }

    /**
     * Transpone un acorde
     */
    public function transposeChord(string $chord, int $semitones): string
    {
        $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $notesFlat = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];

        // Detectar nota base
        if (preg_match('/^([A-G])(#|b)?/', $chord, $matches)) {
            $baseNote = $matches[1];
            $accidental = $matches[2] ?? '';
            $fullNote = $baseNote . $accidental;

            // Encontrar índice actual
            $currentIndex = array_search($fullNote, $notes, true);
            if ($currentIndex === false) {
                $currentIndex = array_search($fullNote, $notesFlat, true);
            }

            if ($currentIndex !== false) {
                // Calcular nueva nota
                $newIndex = ($currentIndex + $semitones) % 12;
                if ($newIndex < 0) {
                    $newIndex += 12;
                }
                $newNote = $notes[$newIndex];

                // Reemplazar nota en el acorde
                return preg_replace('/^' . preg_quote($fullNote, '/') . '/', $newNote, $chord, 1);
            }
        }

        return $chord;
    }

    /**
     * Transpone todo el texto de acordes
     */
    public function transpose(string $text, int $semitones): string
    {
        $lines = explode("\n", $text);
        $result = [];

        foreach ($lines as $line) {
            if ($this->isChordLine($line)) {
                $tokens = preg_split('/(\s+)/', $line, -1, PREG_SPLIT_DELIM_CAPTURE);
                $transposed = '';

                foreach ($tokens as $token) {
                    $trimmed = trim($token);
                    if ($this->isValidChord($trimmed)) {
                        $transposed .= $this->transposeChord($trimmed, $semitones) . ' ';
                    } else {
                        $transposed .= $token;
                    }
                }

                $result[] = rtrim($transposed);
            } else {
                $result[] = $line;
            }
        }

        return implode("\n", $result);
    }
}
