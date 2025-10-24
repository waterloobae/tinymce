<?php

namespace Waterloobae\TinyMce\View\Components;

use Illuminate\View\Component;

class TinyMceEditor extends Component
{
    public function __construct(
        public string $name = 'content',
        public string $value = '',
        public ?string $wireModel = null,
        public string $profile = 'simple',
        public int $height = 400,
    ) {}

    public function render()
    {
        return view('tinymce::components.tinymce-editor');
    }
}
