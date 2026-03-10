<?php

namespace App\Services;

class LaCuerdaParserService
{
    /**
     * Parsea el contenido copiado de La Cuerda
     */
    public function parse(string $content): array
    {
        // Normalizar saltos de línea
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        $result = [
            'title' => '',
            'artist' => '',
            'album' => '',
            'chords' => '',
            'sections' => [],
        ];

        // Extraer metadata (primeras líneas)
        $result['title'] = $this->extractTitle($lines);
        $result['artist'] = $this->extractArtist($lines);
        $result['album'] = $this->extractAlbum($lines);

        // Procesar el contenido de acordes y letra
        $chordContent = $this->extractChordContent($lines);
        $result['chords'] = $chordContent;

        return $result;
    }

    /**
     * Extrae el título de la canción
     */
    protected function extractTitle(array $lines): string
    {
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            // Ignorar líneas de metadata de La Cuerda
            if ($this->isLaCuerdaMetadata($trimmed)) continue;

            // Ignorar líneas que parecen acordes o secciones
            if ($this->looksLikeChords($trimmed)) continue;
            if ($this->isSectionHeader($trimmed)) continue;

            // La primera línea significativa debería ser el título
            return $trimmed;
        }
        return '';
    }

    /**
     * Extrae el artista
     */
    protected function extractArtist(array $lines): string
    {
        $foundTitle = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            if ($this->isLaCuerdaMetadata($trimmed)) continue;
            if ($this->looksLikeChords($trimmed)) continue;
            if ($this->isSectionHeader($trimmed)) continue;

            if (!$foundTitle) {
                $foundTitle = true; // Saltar título
                continue;
            }

            // Esta debería ser el artista
            return $trimmed;
        }
        return '';
    }

    /**
     * Extrae el álbum
     */
    protected function extractAlbum(array $lines): string
    {
        foreach ($lines as $line) {
            $trimmed = trim($line);
            // Buscar patrón: "Album [año]" o similar
            if (preg_match('/\[\d{4}\]/', $trimmed)) {
                return $trimmed;
            }
        }
        return '';
    }

    /**
     * Extrae el contenido de acordes - PRESERVA FORMATO EXACTO
     */
    protected function extractChordContent(array $lines): string
    {
        $content = [];
        $foundChords = false;

        // Patrones para saltar (UI de La Cuerda)
        $skipPatterns = [
            'Enviado por',
            'Mostrar/Ocultar',
            'Desfile Automático',
            'Diagramas de Acordes',
            'Cambio de Tono',
            'Cifrado Inglés/Latino',
            'Formato del Texto',
            'Calificar',
            'Agregar a',
            'Imprimir',
            'Reportar',
        ];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Saltar líneas vacías al inicio
            if (!$foundChords && empty($trimmed)) {
                continue;
            }

            // Detectar cuando empieza el contenido real
            if (!$foundChords) {
                if ($this->isSectionHeader($trimmed) ||
                    $this->looksLikeChords($trimmed) ||
                    preg_match('/^[A-G][#bmMaj0-9\/\-]/', $trimmed)) {
                    $foundChords = true;
                } else {
                    continue;
                }
            }

            // Saltar líneas de UI de La Cuerda
            $skip = false;
            foreach ($skipPatterns as $pattern) {
                if (str_starts_with($trimmed, $pattern)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            // NO hacer trim a la línea original - preservar espacios para alineación de acordes
            // Solo eliminar caracteres de control al final
            $line = rtrim($line, "\r\n\t");

            $content[] = $line;
        }

        return implode("\n", $content);
    }

    /**
     * Detecta si una línea es un encabezado de sección
     */
    protected function isSectionHeader(string $line): bool
    {
        $headers = [
            'INTRO:', 'INTRO ',
            'VERSO:', 'VERSO ', 'VERSO',
            'CORO:', 'CORO ', 'CORO',
            'PUENTE:', 'PUENTE ', 'PUENTE',
            'PRE-CORO:', 'PRE-CORO ', 'PRE CORO',
            'SOLO:', 'SOLO ',
            'FINAL:', 'FINAL ',
            'OUTRO:', 'OUTRO ',
        ];

        $upperLine = strtoupper($line);
        foreach ($headers as $header) {
            if (str_starts_with($upperLine, $header)) {
                return true;
            }
        }

        // Detectar patrón: "Coro: (1era.vuelta)" o similar
        if (preg_match('/^(INTRO|VERSO?|CORO|PUENTE|PRE[-\s]?CORO|SOLO|FINAL|OUTRO)\s*[:(]/i', $line)) {
            return true;
        }

        return false;
    }

    /**
     * Detecta si una línea parece contener solo acordes
     */
    protected function looksLikeChords(string $line): bool
    {
        // Acordes comunes
        $chordPattern = '/^[\s]*[A-G][#bmMaj0-9\-\/\(]*(\s+[A-G][#bmMaj0-9\-\/\(]*)+[\s]*$/';

        // Líneas tipo "Am-D-G-C" o "Am - D - G - C"
        $progressionPattern = '/^[\s]*[A-G][#bmMaj0-9]*\s*[-\|]\s*/i';

        if (preg_match($chordPattern, $line)) {
            return true;
        }

        if (preg_match($progressionPattern, $line) && substr_count($line, '-') >= 1) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si es metadata de La Cuerda
     */
    protected function isLaCuerdaMetadata(string $line): bool
    {
        $patterns = [
            '/^\d+\.\d+\/10/',           // Rating: 8.52/10
            '/^\(\d+\)/',                 // (97)
            '/^msmr\d+$/i',              // Código: msmr1072
            '/^\[\d{4}\]$/',              // [2014]
            '/^Enviado por/',            // Enviado por contodo
            '/^Mostrar\/Ocultar/',       // Menú
            '/^Desfile Automático/',
            '/^Diagramas de Acordes/',
            '/^Cambio de Tono/',
            '/^Cifrado Inglés\/Latino/',
            '/^Formato del Texto/',
            '/^Calificar/',
            '/^Agregar a/',
            '/^Imprimir/',
            '/^Reportar/',
            '/^https?:\/\//',             // URLs
            '/^@/',                      // Usuarios
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si el contenido parece ser formato de La Cuerda
     */
    public function isLaCuerdaFormat(string $content): bool
    {
        $indicators = [
            'lacuerda.net',
            'Enviado por',
            'Mostrar/Ocultar',
            'Diagramas de Acordes',
            'Cambio de Tono',
            'Cifrado Inglés/Latino',
            'Formato del Texto',
        ];

        foreach ($indicators as $indicator) {
            if (str_contains($content, $indicator)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Formatea el contenido parseado para el sistema
     */
    public function formatForSystem(string $content): array
    {
        $parsed = $this->parse($content);

        return [
            'title' => $parsed['title'],
            'artist' => $parsed['artist'],
            'chords' => $parsed['chords'],
            'raw_chords' => $parsed['chords'],
        ];
    }
}
