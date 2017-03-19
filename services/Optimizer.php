<?php

namespace lajax\translatemanager\services;

use yii\helpers\Console;
use lajax\translatemanager\models\LanguageSource;

/**
 * Optimizer class for optimizing database tables
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class Optimizer
{
    /**
     * @var Scanner
     */
    private $_scanner;

    /**
     * @var array a Current language elements in the translating system
     */
    private $_languageElements = [];

    /**
     * Removing unused language elements from database.
     *
     * @return int Number of unused language elements detected.
     *
     * @deprecated since version 1.4
     */
    public function optimization()
    {
        return $this->run();
    }

    /**
     * Removing unused language elements from database.
     *
     * @return int The number of removed language elements.
     */
    public function run()
    {
        $this->_scanner = new Scanner();
        $this->_scanner->run();
        $this->_scanner->stdout('Deleted language elements - BEGIN', Console::FG_RED);

        $languageSourceIds = $this->_scanner->getRemovableLanguageSourceIds();

        $this->_initLanguageElements($languageSourceIds);

        LanguageSource::deleteAll(['id' => $languageSourceIds]);

        $this->_scanner->stdout('Deleted language elements - END', Console::FG_RED);

        return count($languageSourceIds);
    }

    /**
     * Returns removed language elements.
     *
     * @return array
     */
    public function getRemovedLanguageElements()
    {
        return $this->_languageElements;
    }

    /**
     * Initializing $_languageElements array.
     *
     * @param array $languageSourceIds
     */
    private function _initLanguageElements($languageSourceIds)
    {
        $languageSources = LanguageSource::findAll(['id' => $languageSourceIds]);
        foreach ($languageSources as $languageSource) {
            $this->_languageElements[$languageSource->category][$languageSource->message] = $languageSource->id;

            $category = Console::ansiFormat($languageSource->category, [Console::FG_RED]);
            $message = Console::ansiFormat($languageSource->message, [Console::FG_RED]);

            $this->_scanner->stdout('category: ' . $category . ', message: ' . $message);
        }
    }
}
