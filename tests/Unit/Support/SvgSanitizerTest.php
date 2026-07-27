<?php

use Guava\IconPicker\Support\SvgSanitizer;

function sanitize(string $contents): ?string
{
    return (new SvgSanitizer)->sanitize($contents);
}

it('keeps the geometry of an ordinary icon', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />'
        . '</svg>';

    expect(sanitize($svg))
        ->toContain('d="M12 6v12m6-6H6"')
        ->toContain('viewBox="0 0 24 24"')
        ->toContain('stroke="currentColor"')
        ->toContain('stroke-linecap="round"')
    ;
});

it('strips script elements', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.cookie)</script><path d="M0 0" /></svg>';

    expect(sanitize($svg))
        ->not->toContain('script')
        ->not->toContain('alert')
        ->toContain('d="M0 0"')
    ;
});

it('strips event handler attributes', function (string $attribute) {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0" ' . $attribute . '="alert(1)" /></svg>';

    expect(sanitize($svg))
        ->not->toContain($attribute)
        ->not->toContain('alert(1)')
    ;
})->with(['onload', 'onclick', 'onmouseover', 'onbegin', 'onfocusin']);

it('strips elements that can run or fetch something', function (string $element) {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><' . $element . ' /><path d="M0 0" /></svg>';

    expect(sanitize($svg))->not->toContain($element);
})->with(['foreignObject', 'animate', 'set', 'image', 'use', 'style', 'handler', 'iframe']);

it('strips href and namespaced attributes', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
        . '<path d="M0 0" xlink:href="javascript:alert(1)" href="https://evil.test/x.svg" />'
        . '</svg>';

    expect(sanitize($svg))
        ->not->toContain('href')
        ->not->toContain('javascript')
        ->not->toContain('evil.test')
    ;
});

it('strips the style attribute', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0" style="background:url(javascript:alert(1))" /></svg>';

    expect(sanitize($svg))->not->toContain('style')->not->toContain('javascript');
});

it('keeps a local url() reference but drops a remote one', function () {
    $local = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0" fill="url(#gradient)" /></svg>';
    $remote = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0" fill="url(https://evil.test/x)" /></svg>';

    expect(sanitize($local))->toContain('url(#gradient)')
        ->and(sanitize($remote))->not->toContain('evil.test')
    ;
});

it('strips comments, which can hide markup from a later parser', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><!--<script>alert(1)</script>--><path d="M0 0" /></svg>';

    expect(sanitize($svg))->not->toContain('script')->not->toContain('alert');
});

it('refuses a file that declares entities', function () {
    $svg = '<?xml version="1.0"?>'
        . '<!DOCTYPE svg [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
        . '<svg xmlns="http://www.w3.org/2000/svg"><path d="&xxe;" /></svg>';

    expect(sanitize($svg))->toBeNull();
});

it('refuses anything that is not an svg', function (string $contents) {
    expect(sanitize($contents))->toBeNull();
})->with([
    'empty' => '',
    'whitespace' => "  \n ",
    'not xml' => '<?php echo 1;',
    'malformed' => '<svg><path d="M0 0"></svg>',
    'html' => '<html><body><script>alert(1)</script></body></html>',
    'wrong root' => '<div xmlns="http://www.w3.org/1999/xhtml"><p>hi</p></div>',
]);
