<?php

namespace Waterloobae\TinyMce\Forms\Components;

use Filament\Forms\Components\Field;

class TinyEditor extends Field
{
    protected string $view = 'tinymce::forms.components.tiny-editor';

    protected string $profile = 'default';
    
    protected array $plugins = [];
    
    protected string $toolbar = '';

    public function profile(string $profile): static
    {
        $this->profile = $profile;
        
        return $this;
    }

    public function getProfile(): string
    {
        return $this->profile;
    }

    public function plugins(array $plugins): static
    {
        $this->plugins = $plugins;
        
        return $this;
    }

    public function getPlugins(): array
    {
        if (!empty($this->plugins)) {
            return $this->plugins;
        }

        // Default plugins based on profile
        return match($this->profile) {
            'minimal' => ['lists', 'link'],
            'simple' => ['lists', 'link', 'image', 'table', 'code'],
            'full' => ['lists', 'link', 'image', 'table', 'code', 'codesample', 'media', 'preview'],
            default => ['lists', 'link', 'image', 'table', 'code'],
        };
    }

    public function toolbar(string $toolbar): static
    {
        $this->toolbar = $toolbar;
        
        return $this;
    }

    public function getToolbar(): string
    {
        if (!empty($this->toolbar)) {
            return $this->toolbar;
        }

        // Default toolbar based on profile
        return match($this->profile) {
            'minimal' => 'bold italic | bullist numlist | link',
            'simple' => 'bold italic underline | bullist numlist | link image | code',
            'full' => 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media table | code codesample | preview',
            default => 'bold italic underline | bullist numlist | link image table | code',
        };
    }
}
