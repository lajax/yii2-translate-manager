<?php

namespace lajax\translatemanager\translation;

/**
 * A translator that can translate arbitrary text.
 *
 * @author moltam
 */
interface Translator
{
    /**
     * Translates the given text to the specified language.
     *
     * @param string $text The text to translate.
     * @param string $target The language code for translation.
     * @param string $source [optional]
     * <p>The language code of the source text. If not given, the translator tries to detect the language.</p>
     * @param string $format [optional]
     * <p>The format of the source text. Possible values:
     * - html: string with HTML markup,
     * - text: plain text without markup.
     * </p>
     *
     * @return string The translation.
     *
     * @throws Exception If the text cannot be translated, or an error occurred during translation.
     */
    public function translate($text, $target, $source = null, $format = 'html');

    /**
     * Detects the language of the specified text.
     *
     * @param string $text The analyzed text.
     *
     * @return string The language code for translation.
     *
     * @throws Exception If the language cannot be detected, or an error occurred during detection.
     */
    public function detect($text);

    /**
     * Returns the languages supported by the translator.
     *
     * @return string[] A list of language codes.
     */
    public function getLanguages();
}
