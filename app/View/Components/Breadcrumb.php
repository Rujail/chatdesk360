<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public string $title;
    public array $links;   // optional extra links

    public function __construct(string $title = 'Page Title', array $links = [])
    {
        $this->title = $title;
        $this->links = $links;
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}