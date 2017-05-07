# Yii2 - Translate Manager

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Translation management extension for Yii 2

## Introduction

This module provides a simple translating interface for the multilingual elements of your project. It can auto-detect new language elements (project scan).
Duplications are filtered out automatically during project scanning.
Unused language elements can be removed from the database with a single click (database optimisation) and translations can be imported and exported.
It is possible to translate client side messages too (those stored in JavaScript files) as the project scan collects language elements to be translated from JavaScript files as well.

It also allows you to translate text on the client side (on the live webpage) without having to log in to the translating interface. (frontendTranslation).

On the server side it can handle database or one-dimensional/multidimensional array elements and Yii::t functions.
You can exclude files, folders or categories to prevent them from being translated.

## Contributing

Please read and follow the instructions in the [Contributing guide](CONTRIBUTING.md).

## Installation

Via [Composer](http://getcomposer.org/download/)

```
composer require lajax/yii2-translate-manager
```

### Migration

Run the following command in Terminal for database migration:

```
yii migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
```

Or use the [namespaced migration](http://www.yiiframework.com/doc-2.0/guide-db-migrations.html#namespaced-migrations) (requires at least Yii 2.0.10):

```php
// Add namespace to console config:
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationNamespaces' => [
            'lajax\translatemanager\migrations\namespaced',
        ],
    ],
],
```

Then run:
```
yii migrate/up
```

### Config

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
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable' => '{{%language_translate}}',
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
        'scanRootParentDirectory' => true, // Whether scan the defined `root` parent directory, or the folder itself.
                                           // IMPORTANT: for detailed instructions read the chapter about root configuration.
        'layout' => 'language',         // Name of the used layout. If using own layout use 'null'.
        'allowedIPs' => ['127.0.0.1'],  // IP addresses from which the translation interface is accessible.
        'roles' => ['@'],               // For setting access levels to the translating interface.
        'tmpDir' => '@runtime',         // Writable directory for the client-side temporary language files.
                                        // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
        'phpTranslators' => ['::t'],    // list of the php function for translating messages.
        'jsTranslators' => ['lajax.t'], // list of the js function for translating messages.
        'patterns' => ['*.js', '*.php'],// list of file extensions that contain language elements.
        'ignoredCategories' => ['yii'], // these categories won't be included in the language database.
        'ignoredItems' => ['config'],   // these files will not be processed.
        'scanTimeLimit' => null,        // increase to prevent "Maximum execution time" errors, if null the default max_execution_time will be used
        'searchEmptyCommand' => '!',    // the search string to enter in the 'Translation' search field to find not yet translated items, set to null to disable this feature
        'defaultExportStatus' => 1,     // the default selection of languages to export, set to 0 to select all languages by default
        'defaultExportFormat' => 'json',// the default format for export, can be 'json' or 'xml'
        'tables' => [                   // Properties of individual tables
            [
                'connection' => 'db',   // connection identifier
                'table' => '{{%language}}',         // table name
                'columns' => ['name', 'name_ascii'],// names of multilingual fields
                'category' => 'database-table-name',// the category is the database table name
            ]
        ],
        'scanners' => [ // define this if you need to override default scanners (below)
            '\lajax\translatemanager\services\scanners\ScannerPhpFunction',
            '\lajax\translatemanager\services\scanners\ScannerPhpArray',
            '\lajax\translatemanager\services\scanners\ScannerJavaScriptFunction',
            '\lajax\translatemanager\services\scanners\ScannerDatabase',
        ],
    ],
],
```

#### Configuring the scan root

The root path can be an alias or a full path (e.g. `@app` or `/webroot/site/`).

The file scanner will scan the configured folders for translatable elements. The following two options
determine the scan root directory: `root`, and `scanRootParentDirectory`. These options are defaults to
values that works with the Yii 2 advanced project template. If you are using basic template, you have to modify
these settings.

The `root` options tells which is the root folder for project scan. It can contain a single directory (string),
or multiple directories (in an array).

The `scanRootParentDirectory` **is used only** if a single root directory is specified in a string.

**IMPORTANT: Changing these options could cause loss of translated items,
as optimize action removes the missing items.** So be sure to double check your configuration!

**a)** Single root directory:

It is possible to define one root directory as string in the `root` option. In this case the `scanRootParentDirectory`
will be used when determining the actual directory to scan.

If `scanRootParentDirectory` is set to `true` (which is the default value), the scan will run on the parent directory.
This is desired behavior on advanced template, because the `@app` is the root for the current app, which is a subfolder
inside the project (so the entire root of the project is the parent directory of `@app`).

For basic template the `@app` is also the root for the entire project. Because of this with the default value
of `scanRootParentDirectory`, the scan runs outside the project folder. This is not desired behavior, and
changing the value to `false` solves this.

**IMPORTANT: Changing the `scanRootParentDirectory` from `true` to `false` could cause loss of translated items,
as the root will be a different directory.**

For example:

| `root` value | `scanRootParentDirectory` value| Scanned directory |
|---|---|---|
| `/webroot/site/frontend` | `true` | `/webroot/site` |
| `/webroot/site/frontend` | `false` | `/webroot/site/frontend` |

**b)** Multiple root directories:

Multiple root directories can be defined in an array. In this case all items must point to the exact directory,
as `scanRootParentDirectory` **will be omitted**.

For example:

```php
'root' => [
    '@frontend',
    '@vendor',
    '/some/external/folder',
],
```

#### Using of [authManager](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html)

Examples:

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

#### Front end translation:

```php
'bootstrap' => ['translatemanager'],
'components' => [
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Component'
    ]
]
```

## Usage

### Register client scripts

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

### ToggleTranslate button

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

### Placing multilingual elements in the source code.

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

**IMPORTANT: The lajax\translatemanager\helpers\Language::t() (Lx::t()) function currently does not support the translation of HTMLattributes**

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
 * Returning the 'status' array on the site's own language.
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
 * Returning the 'genders' array in German
 * return array
 */
public function getGenders() {
    return \lajax\translatemanager\helpers\Language::a($this->_GENDERS, 'de-DE');
}
```

PHP Database:

* With new attributes:

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
     * @var Returning the 'name' attribute on the site's own language.
     */
    public $name_t;

    /**
     * @var Returning the 'description' attribute on the site's own language.
     */
    public $description_t;

    /* ... */

    public function afterFind() {
        $this->name_t = Language::d($this->name);
        $this->description_t = Language::d($this->descrioption);

        // or If the category is the database table name.
        // $this->name_t = Language::t(static::tableName(), $this->name);
        // $this->description_t = Language::t(static::tableName(), $this->description);
        parent::afterFind();
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            Language::saveMessage($this->name);
            Language::saveMessage($this->description);

            // or If the category is the database table name.
            // Language::saveMessage($this->name, static::tableName());
            // Language::saveMessage($this->description, static::tableName());

            return true;
        }

        return false;
    }

    // or GETTERS:

    /**
     * @return string Returning the 'name' attribute on the site's own language.
     */
    public function getName($params = [], $language = null) {
        return Language::d($this->name, $params, $language);

        // or If the category is the database table name.
        // return Language::t(static::tableName(), $this->name, $params, $language);
    }

    /**
     * @return string Returning the 'description' attribute on the site's own language.
     */
    public function getDescription($params = [], $language = null) {
        return Language::d($this->description, $params, $language);

        // or If the category is the database table name.
        // return Language::t(static::tableName(), $this->description, $params, $language);
    }
}
```


* With behavior (since 1.5.3):

    This behavior does the following:
    - Replaces the specified attributes with translations after the model is loaded.
    - Saves the attribute values as:
        1. Source messages, if the current language is the source language.
        2. Translations, if the current language is different from the source language.
           This way the value stored in database is not overwritten with the translation.

    **Note**: If the model should be saved as translation, but the source message does not exist yet in the database
    then the message is saved as the source message whether the current language is the source language or not.
    To avoid this scan the database for existing messages when using the behavior first, and only save new records
    when the current language is the source language.

```php
namespace common\models;

/**
 * This is the model class for table "category".
 *
 * @property string $category_id
 * @property string $name
 * @property string $description
 */
class Category extends \yii\db\ActiveRecord {

    // TranslateBehavior

    public function behaviors()
    {
        return [
            [
                'class' => \lajax\translatemanager\behaviors\TranslateBehavior::className(),
                'translateAttributes' => ['name', 'description'],
            ],

            // or If the category is the database table name.
            // [
            //     'class' => \lajax\translatemanager\behaviors\TranslateBehavior::className(),
            //     'translateAttributes' => ['name', 'description'],
            //     'category' => static::tableName(),
            // ],
        ];
    }

}
```

### URLs

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
    ['label' => Yii::t('language', 'Im-/Export'), 'items' => [
        ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import']],
        ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export']],
    ]
];
```

### Console commands

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

## Known issues

* Scanner is scanning parent root directory [#12](https://github.com/lajax/yii2-translate-manager/pull/12).

  You can overwrite this behavior with the `scanRootParentDirectory` option. (See Config section for details.)
* Frontend translation of strings in hidden tags corrupts HTML. [#45](https://github.com/lajax/yii2-translate-manager/issues/45)

## Coding style

The project uses the PSR-2 coding standard.

Coding style issues can be fixed using the following command:

```
composer cs-fix
```

You can check the code, without affecting it:

```
composer cs-fix-dry-run
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Screenshots

### List of languages
![translate-manager-0 2-screen-1](https://res.cloudinary.com/lajax/image/upload/v1421343987/admin-languages_ikxjqz.png)


### Scanning project
![translate-manager-0 2-screen-2](https://res.cloudinary.com/lajax/image/upload/v1424605567/admin-scan-2_lig4wn.png)


### Optimise database
![translate-manager-0 2-screen-3](https://res.cloudinary.com/lajax/image/upload/v1424606158/admin-optimise-2_nf6u3t.png)


### Translate on the admin interface
![translate-manager-0 2-screen-4](https://res.cloudinary.com/lajax/image/upload/v1421382395/admin-translation_p9uavl.png)


### Front end in translating mode
![translate-manager-0 2-screen-6](https://res.cloudinary.com/lajax/image/upload/v1421343986/frontend-translation-toggle_fsqflh.png)


### Translate on the front end
![translate-manager-0 2-screen-7](https://res.cloudinary.com/lajax/image/upload/v1421343987/frontend-translation-dialog_jivgkh.png)


## Links

- [GitHub](https://github.com/lajax/yii2-translate-manager)
- [Api Docs](http://lajax.github.io/yii2-translate-manager)
- [Packagist][link-packagist]
- [Yii Extensions](http://www.yiiframework.com/extension/yii2-translate-manager)

[ico-version]: https://img.shields.io/packagist/v/lajax/yii2-translate-manager.svg?style=flat
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat
[ico-downloads]: https://img.shields.io/packagist/dt/lajax/yii2-translate-manager.svg?style=flat

[link-packagist]: https://packagist.org/packages/lajax/yii2-translate-manager
[link-downloads]: https://packagist.org/packages/lajax/yii2-translate-manager