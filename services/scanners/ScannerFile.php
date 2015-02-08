<?php

namespace lajax\translatemanager\services\scanners;

use Yii;
use yii\helpers\FileHelper;
use lajax\translatemanager\services\Scanner;

/**
 * Class for processing PHP and JavaScript files.
 * Language elements detected in JavaScript files:
 * 
 * ~~~
 * lajax.t('language element);
 * lajax.t('language element {replace}', {replace:'String'});
 * lajax.t("language element");
 * lajax.t("language element {replace}", {replace:'String'});
 * ~~~
 * 
 * Language elements detected in PHP files:
 * "t" functions:
 * 
 * ~~~
 * ::t('category of language element', 'language element');
 * ::t('category of language element', 'language element {replace}', ['replace' => 'String']);
 * ::t('category of language element', "language element");
 * ::t('category of language element', "language element {replace}", ['replace' => 'String']);
 * ~~~
 * 
 * Language elements detected in constant arrays:
 * 
 * ~~~
 *  /**
 *   * @translate
 *   *\/
 *  private $_GENDERS = ['Male', 'Female'];
 *  /**
 *   * @translate
 *   *\/
 *   private $_STATUSES = [
 *      self::STATUS_ACTIVE => 'Active',
 *      self::STATUS_INACTIVE => 'Inactive'
 *   ];
 * ~~~
 * 
 * Translation of constant arrays:
 * Translation to site language:
 * 
 * ~~~
 * $genders = \lajax\translatemanager\helpers\Language::a($this->_GENDERS);
 * ~~~
 * 
 * Translating to the language of your coice:
 * 
 * ~~~
 * $statuses = \lajax\translatemanager\helpers\Language::a($this->_STATUSES, [], 'de_DE');
 * ~~~
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.1
 */
abstract class ScannerFile extends \yii\console\controllers\MessageController {

    /**
     * Extension of PHP files.
     */
    const EXTENSION = '*.php';

    /**
     * @var Scanner object.
     */
    public $scanner;

    /**
     * @var \lajax\translatemanager\Module TranslateManager Module
     */
    public $module;

    /**
     * @var array Array to store patsh to project files.
     */
    protected static $files = ['*.php' => [], '*.js' => []];

    /**
     * @param Scanner $scanner
     */
    public function __construct(Scanner $scanner) {
        parent::__construct('language', Yii::$app->getModule('translatemanager'), [
            'scanner' => $scanner
        ]);
    }

    /**
     * @inheritdoc Initialise the $files static array.
     */
    public function init() {

        if (empty(self::$files[static::EXTENSION]) && in_array(static::EXTENSION, $this->module->patterns)) {
            self::$files[static::EXTENSION] = FileHelper::findFiles(realpath($this->_getRoot()), [
                        'except' => $this->module->ignoredItems,
                        'only' => [static::EXTENSION],
            ]);
        }

        parent::init();
    }

    /**
     * Extracts messages from a file
     *
     * @param string $fileName name of the file to extract messages from
     * @param array $options Definition of the parameters required to identify language elements.
     * example: 
     * ~~~
     * [
     *      'translator' => ['Yii::t', 'Lx::t'],
     *      'begin' => '(',
     *      'end' => ')'
     * ]
     * ~~~
     */
    protected function extractMessages($fileName, $options) {
        $subject = file_get_contents($fileName);
        if (static::EXTENSION !== '*.php') {
            $subject = "<?php\n" . $subject;
        }

        foreach ($options['translator'] as $currentTranslator) {
            $translatorTokens = token_get_all('<?php ' . $currentTranslator);
            array_shift($translatorTokens);

            $translatorTokensCount = count($translatorTokens);
            $matchedTokensCount = 0;
            $buffer = [];
            $tokens = token_get_all($subject);

            foreach ($tokens as $token) {
                // finding out translator call
                if ($matchedTokensCount < $translatorTokensCount) {
                    if ($this->tokensEqual($token, $translatorTokens[$matchedTokensCount])) {
                        $matchedTokensCount++;
                    } else {
                        $matchedTokensCount = 0;
                    }
                } elseif ($matchedTokensCount === $translatorTokensCount) {
                    // translator found
                    // end of translator call or end of something that we can't extract
                    if ($this->tokensEqual($options['end'], $token)) {

                        $languageItems = $this->getLanguageItem($buffer);
                        if ($languageItems) {
                            $this->scanner->addLanguageItems($languageItems);
                        }

                        // prepare for the next match
                        $matchedTokensCount = 0;
                        $buffer = [];
                    } elseif ($token !== $options['begin'] && isset($token[0]) && !in_array($token[0], [T_WHITESPACE, T_COMMENT])) {
                        // ignore comments, whitespaces and beginning of function call
                        $buffer[] = $token;
                    }
                }
            }
        }
    }

    /**
     * Returns language elements in the token buffer.
     * If there is no recognisable language element in the array, returns null.
     */
    abstract protected function getLanguageItem($buffer);

    /**
     * Returns the root directory of the project.
     * @return string
     */
    private function _getRoot() {
        $directories = explode('/', Yii::getAlias($this->module->root));
        array_pop($directories);
        return implode('/', $directories);
    }

    /**
     * Determines whether the category received as a parameter can be processed.
     * @param string $category
     * @return boolean
     */
    protected function isValidCategory($category) {
        return !in_array($category, $this->module->ignoredCategories);
    }

}
