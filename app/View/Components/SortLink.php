<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SortLink extends Component
{
    public function __construct(
        public string $column,
        public string $label,
    ) {}

    public function nextDirection(): string
    {
        if (request()->query('sort') !== $this->column) {
            return 'asc';
        }

        return request()->query('direction') === 'asc' ? 'desc' : 'asc';
    }

    public function isActive(): bool
    {
        return request()->query('sort') === $this->column;
    }

    public function url(): string
    {
        return request()->fullUrlWithQuery([
            'sort' => $this->column,
            'direction' => $this->nextDirection(),
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sort-link');
    }
}
