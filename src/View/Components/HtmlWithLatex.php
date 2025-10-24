<?php

namespace Waterloobae\TinyMce\View\Components;

use Illuminate\View\Component;

class HtmlWithLatex extends Component
{
    public function __construct(
        public string $content = '',
    ) {}

    public function render()
    {
        return view('tinymce::components.html-with-latex');
    }
}
