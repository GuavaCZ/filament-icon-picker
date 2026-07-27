<?php

namespace Guava\IconPicker\Support;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Reduces an uploaded SVG to the elements and attributes below. Anything else is dropped.
 */
class SvgSanitizer
{
    /**
     * @var array<string>
     */
    protected const ELEMENTS = [
        'svg', 'g', 'defs', 'title', 'desc',
        'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'pattern',
    ];

    /**
     * @var array<string>
     */
    protected const ATTRIBUTES = [
        'xmlns', 'viewbox', 'preserveaspectratio', 'width', 'height', 'id', 'class',
        'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry', 'd', 'points',
        'transform', 'color', 'opacity', 'vector-effect',
        'fill', 'fill-rule', 'fill-opacity',
        'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit',
        'stroke-dasharray', 'stroke-dashoffset', 'stroke-opacity',
        'clip-path', 'clip-rule', 'clippathunits', 'mask', 'maskunits',
        'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform',
        'patternunits', 'patterncontentunits', 'patterntransform',
    ];

    /**
     * Returns null if the file is not usable as an icon.
     */
    public function sanitize(string $contents): ?string
    {
        if (trim($contents) === '') {
            return null;
        }

        $internalErrors = libxml_use_internal_errors(true);

        $document = new DOMDocument;
        $document->preserveWhiteSpace = false;

        $loaded = $document->loadXML($contents, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        if (! $loaded) {
            return null;
        }

        // Entities can only be declared in a doctype. An icon needs neither.
        if ($document->doctype !== null) {
            return null;
        }

        $root = $document->documentElement;

        if (! $root instanceof DOMElement || strtolower($root->nodeName) !== 'svg') {
            return null;
        }

        $this->cleanElement($root);

        $markup = $document->saveXML($root);

        return ($markup === false) ? null : $markup;
    }

    protected function cleanElement(DOMElement $element): void
    {
        foreach (iterator_to_array($element->childNodes, false) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if ($child instanceof DOMElement && $this->isAllowedElement($child)) {
                $this->cleanElement($child);

                continue;
            }

            // Disallowed elements, comments and processing instructions.
            $this->remove($child);
        }

        foreach (iterator_to_array($element->attributes, false) as $attribute) {
            if (! $this->isAllowedAttribute($attribute)) {
                $element->removeAttributeNode($attribute);
            }
        }
    }

    protected function isAllowedElement(DOMElement $element): bool
    {
        return in_array(strtolower($element->nodeName), static::ELEMENTS, true);
    }

    protected function isAllowedAttribute(DOMAttr $attribute): bool
    {
        $name = strtolower($attribute->nodeName);

        // xlink:href and friends get past an allowlist that only knows local names.
        if (str_contains($name, ':')) {
            return false;
        }

        if (! in_array($name, static::ATTRIBUTES, true)) {
            return false;
        }

        // Allow local references only, like fill="url(#gradient)".
        return preg_match('/url\s*\(\s*[\'"]?\s*(?!#)/i', $attribute->value) !== 1;
    }

    protected function remove(DOMNode $node): void
    {
        $node->parentNode?->removeChild($node);
    }
}
