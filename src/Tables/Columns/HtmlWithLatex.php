<?php

namespace Waterloobae\TinyMce\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class HtmlWithLatex extends TextColumn
{
    protected string $view = 'tinymce::tables.columns.html-with-latex';
}
