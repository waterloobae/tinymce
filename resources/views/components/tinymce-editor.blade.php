@props([
    'name' => 'content',
    'value' => '',
    'wireModel' => null,
    'profile' => 'simple', // 'minimal', 'simple', 'full'
    'height' => 400,
])

@php
    $id = $attributes->get('id', 'tinymce-' . uniqid());
    $isLivewire = $wireModel !== null || $attributes->wire('model')->value();
    $wireModelValue = $wireModel ?? $attributes->wire('model')->value() ?? $name;
    
    // Define plugins based on profile
    $plugins = match($profile) {
        'minimal' => 'lists link',
        'simple' => 'lists link image table code',
        'full' => 'lists link image table code codesample media preview',
        default => 'lists link image table code',
    };
    
    // Define toolbar based on profile
    $toolbar = match($profile) {
        'minimal' => 'bold italic | bullist numlist | link',
        'simple' => 'bold italic underline | bullist numlist | link image | code',
        'full' => 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media table | code codesample | preview',
        default => 'bold italic underline | bullist numlist | link image table | code',
    };
@endphp

<div 
    x-data="tinyMceComponent({
        id: '{{ $id }}',
        name: '{{ $name }}',
        @if($isLivewire)
        value: @entangle($wireModelValue),
        @else
        value: {{ json_encode($value) }},
        @endif
        isLivewire: {{ $isLivewire ? 'true' : 'false' }},
        plugins: '{{ $plugins }}',
        toolbar: '{{ $toolbar }}',
        height: {{ $height }}
    })"
    x-init="initTinyMCE()"
    @if($isLivewire) wire:ignore @endif
    {{ $attributes->except(['id', 'wire:model']) }}
>
    <textarea 
        id="{{ $id }}"
        name="{{ $name }}"
        x-ref="editor"
    >{{ $value }}</textarea>
</div>

@once
    @push('scripts')
    {{-- TinyMCE Core --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        function tinyMceComponent(config) {
            return {
                editor: null,
                value: config.value,
                isLivewire: config.isLivewire,

                initTinyMCE() {
                    const self = this;
                    const isDark = document.documentElement.classList.contains('dark');

                    // Wait for TinyMCE to load
                    const checkTinyMCE = setInterval(() => {
                        if (typeof tinymce !== 'undefined') {
                            clearInterval(checkTinyMCE);

                            tinymce.init({
                                target: this.$refs.editor,
                                plugins: config.plugins,
                                toolbar: config.toolbar,
                                menubar: false,
                                height: config.height,
                                branding: false,
                                promotion: false,
                                
                                // Dark theme support
                                skin: isDark ? 'oxide-dark' : 'oxide',
                                content_css: isDark ? 'dark' : 'default',
                                
                                // Base64 image handling
                                paste_data_images: true,
                                automatic_uploads: false,
                                images_upload_handler: function (blobInfo, progress) {
                                    return new Promise((resolve, reject) => {
                                        const reader = new FileReader();
                                        reader.onloadend = function() {
                                            resolve(reader.result);
                                        };
                                        reader.onerror = reject;
                                        reader.readAsDataURL(blobInfo.blob());
                                    });
                                },

                                // File picker for images
                                file_picker_types: 'image',
                                file_picker_callback: function(callback, value, meta) {
                                    if (meta.filetype === 'image') {
                                        const input = document.createElement('input');
                                        input.setAttribute('type', 'file');
                                        input.setAttribute('accept', 'image/*');

                                        input.onchange = function() {
                                            const file = this.files[0];
                                            const reader = new FileReader();

                                            reader.onload = function() {
                                                callback(reader.result, {
                                                    alt: file.name
                                                });
                                            };

                                            reader.readAsDataURL(file);
                                        };

                                        input.click();
                                    }
                                },

                                // Content handling
                                setup: function(editor) {
                                    self.editor = editor;

                                    // Set initial content
                                    editor.on('init', function() {
                                        if (self.value) {
                                            editor.setContent(self.value);
                                        }
                                    });

                                    // Sync changes
                                    editor.on('blur change', function() {
                                        const content = editor.getContent();
                                        self.value = content;
                                        
                                        // Update textarea for non-Livewire forms
                                        if (!self.isLivewire) {
                                            self.$refs.editor.value = content;
                                        }
                                    });

                                    // Watch for external state changes (Livewire only)
                                    if (self.isLivewire) {
                                        self.$watch('value', (newValue) => {
                                            if (editor.getContent() !== newValue) {
                                                editor.setContent(newValue || '');
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }, 100);
                },

                destroy() {
                    if (this.editor) {
                        tinymce.remove(this.editor);
                    }
                }
            }
        }
    </script>
    @endpush
@endonce
