<?php

namespace Leart\JsonDerulo\Services;

class StorageService
{
    private string $basePath;

    public function __construct(string $collection)
    {
        $this->basePath = __DIR__ . '/../../ElevUra-AI/data/' . $collection;
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0777, true);
        }
    }

    public function save(string $id, array $data): bool
    {
        $data['_id'] = $id;
        $data['_updated'] = date('c');
        if (!isset($data['_created'])) {
            $data['_created'] = date('c');
        }
        $path = $this->getPath($id);
        return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    public function load(string $id): ?array
    {
        $path = $this->getPath($id);
        if (!file_exists($path)) {
            return null;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }
        $data = json_decode($content, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
    }

    public function listAll(): array
    {
        $items = [];
        $pattern = $this->basePath . '/*.json';
        $files = glob($pattern);
        if ($files === false) {
            return [];
        }
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) continue;
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $items[] = $data;
            }
        }
        usort($items, function ($a, $b) {
            return ($b['_updated'] ?? '') <=> ($a['_updated'] ?? '');
        });
        return $items;
    }

    public function delete(string $id): bool
    {
        $path = $this->getPath($id);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    public function append(string $id, string $key, array $entry): array
    {
        $data = $this->load($id);
        if ($data === null) {
            $data = ['_id' => $id, '_created' => date('c')];
        }
        if (!isset($data[$key]) || !is_array($data[$key])) {
            $data[$key] = [];
        }
        $data[$key][] = $entry;
        $this->save($id, $data);
        return $data;
    }

    private function getPath(string $id): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $id);
        return $this->basePath . '/' . $safe . '.json';
    }
}
