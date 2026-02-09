@props(['content'])

<div 
    {{ $attributes->merge(['class' => 'prose prose-sm dark:prose-invert max-w-none']) }}
    x-data="{ 
        rendered: false,
        renderLatex() {
            if (typeof MathJax !== 'undefined' && MathJax.typesetPromise && !this.rendered) {
                MathJax.typesetPromise([$el]).then(() => {
                    this.rendered = true;
                }).catch((err) => console.log('MathJax error:', err));
            } else if (!this.rendered) {
                setTimeout(() => this.renderLatex(), 100);
            }
        }
    }"
    x-init="renderLatex()"
>
    {!! $content !!}
</div>

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
