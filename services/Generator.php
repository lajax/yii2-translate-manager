<?php

namespace lajax\translatemanager\services;

use Yii;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use lajax\translatemanager\models\LanguageSource;

/**
 * Generator class for producing JavaScript files containing language elements.
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class Generator
{
    /**
     * Location of generated language files.
     */
    private $_basePath;

    /**
     * @var string Language of current translation. The JavaScript language file will be generated in this language.
     */
    private $_languageId;

    /**
     * @var array The array containing the language elements. [md5('Framework') => 'Framework']
     */
    private $_languageItems = [];

    /**
     * @var string JavaScript code for serving all language elements.
     */
    private $_template = 'var languageItems = languageItems || new Object();  languageItems[\'{language_id}\']=(function(){var _languages={language_items};return{getLanguageItems:function(){return _languages;}};})();';

    /**
     * @param \lajax\translatemanager\Module $module
     * @param string $language_id Language of the file to be generated.
     */
    public function __construct($module, $language_id)
    {
        $this->_languageId = $language_id;
        $this->_basePath = Yii::getAlias($module->tmpDir);
        if (!is_dir($this->_basePath)) {
            throw new InvalidConfigException("The directory does not exist: {$this->_basePath}");
        } elseif (!is_writable($this->_basePath)) {
            throw new InvalidConfigException("The directory is not writable by the Web process: {$this->_basePath}");
        }

        $this->_basePath = $module->getLanguageItemsDirPath();
        if (!is_dir($this->_basePath)) {
            mkdir($this->_basePath);
        }

        if (!is_writable($this->_basePath)) {
            throw new InvalidConfigException("The directory is not writable by the Web process: {$this->_basePath}");
        }
    }

    /**
     * Generating JavaScript language file.
     *
     * @return int
     *
     * @deprecated since version 1.4
     */
    public function generate()
    {
        return $this->run();
    }

    /**
     * Generating JavaScript language file.
     *
     * @return int
     */
    public function run()
    {
        $this->_generateJSFile();

        return count($this->_languageItems);
    }

    /**
     * Creating JavaScript language file in current language.
     */
    private function _generateJSFile()
    {
        $this->_loadLanguageItems();

        $data = [];
        foreach ($this->_languageItems as $language_item) {
            $data[md5($language_item->message)] = $language_item->languageTranslate->translation;
        }

        $langs = \lajax\translatemanager\models\Language::findAll(['status' => \lajax\translatemanager\models\Language::STATUS_ACTIVE]);

        foreach ($langs as $key => $lang) {
            $filename = $this->_basePath . '/' . $lang->language_id . '.js';
            $file_contents = str_replace('{language_items}', Json::encode($data), $this->_template);
            $file_contents = str_replace('{language_id}', $lang->language_id, $file_contents);
            if (!$key) {//first file  should contain `language` var with current language Id
                $file_contents .= 'var language = "' . $this->_languageId . '"';
            }

            file_put_contents($filename, $file_contents);
        }
    }

    /**
     * Loads language elements in JavaScript category.
     */
    private function _loadLanguageItems()
    {
        $this->_languageItems = LanguageSource::find()
            ->joinWith(['languageTranslate' => function ($query) {
                $query->where(['language' => $this->_languageId]);
            },
            ])
            ->where(['category' => Scanner::CATEGORY_JAVASCRIPT])
            ->all();
    }

    /**
     * @return string returns the language id of the translation.
     */
    public function getLanguageId()
    {
        return $this->_languageId;
    }

    /**
     * @param string $language_id Stores the language id of the translation.
     */
    public function setLanguageId($language_id)
    {
        $this->_languageId = $language_id;
    }
}
