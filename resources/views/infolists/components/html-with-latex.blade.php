<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div 
        x-data="{ 
            content: @js($getState()),
            rendered: false 
        }"
        x-init="
            // Wait for MathJax to load and typeset
            const renderLatex = () => {
                if (typeof MathJax !== 'undefined' && MathJax.typesetPromise && !rendered) {
                    MathJax.typesetPromise([$el]).then(() => {
                        rendered = true;
                    }).catch((err) => console.log('MathJax error:', err));
                } else if (!rendered) {
                    setTimeout(renderLatex, 100);
                }
            };
            renderLatex();
        "
        class="prose prose-sm dark:prose-invert max-w-none"
    >
        {!! $getState() !!}
    </div>
</x-dynamic-component>

@once
    @push('scripts')
    {{-- MathJax Configuration --}}
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
            }
        };
    </script>
    {{-- MathJax Library --}}
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @endpush
@endonce
