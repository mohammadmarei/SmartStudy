<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class MaterialTextExtractor
{
    /**
     * Extracts text from an uploaded material (PDF/DOCX).
     *
     * @throws \RuntimeException
     */
    public function extract(File $material): string
    {
        $disk = Storage::disk('public');
        if (!$material->file_path || !$disk->exists($material->file_path)) {
            throw new \RuntimeException('Material file not found on disk.');
        }

        $fullPath = $disk->path($material->file_path);
        $ext = strtolower(pathinfo($material->file_name ?? $fullPath, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => $this->extractPdf($fullPath),
            'docx', 'doc' => $this->extractWord($fullPath),
            default => throw new \RuntimeException("Unsupported material type: {$ext}"),
        };
    }

    private function extractPdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);
        $text = trim($pdf->getText());

        if ($text === '') {
            throw new \RuntimeException('Could not extract text from PDF.');
        }

        return $text;
    }

    private function extractWord(string $path): string
    {
        $phpWord = IOFactory::load($path);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $el) {
                if (method_exists($el, 'getText')) {
                    $text .= $el->getText()."\n";
                }
            }
        }

        $text = trim($text);
        if ($text === '') {
            throw new \RuntimeException('Could not extract text from document.');
        }

        return $text;
    }
}

