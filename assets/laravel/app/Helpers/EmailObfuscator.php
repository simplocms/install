<?php

namespace App\Helpers;

/**
 * Class EmailObfuscator - for protecting emails on the web before email-harvesting robots.
 * @package App\Helpers
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
abstract class EmailObfuscator
{
    const PRINT_METHOD_RAW = '_raw_';
    const PRINT_METHOD_JS = '_js_';
    const PRINT_METHOD_CSS = '_css_';
    const PRINT_METHOD_ENCODE = '_encode_';

    /**
     * Return safe mailable link.
     *
     * @param string $email
     * @param array $attributes - HTML attributes of the link element
     * @param string $print - which method will be used to print email ['raw', 'js', 'css', 'encode'] or text to be printed as link
     * @return string
     */
    public static function makeLink(
        string $email,
        array $attributes = [],
        $print = self::PRINT_METHOD_JS
    ): string
    {
        $joinExpression = self::toJsExpression($email, "'");
        $attributes['href'] = $attributes['href'] ?? "mailto:";

        $attributePairs = [];
        foreach ($attributes as $name => $value) {
            $attributePairs[] = "$name=\"$value\"";
        }

        $attributesString = join(' ', $attributePairs);
        $safePrint = $print;

        switch ($print) {
            case self::PRINT_METHOD_JS:
                $safePrint = self::safeJsPrint($email);
                break;
            case self::PRINT_METHOD_CSS:
                $safePrint = self::safeCssPrint($email);
                break;
            case self::PRINT_METHOD_ENCODE:
                $safePrint = self::encode($email);
                break;
        }

        return "<a $attributesString onclick=\"event.preventDefault();window.location.href='mailto:' + $joinExpression;\">$safePrint</a>";
    }


    /**
     * For safe printing emails on the spot using javascript.
     * Returns something like "<script>document.write('te'+'st'+y@'+'em'+'ai'+'l.'+'cz')</script>"
     *
     * @param string $email
     * @param int $chunkSize
     * @return string
     */
    public static function safeJsPrint(string $email, int $chunkSize = 2): string
    {
        $joinExpression = self::toJsExpression($email, "'", $chunkSize);
        return "<script>document.write($joinExpression)</script>";
    }


    /**
     * For safe printing emails on the spot using reversed text and CSS.
     * Returns something like "<span style='unicode-bidi:bidi-override;direction:rtl;'>zc.liame@ytset</span>"
     *
     * @param string $email
     * @param string $tag
     * @return string
     */
    public static function safeCssPrint(string $email, string $tag = 'span'): string
    {
        $rules = [
            'unicode-bidi:bidi-override;',
            'direction:rtl;'
        ];

        return "<$tag style='" . join($rules) . "'>" . strrev($email) . "</$tag>";
    }


    /**
     * Encodes input with HTML tags randomly.
     * Returns something like "t&#101;sty&#64;ema&#105;&#108;.&#99;&#122;".
     *
     * @param string $email
     * @return string
     */
    public static function encode(string $email): string
    {
        $output = '';

        foreach (str_split($email) as $char) {
            if (rand(0, 1)) {
                $output .= '&#' . ord(strtolower($char)) . ';';
            } else {
                $output .= $char;
            }
        }

        return $output;
    }


    /**
     * For safe printing emails on the spot combines JS print and CSS reversed text print.
     *
     * @param string $email
     * @param string $tag
     * @param int $chunkSize
     * @return string
     */
    public static function safeComboPrint(string $email, string $tag = 'span', int $chunkSize = 2): string
    {
        $rules = [
            'unicode-bidi:bidi-override;',
            'direction:rtl;'
        ];

        return "<$tag style='" . join($rules) . "'>" . self::safeJsPrint(strrev($email), $chunkSize) . "</$tag>";
    }


    /**
     * Replaces characters '@' and '.' with specified string.
     * Returns something like "testy&#064;email&#046;cz".
     *
     * @param string $email
     * @param string $at - replacement for '@'
     * @param string $dot - replacement for '.'
     * @return string
     */
    public static function replace(string $email, $at = '&#064;', $dot = '&#046;'): string
    {
        $email = str_replace('@', $at, $email);
        return str_replace('.', $dot, $email);
    }


    /**
     * Transforms given string into javascript string join expression.
     * Returns something like "'te'+'st'+'y@'+'em'+'ai'+'l.'+'cz'".
     *
     * @param string $email
     * @param string $quote
     * @param int $chunkSize
     * @return string
     */
    public static function toJsExpression(string $email, string $quote = "'", int $chunkSize = 2): string
    {
        $quotedChunks = array_map(function (string $chunk) use ($quote) {
            return "{$quote}$chunk{$quote}";
        }, str_split($email, $chunkSize));

        return join('+', $quotedChunks);
    }
}
