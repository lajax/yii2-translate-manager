<?php

namespace lajax\translatemanager;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * This is the main module class for the TranslateManager module.
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lajax\translatemanager\controllers';

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
    ];

    /**
     * @var string the root directory of the scanning.
     */
    public $root = '@app';

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
     */
    public $patternPhp = '#::t\s*\(\s*(?P<category>\'[\w\d\s_-]+?(?<!\\\\)\'|"[\w\d\s_-]+?(?<!\\\\)"?)\s*,\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';

    /**
     * @var string PHP Regular expression to match arrays containing language elements to translate.
     */
    public $patternArray = "#\@translate[^\$]+\$(?P<text>.+?)[\]\)];#smui";

    /**
     * @var string PHP Regular expression to detect langualge elements within arrays.
     */
    public $patternArrayRecursive = '#(?P<category>)(\[|\(|>|,|)\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*(,|$)#s';

    /**
     * @var string Regular expression to detect JavaScript lajax.t functions.
     */
    public $patternJs = '#lajax\.t\s*\(\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';

    /**
     * examples:
     * $tables = [
     *      [
     *          'connection' => 'db',   // connection identifier.
     *          'table' => 'language',  // name of the database table to scan.
     *          'columns' => ['name', 'name_ascii']  // fields to check.
     *      ],
     *      [
     *          'connection' => 'db',   // connection identifier.
     *          'table' => 'post',      // name of the database table to scan.
     *          'columns' => ['title', 'description', 'content']  // fields to check.
     *      ],
     * ];
     * @var array identifiers for the database tables containing language elements.
     */
    public $tables;

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if ($this->checkAccess()) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException('language', 'You are not allowed to access this page.');
        }
    }

    /**
     * @return boolean whether the module can be accessed by the current user
     */
    protected function checkAccess() {
        $ip = Yii::$app->request->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Translate is denied due to IP address restriction. The requested IP is {ip}' . $ip, __METHOD__);

        return false;
    }

    /**
     * @return string The full path of the directory containing the generated JavaScript files.
     */
    public function getLanguageItemsDirPath() {
        return Yii::getAlias($this->tmpDir) . $this->subDir;
    }

}
