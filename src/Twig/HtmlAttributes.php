<?php

namespace Kematjaya\UploadBundle\Twig;

/**
 * Merangkai atribut HTML (nama => nilai) menjadi string yang aman disisipkan ke tag.
 */
final class HtmlAttributes
{
    public static function render(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $name => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $html[] = sprintf(
                '%s="%s"',
                htmlspecialchars(trim((string) $name), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8')
            );
        }

        return implode(' ', $html);
    }
}
