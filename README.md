# TinyMCE + LaTeX for Laravel, Livewire & Filament

A comprehensive Laravel package providing TinyMCE rich text editor with LaTeX/MathJax support for Laravel Blade, Livewire components, and Filament admin panels.

## Features

- 🎨 **TinyMCE 6** rich text editor with dark theme support
- 📐 **MathJax 3** for beautiful LaTeX equation rendering
- 🖼️ **Base64 image uploads** (no file storage needed)
- ⚡ **Livewire** compatible with reactive data binding
- 🎯 **Filament** custom field and infolist components
- 🎭 **Blade components** for easy integration
- 🌓 **Dark mode** automatic detection and switching
- 📦 **Zero configuration** - works out of the box

## Installation

Install via Composer:

```bash
composer require waterloobae/tinymce
```

The package will auto-register via Laravel's package discovery.

### Optional: Publish Views

If you want to customize the views:

```bash
php artisan vendor:publish --tag=tinymce-views
```

This will publish views to `resources/views/vendor/tinymce/`.

## Usage

### 1. Filament Form Field

Use in your Filament resource schemas:

```php
use Waterloobae\TinyMce\Forms\Components\TinyEditor;

// In your resource schema
TinyEditor::make('description')
    ->profile('simple') // 'minimal', 'simple', or 'full'
    ->columnSpanFull();
```

**Profiles:**

- `minimal` - Basic formatting (bold, italic, lists, links)
- `simple` - Standard features (formatting, images, tables, code)
- `full` - All features (media, code samples, preview, etc.)

**Custom Configuration:**

```php
TinyEditor::make('content')
    ->plugins(['lists', 'link', 'image', 'table', 'code'])
    ->toolbar('bold italic | bullist numlist | link image')
    ->columnSpanFull();
```

### 2. Filament Infolist Entry (View)

Display HTML with LaTeX rendering in Filament view pages:

```php
use Waterloobae\TinyMce\Infolists\Components\HtmlWithLatex;

// In your infolist schema
HtmlWithLatex::make('description')
    ->label('Description')
    ->columnSpanFull();
```

Or use the standard TextEntry with custom view:

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('description')
    ->view('tinymce::infolists.components.html-with-latex')
    ->columnSpanFull();
```

### 3. Livewire Component

Use in any Livewire component:

```blade
<x-tinymce::tinymce-editor 
    wire:model="description" 
    profile="simple"
    height="400"
/>
```

**With Livewire properties:**

```php
// Component class
class EditArticle extends Component
{
    public string $content = '';
    
    public function save()
    {
        Article::create([
            'content' => $this->content,
        ]);
    }
}
```

```blade
<!-- Component view -->
<form wire:submit="save">
    <x-tinymce::tinymce-editor 
        wire:model="content" 
        profile="full"
    />
    
    <button type="submit">Save</button>
</form>
```

### 4. Regular Blade Forms

Use in standard Laravel Blade forms (non-Livewire):

```blade
<form action="{{ route('articles.store') }}" method="POST">
    @csrf
    
    <x-tinymce::tinymce-editor 
        name="description" 
        :value="old('description', $article->description ?? '')"
        profile="simple"
        height="300"
    />
    
    <button type="submit">Submit</button>
</form>
```

### 5. Display HTML with LaTeX (Blade Component)

Render HTML content with LaTeX equations:

```blade
<x-tinymce::html-with-latex :content="$article->description" />
```

**With custom classes:**

```blade
<x-tinymce::html-with-latex 
    :content="$content" 
    class="my-4 text-lg" 
/>
```

## LaTeX Support

The package supports both inline and display math equations using standard LaTeX syntax:

**Inline equations:**
```
Einstein's famous equation: $E = mc^2$
Alternative syntax: \(E = mc^2\)
```

**Display equations:**
```
$$
\int_{a}^{b} f(x)dx
$$

Alternative syntax:
\[
\int_{a}^{b} f(x)dx
\]
```

## Examples

### Complete Filament Resource Example

```php
<?php

namespace App\Filament\Resources;

use Waterloobae\TinyMce\Forms\Components\TinyEditor;
use Waterloobae\TinyMce\Infolists\Components\HtmlWithLatex;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ArticleResource extends Resource
{
    // Form Schema
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TinyEditor::make('body')
                            ->label('Article Body')
                            ->profile('full')
                            ->columnSpanFull()
                            ->required(),
                    ]),
            ]);
    }
    
    // View Schema
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        HtmlWithLatex::make('body')
                            ->label('Article Body')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
```

### Complete Livewire Example

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class ArticleEditor extends Component
{
    public string $title = '';
    public string $content = '';
    
    public function mount($article = null)
    {
        if ($article) {
            $this->title = $article->title;
            $this->content = $article->content;
        }
    }
    
    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        // Save logic here
    }
    
    public function render()
    {
        return view('livewire.article-editor');
    }
}
```

```blade
<!-- resources/views/livewire/article-editor.blade.php -->
<div>
    <form wire:submit="save">
        <div class="mb-4">
            <label for="title">Title</label>
            <input type="text" wire:model="title" id="title" class="form-input">
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-4">
            <label for="content">Content</label>
            <x-tinymce::tinymce-editor 
                wire:model="content" 
                profile="full"
                height="500"
            />
            @error('content') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">
            Save Article
        </button>
    </form>
</div>
```

## Component Options

### TinyMCE Editor Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `name` | string | 'content' | Form field name |
| `value` | string | '' | Initial value |
| `wire:model` | string | null | Livewire property binding |
| `profile` | string | 'simple' | Editor profile (minimal/simple/full) |
| `height` | int | 400 | Editor height in pixels |

### HtmlWithLatex Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `content` | string | '' | HTML content to render |
| `class` | string | 'prose prose-sm dark:prose-invert max-w-none' | CSS classes |

## Features in Detail

### Base64 Image Handling

Images are automatically converted to base64 when:
- Pasting images from clipboard
- Dragging and dropping images
- Selecting images via file picker

This eliminates the need for file storage and makes content portable.

### Dark Mode Support

The editor automatically detects your application's dark mode:
- Applies `oxide-dark` skin in dark mode
- Applies `oxide` skin in light mode
- Content styling adjusts automatically

### Profiles

**Minimal Profile:**
- Plugins: lists, link
- Toolbar: bold, italic, bullets, numbers, links
- Perfect for: Comments, short descriptions

**Simple Profile (Default):**
- Plugins: lists, link, image, table, code
- Toolbar: formatting, lists, links, images, code
- Perfect for: Blog posts, articles, general content

**Full Profile:**
- Plugins: all features including media, code samples, preview
- Toolbar: complete formatting options
- Perfect for: Documentation, complex content

## Updating Your Existing Code

If you're migrating from local components to this package:

### Filament Forms

**Before:**
```php
use App\Filament\Forms\Components\TinyEditor;
```

**After:**
```php
use Waterloobae\TinyMce\Forms\Components\TinyEditor;
```

### Filament Infolists

**Before:**
```php
TextEntry::make('cotent')
    ->view('filament.infolists.components.html-with-latex')
```

**After:**
```php
use Waterloobae\TinyMce\Infolists\Components\HtmlWithLatex;

HtmlWithLatex::make('content')
```

### Blade Components

**Before:**
```blade
<x-tinymce-editor wire:model="content" />
<x-html-with-latex :content="$content" />
```

**After:**
```blade
<x-tinymce::tinymce-editor wire:model="content" />
<x-tinymce::html-with-latex :content="$content" />
```

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Livewire 3.x
- Alpine.js (typically included with Livewire)
- Filament 3.x or 4.x (optional, only if using Filament components)

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- TinyMCE 6 browser support
- MathJax 3 browser support

## CDN Resources

This package uses the following CDN resources:
- TinyMCE 6.8.2 from jsdelivr.net
- MathJax 3 from jsdelivr.net

## License

MIT License. See LICENSE file for details.

## Credits

- **TinyMCE** - https://www.tiny.cloud/
- **MathJax** - https://www.mathjax.org/
- **Laravel** - https://laravel.com/
- **Livewire** - https://livewire.laravel.com/
- **Filament** - https://filamentphp.com/

## Support

For issues, questions, or contributions, please visit:
https://github.com/waterloobae/tinymce

## Changelog

### 1.0.0 (2025-10-23)
- Initial release
- TinyMCE 6 integration
- MathJax 3 support
- Filament components
- Livewire support
- Blade components
- Dark theme support
- Base64 image handling
