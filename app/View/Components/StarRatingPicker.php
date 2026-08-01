<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StarRatingPicker extends Component
{
    public int $selected;

    public string $name;

    public function __construct(int $selected = 0, string $name = 'rating')
    {
        $this->selected = $selected;
        $this->name     = $name;
    }

    public function render()
    {
        return view('components.frontend.star-rating-picker');
    }
}
