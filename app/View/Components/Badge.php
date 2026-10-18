<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    /**
     * @param  'gray'|'green'|'red'|'amber'|'blue'|'indigo'  $variant
     */
    public function __construct(
        public string $variant = 'gray',
    ) {}

    public function classes(): string
    {
        $map = [
            'gray' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
            'green' => 'bg-green-100 text-green-700 ring-green-600/20',
            'red' => 'bg-red-100 text-red-700 ring-red-600/20',
            'amber' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            'blue' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
            'indigo' => 'bg-indigo-100 text-indigo-700 ring-indigo-600/20',
        ];

        return 'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.$map[$this->variant] ?? $map['gray'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.badge');
    }
}
