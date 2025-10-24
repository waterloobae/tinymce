<?php

namespace Waterloobae\TinyMce;

use Illuminate\Support\ServiceProvider;

class TinyMceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/tinymce'),
        ], 'tinymce-views');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tinymce');

        // Load Blade components
        $this->loadViewComponentsAs('tinymce', [
            \Waterloobae\TinyMce\View\Components\TinyMceEditor::class,
            \Waterloobae\TinyMce\View\Components\HtmlWithLatex::class,
        ]);
    }

    public function register(): void
    {
        //
    }
}
