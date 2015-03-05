Yii2 - Translate Manager
========================
Online Translations

Introduction
------------

This module provides a simple translating interface for the multilingual elements of your project. It can auto-detect new language elements (project scan).
Duplications are filtered out automatically during project scanning.
Unused language elements can be removed from the database with a single click (database optimisation).
It is possible to translate client side messages too (those stored in JavaScript files) as the project scan collects language elements to be translated from JavaScript files as well.

It also allows you to translate text on the client side (on the live webpage) without having to log in to the translating interface. (frontendTranslation).

On the server side it can handle database or one-dimensional/multidimensional array elements and Yii::t functions. 
You can exclude files, folders or categories to prevent them from being translated.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist lajax/yii2-translate-manager "1.*"
```

or add

```json
"lajax/yii2-translate-manager": "1.*"
```

to the require section of your `composer.json` file.

###Migration


Run the following command in Terminal for database migration:

Linux/Unix:
```
yii migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
```

Windows:
```
yii.bat migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
```

###Config

A simple exmple of turning on Yii database multilingual.

```php
'language' => 'en-US',
'components' => [
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'xx-XX', // Developer language
                    'sourceMessageTable' => 'language_source',
                    'messageTable' => 'language_translate',
                    'cachingDuration' => 86400,
                    'enableCaching' => true,
                ],
            ],
        ],
    ],
```


Turning on the TranslateManager Module:


Simple example:

```php
'modules' => [
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Module',
    ],
],
```

A more complex example including database table with multilingual support is below:
```php
'modules' => [
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Module',
        'root' => '@app',               // The root directory of the project scan.
        'layout' => 'language',         // Name of the used layout. If using own layout use 'null'.
        'allowedIPs' => ['127.0.0.1'],  // IP addresses from which the translation interface is accessible.
        'roles' => ['@'],               // For setting access levels to the translating interface.
        'tmpDir' => '@runtime',         // Writable directory for the client-side temporary language files. 
                                        // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
        'phpTranslators' => ['::t'],    // list of the php function for translating messages.
        'jsTranslators' => ['lajax.t'], // list of the js function for translating messages.
        'patterns' => ['*.js', '*.php'],// list of file extensions that contain language elements.
        'ignoredCategories' => ['yii'], // these categories won’t be included in the language database.
        'ignoredItems' => ['config'],   // these files will not be processed.
        'tables' => [                   // Properties of individual tables
            [
                'connection' => 'db',   // connection identifier
                'table' => 'language',  // table name
                'columns' => ['name', 'name_ascii']  //names of multilingual fields
            ]
        ]
    ],
],
```

IMPORTANT: If you want to modify the value of roles (in other words to start using user roles) you need to enable authManager in the common config.

Using of [authManager](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html).

examples:

PhpManager:
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\PhpManager',
    ],
    // ...
],
```

DbManager:
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
    // ...
],
```

Front end translation:

```php
'bootstrap' => ['translatemanager'],
'component' => [
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Component'
    ]
]
```

Usage
-----
###Register client scripts

To translate static messages in JavaScript files it is necessary to register the files.

To register your scripts, call the following method in each action:

```php
\lajax\translatemanager\helpers\Language::registerAssets();
```

A simple example for calling the above method at each page load:
```php
namespace common\controllers;

use lajax\translatemanager\helpers\Language;

// IMPORTANT: all Controllers must originate from this Controller!
class Controller extends \yii\web\Controller {

    public function init() {
        Language::registerAssets();
        parent::init();
    }
}
```

###ToggleTranslate button

Simple example for displaying a button to switch to front end translation mode.
(The button will only appear for users who have the necessary privileges for translating!)

```php
\lajax\translatemanager\widgets\ToggleTranslate::widget();
```

A more complex example for displaying the button:

```php
\lajax\translatemanager\widgets\ToggleTranslate::widget([
 'position' => \lajax\translatemanager\widgets\ToggleTranslate::POSITION_TOP_RIGHT,
 'template' => '<a href="javascript:void(0);" id="toggle-translate" class="{position}" data-language="{language}" data-url="{url}"><i></i> {text}</a><div id="translate-manager-div"></div>',
 'frontendTranslationAsset' => 'lajax\translatemanager\bundles\FrontendTranslationAsset',
 'frontendTranslationPluginAsset' => 'lajax\translatemanager\bundles\FrontendTranslationPluginAsset',
]);
```

###Placing multilingual elements in the source code.

JavaScript:

```php
lajax.t('Apple');
lajax.t('Hello {name}!', {name:'World'});
lajax.t("Don't be so upset.");
```

PHP methods:

```php
Yii::t('category', 'Apple');
Yii::t('category', 'Hello {name}!', ['name' => 'World']);
Yii::t('category', "Don't be so upset.");
```

PHP functions for front end translation:

```php
use lajax\translatemanager\helpers\Language as Lx;

Lx::t('category', 'Apple');
Lx::t('category', 'Hello {name}!', ['name' => 'World']);
Lx::t('category', "Don't be so upset.");
```

PHP arrays:

```php
/**
 * @translate
 */
private $_STATUSES = [
    self::STATUS_INACTIVE => 'Inactive',
    self::STATUS_ACTIVE => 'Active',
    self::STATUS_DELETED => 'Deleted'
];

/**
 * Returning the ‘status’ array on the site’s own language.
 * return array
 */
public function getStatuses() {
    return \lajax\translatemanager\helpers\Language::a($this->_STATUSES);
}

/**
 * @translate
 */
private $_GENDERS = ['Male', 'Female'];

/**
 * Returning the ‘genders’ array in German
 * return array
 */
public function getGenders() {
    return \lajax\translatemanager\helpers\Language::a($this->_GENDERS, 'de-DE');
}
```

PHP Database:

```php
namespace common\models;

use lajax\translatemanager\helpers\Language;

/**
 * This is the model class for table "category".
 *
 * @property string $category_id
 * @property string $name
 * @property string $description
 */
class Category extends \yii\db\ActiveRecord {

    // afterFind & beforeSave:

    /**
     * @var Returning the ‘name’ attribute on the site’s own language.
     */
    public $name_t;

    /**
     * @var Returning the ‘description’ attribute on the site’s own language.
     */
    public $description_t;

    ...

    public function afterFind() {
        $this->name_t = Language::d($this->name);
        $this->description_t = Language::d($this->descrioption);
        parent::afterFind();
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            Language::saveMessage($this->name);
            Language::saveMessage($this->description);

            return true;
        }

        return false;
    }

    // or GETTERS:

    /**
     * @return string Returning the ‘name’ attribute on the site’s own language.
     */
    public function getName($params = [], $language = null) {
        return Language::d($this->name, $params, $language);
    }

    /**
     * @return string Returning the ‘description’ attribute on the site’s own language.
     */
    public function getDescription($params = [], $language = null) {
        return Language::d($this->description, $params, $language);
    }
}
```

###URLs

URLs for the translating tool:

```php
/translatemanager/language/list         // List of languages and modifying their status
/translatemanager/language/create       // Create languages
/translatemanager/language/scan         // Scan the project for new multilingual elements
/translatemanager/language/optimizer    // Optimise the database
```

Example implementation of the Yii2 menu into your own menu. 

```php
$menuItems = [
    ['label' => Yii::t('language', 'Language'), 'items' => [
            ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
            ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
        ]
    ],
    ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
    ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
];
```

###Console commands
 
Register the command

```php
'controllerMap' => [
    'translate' => \lajax\translatemanager\commands\TranslatemanagerController::className()
],
```

Use it with the Yii CLI

```
./yii translate/scan
./yii translate/optimize
```

Screenshots
-----------

###List of languages
![translate-manager-0 2-screen-1](https://res.cloudinary.com/lajax/image/upload/v1421343987/admin-languages_ikxjqz.png)


###Scanning project
![translate-manager-0 2-screen-2](https://res.cloudinary.com/lajax/image/upload/v1424605567/admin-scan-2_lig4wn.png)


###Optimise database
![translate-manager-0 2-screen-3](https://res.cloudinary.com/lajax/image/upload/v1424606158/admin-optimise-2_nf6u3t.png)


###Translate on the admin interface
![translate-manager-0 2-screen-4](https://res.cloudinary.com/lajax/image/upload/v1421382395/admin-translation_p9uavl.png)


###Front end in translating mode
![translate-manager-0 2-screen-6](https://res.cloudinary.com/lajax/image/upload/v1421343986/frontend-translation-toggle_fsqflh.png)


###Translate on the front end
![translate-manager-0 2-screen-7](https://res.cloudinary.com/lajax/image/upload/v1421343987/frontend-translation-dialog_jivgkh.png)


Links
-----

- [GitHub](https://github.com/lajax/yii2-translate-manager)
- [Packagist](https://packagist.org/packages/lajax/yii2-translate-manager)
- [Yii Extensions](http://www.yiiframework.com/extension/yii2-translate-manager)
