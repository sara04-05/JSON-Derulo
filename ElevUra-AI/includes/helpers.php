<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function attr_hidden(bool $condition): string
{
    return $condition ? '' : ' hidden';
}

function asset_url(string $path): string
{
  $path = ltrim($path, '/');
  return $path;
}
