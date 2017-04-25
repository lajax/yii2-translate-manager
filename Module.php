<?php

namespace lajax\translatemanager;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * This is the main module class for the TranslateManager module.
 *
 * Initialisation example:
 *
 * Simple example:
 *
 * ~~~
 * 'modules' => [
 *     'translatemanager' => [
 *         'class' => 'lajax\translatemanager\Module',
 *     ],
 * ],
 * ~~~
 *
 * Complex example:
 *
 * ~~~
 * 'modules' => [
 *     'translatemanager' => [
 *         'class' => 'lajax\translatemanager\Module',
 *         'root' => '@app',               // The root directory of the project scan.
 *         'layout' => 'language',         // Name of the used layout. If using own layout use 'null'.
 *         'allowedIPs' => ['127.0.0.1'],  // IP addresses from which the translation interface is accessible.
 *         'roles' => ['@'],               // For setting access levels to the translating interface.
 *         'tmpDir' => '@runtime',         // Writable directory for the client-side temporary language files.
 *                                         // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
 *         'phpTranslators' => ['::t'],    // list of the php function for translating messages.
 *         'jsTranslators' => ['lajax.t'], // list of the js function for translating messages.
 *         'patterns' => ['*.js', '*.php'],// list of file extensions that contain language elements.
 *         'ignoredCategories' => ['yii'], // these categories won’t be included in the language database.
 *         'ignoredItems' => ['config'],   // these files will not be processed.
 *         'languageTable' => 'language',  // Name of the database table storing the languages.
 *         'scanTimeLimit' => null,        // increase to prevent "Maximum execution time" errors, if null the default max_execution_time will be used
 *         'searchEmptyCommand' => '!',    // the search string to enter in the 'Translation' search field to find not yet translated items, set to null to disable this feature
 *         'defaultExportStatus' => 1,     // the default selection of languages to export, set to 0 to select all languages by default
 *         'defaultExportFormat' => 'json',// the default format for export, can be 'json' or 'xml'
 *         'tables' => [                   // Properties of individual tables
 *             [
 *                 'connection' => 'db',   // connection identifier
 *                 'table' => '{{%language}}',          // table name
 *                 'columns' => ['name', 'name_ascii'], //names of multilingual fields
 *                 'category' => 'database-table-name', // the category is the database table name
 *             ]
 *         ]
 *     ],
 * ],
 * ~~~
 *
 * IMPORTANT: If you want to modify the value of roles (in other words to start using user roles) you need to enable authManager in the common config.
 *
 * Using of authManager: http://www.yiiframework.com/doc-2.0/guide-security-authorization.html
 *
 * examples:
 *
 * PhpManager:
 *
 * ~~~
 * 'components' => [
 *      'authManager' => [
 *          'class' => 'yii\rbac\PhpManager',
 *      ],
 * ],
 * ~~~
 *
 * DbManager:
 *
 * ~~~
 * 'components' => [
 *      'authManager' => [
 *          'class' => 'yii\rbac\DbManager',
 *      ],
 * ],
 * ~~~
 *
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    /**
     * Session key for storing front end translating privileges.
     */
    const SESSION_KEY_ENABLE_TRANSLATE = 'frontendTranslation_EnableTranslate';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lajax\translatemanager\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'language/list';

    /**
     * @var string name of the used layout. If you want to use the site default layout set value null.
     */
    public $layout = 'language';

    /**
     * @var array the list of IPs that are allowed to access this module.
     */
    public $allowedIPs = ['127.0.0.1', '::1'];

    /**
     * @var array the list of rights that are allowed to access this module.
     * If you modify, you also need to enable authManager.
     * http://www.yiiframework.com/doc-2.0/guide-security-authorization.html
     */
    public $roles = [];

    /**
     * @var array list of the categories being ignored.
     */
    public $ignoredCategories = [];

    /**
     * @var array directories/files being ignored.
     */
    public $ignoredItems = [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/BaseYii.php',
        'runtime',
        'bower',
        'nikic',
    ];

    /**
     * @var string|array The root directory or directories of the scanning. The path can be an alias or
     * a full path.
     *
     * It is possible to define one root directory as string. In this case the `scanRootParentDirectory` will be used
     * when determining the actual directory to scan.
     *
     * Multiple root directories can be declared in an array. In this case all items must point to the exact directory,
     * as `scanRootParentDirectory` **will be omitted**.
     */
    public $root = '@app';

    /**
     * @var bool Whether scan the defined `root` parent directory, or the folder itself. This option is used only,
     * when the `root` option contains a single directory as string (e.g. `'root' => '@app'`).
     *
     * <b>IMPORTANT</b>: Changing this from `true` to `false` could cause loss of translated items, as
     * optimize action removes the missing items.
     *
     * If the configured root is `@app`:
     *  - `true` means for advanced apps, that the scan runs on the parent directory, which is the root for the entire project.
     *     This is the desired behavior.
     *  - `true` means for basic apps, that the scan runs also on the parent directory, which is outside of the project folder
     *     (as `@app` is equals to the project root). This is not desired behavior, it is preferred to change this option
     *     to `false`.
     */
    public $scanRootParentDirectory = true;

    /**
     * @var string writeable directory used for keeping the generated javascript files.
     */
    public $tmpDir = '@runtime/';

    /**
     * @var array list of file extensions that contain language elements.
     * Only files with these extensions will be processed.
     */
    public $patterns = ['*.php', '*.js'];

    /**
     * @var string name of the subdirectory which contains the language elements.
     */
    public $subDir = '/translate/';

    /**
     * @var string Regular expression to match PHP Yii::t functions.
     *
     * @deprecated since version 1.2.7
     */
    public $patternPhp = '#::t\s*\(\s*(?P<category>\'[\w\d\s_-]+?(?<!\\\\)\'|"[\w\d\s_-]+?(?<!\\\\)"?)\s*,\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';

    /**
     * @var string PHP Regular expression to match arrays containing language elements to translate.
     *
     * @deprecated since version 1.2.7
     */
    public $patternArray = "#\@translate[^\$]+\$(?P<text>.+?)[\]\)];#smui";

    /**
     * @var string PHP Regular expression to detect langualge elements within arrays.
     *
     * @deprecated since version 1.2.7
     */
    public $patternArrayRecursive = '#(?P<category>)(\[|\(|>|,|)\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*(,|$)#s';

    /**
     * @var string Regular expression to detect JavaScript lajax.t functions.
     *
     * @deprecated since version 1.2.7
     */
    public $patternJs = '#lajax\.t\s*\(\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';

    /**
     * @var array List of the PHP function for translating messages.
     */
    public $phpTranslators = ['::t'];

    /**
     * @var array List of the JavaScript function for translating messages.
     */
    public $jsTranslators = ['lajax.t'];

    /**
     * @var string PHP Regular expression to match arrays containing language elements to translate.
     */
    public $patternArrayTranslator = '#\@translate[^\$]+(?P<translator>[\w\d\s_]+[^\(\[]+)#s';

    /**
     * @var int The max_execution_time used when scanning, when set to null the default max_execution_time will not be modified.
     */
    public $scanTimeLimit = null;

    /**
     * examples:
     *
     * ~~~
     * $tables = [
     *      [
     *          'connection' => 'db',               // connection identifier.
     *          'table' => '{{%language}}',         // name of the database table to scan.
     *          'columns' => ['name', 'name_ascii'] // fields to check.
     *      ],
     *      [
     *          'connection' => 'db',                           // connection identifier.
     *          'table' => '{{%post}}',                         // name of the database table to scan.
     *          'columns' => ['title', 'description', 'content']// fields to check.
     *      ],
     * ];
     * ~~~
     *
     * @var array identifiers for the database tables containing language elements.
     */
    public $tables;

    /**
     * @var string The database table storing the languages.
     */
    public $languageTable = '{{%language}}';

    /**
     * @var string The search string to find empty translations.
     */
    public $searchEmptyCommand = '!';

    /**
     * @var int The minimum status for a language to be selected by default in the export list.
     */
    public $defaultExportStatus = 1;

    /**
     * @var string The default export format (yii\web\Response::FORMAT_JSON or yii\web\Response::FORMAT_XML).
     */
    public $defaultExportFormat = Response::FORMAT_JSON;

    /**
     * @var string The default db connection
     */
    public $connection = 'db';

    /**
     * @var array Scanners can be overriden here. If not set original set of scanners will be used from Scanner
     */
    public $scanners = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->checkAccess()) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }

    /**
     * @return bool whether the module can be accessed by the current user
     */
    public function checkAccess()
    {
        $ip = Yii::$app->request->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Translate is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    /**
     * @return string The full path of the directory containing the generated JavaScript files.
     */
    public function getLanguageItemsDirPath()
    {
        return Yii::getAlias($this->tmpDir) . $this->subDir;
    }
}
