@php
    $id = $getId();
    $statePath = $getStatePath();
    $plugins = implode(' ', $getPlugins());
    $toolbar = $getToolbar();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="tinyEditorComponent({
        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
        statePath: '{{ $statePath }}',
        id: '{{ $id }}',
        plugins: '{{ $plugins }}',
        toolbar: '{{ $toolbar }}'
    })" wire:ignore>
        <textarea 
            id="{{ $id }}"
            x-ref="editor"
            {{ $attributes->merge($getExtraAttributes(), escape: false) }}
        ></textarea>
    </div>
</x-dynamic-component>

@once
    @push('scripts')
    {{-- TinyMCE Core --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tinyEditorComponent', (config) => ({
                state: config.state,
                editor: null,

                init() {
                    this.initTinyMCE();
                },

                initTinyMCE() {
                    const self = this;

                    // Wait for TinyMCE to load
                    const checkTinyMCE = setInterval(() => {
                        if (typeof tinymce !== 'undefined') {
                            clearInterval(checkTinyMCE);

                            // Detect dark mode
                            const isDark = document.documentElement.classList.contains('dark');

                            tinymce.init({
                                target: this.$refs.editor,
                                plugins: config.plugins,
                                toolbar: config.toolbar,
                                menubar: false,
                                height: 400,
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
                                        if (self.state) {
                                            editor.setContent(self.state);
                                        }
                                    });

                                    // Sync changes to Livewire - use blur and change for better performance
                                    editor.on('blur change', function() {
                                        const content = editor.getContent();
                                        self.state = content;
                                        // Force sync to Livewire
                                        self.$wire.set(config.statePath, content);
                                    });

                                    // Watch for external state changes
                                    self.$watch('state', (newValue) => {
                                        if (editor.getContent() !== newValue) {
                                            editor.setContent(newValue || '');
                                        }
                                    });
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
            }));
        });
    </script>
    @endpush
@endonce
