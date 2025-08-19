<?php

use Illuminate\Support\Str;

function truncate(string $text, $limit = 50): String
{
    return Str::limit($text, $limit, '...');
}
