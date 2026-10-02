<?php

namespace App\Libraries;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;

final class SafeHtml
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u',
        'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'blockquote', 'a', 'hr',
    ];

    private const DROP_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'form',
        'input', 'button', 'textarea', 'select', 'option', 'svg', 'math',
    ];

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! str_contains($html, '<')) {
            return nl2br(self::escape($html));
        }

        if (! class_exists(DOMDocument::class)) {
            return self::fallback($html);
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');

        $loaded = $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div id="minsix-rich-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return self::fallback($html);
        }

        $root = $dom->getElementById('minsix-rich-root');
        if (! $root) {
            return self::fallback($html);
        }

        self::cleanChildren($root);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $dom->saveHTML($child);
        }

        return trim($out);
    }

    public static function renderStored(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        return self::sanitize($value);
    }

    private static function cleanChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node instanceof DOMComment) {
                $parent->removeChild($node);
                continue;
            }

            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, self::DROP_TAGS, true)) {
                $parent->removeChild($node);
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                self::cleanChildren($node);
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            self::cleanElement($node, $tag);
            self::cleanChildren($node);
        }
    }

    private static function cleanElement(DOMElement $element, string $tag): void
    {
        $href = $tag === 'a' ? trim($element->getAttribute('href')) : '';
        $target = $tag === 'a' ? trim($element->getAttribute('target')) : '';

        while ($element->attributes->length > 0) {
            $element->removeAttributeNode($element->attributes->item(0));
        }

        if ($tag !== 'a' || $href === '' || ! self::safeHref($href)) {
            return;
        }

        $element->setAttribute('href', $href);

        if ($target === '_blank') {
            $element->setAttribute('target', '_blank');
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function safeHref(string $href): bool
    {
        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private static function fallback(string $html): string
    {
        $html = preg_replace(
            '~<(script|style|iframe|object|embed|form|input|button|textarea|select|option|svg|math)\b[^>]*>.*?</\1>~is',
            '',
            $html
        ) ?? '';

        $html = strip_tags($html, '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><a><hr>');

        // Fallback strips all attributes rather than attempting to preserve URLs.
        $html = preg_replace('/<([a-z0-9]+)\b[^>]*>/i', '<$1>', $html) ?? '';

        return trim($html);
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
