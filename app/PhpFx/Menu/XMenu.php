<?php

namespace App\PhpFx\Menu;

use Illuminate\Support\Collection;

class XMenu
{
    public string $name;
    public string $icon;
    public string $route;
    public Collection $subMenu;
    public bool $condition;
    public ?int $badge;
    public bool $active = false;

    protected Collection $items;

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->icon = $data['icon'] ?? '';
        $this->route = $data['route'] ?? '#';
        $this->subMenu = self::get($data['subMenu'] ?? []);
        $this->condition = $data['condition'] ?? true;
        $this->badge = $data['badge'] ?? null;
        $this->active = $data['active'] ?? false;
    }

    public static function get(array $data): Collection
    {
        return collect($data)
            ->map(fn($item) => new self($item))
            ->where('condition', true);
    }

    public function hasSubMenu(): bool
    {
        return $this->subMenu->isNotEmpty();
    }
}
