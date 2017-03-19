<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class m141002_030233_translate_manager extends Migration
{
    /**
     * @var array The language table contains the list of languages.
     */
    protected $languages = [
        ['af-ZA', 'af', 'za', 'Afrikaans', 'Afrikaans', 0],
        ['ar-AR', 'ar', 'ar', '‏العربية‏', 'Arabic', 0],
        ['az-AZ', 'az', 'az', 'Azərbaycan dili', 'Azerbaijani', 0],
        ['be-BY', 'be', 'by', 'Беларуская', 'Belarusian', 0],
        ['bg-BG', 'bg', 'bg', 'Български', 'Bulgarian', 0],
        ['bn-IN', 'bn', 'in', 'বাংলা', 'Bengali', 0],
        ['bs-BA', 'bs', 'ba', 'Bosanski', 'Bosnian', 0],
        ['ca-ES', 'ca', 'es', 'Català', 'Catalan', 0],
        ['cs-CZ', 'cs', 'cz', 'Čeština', 'Czech', 0],
        ['cy-GB', 'cy', 'gb', 'Cymraeg', 'Welsh', 0],
        ['da-DK', 'da', 'dk', 'Dansk', 'Danish', 0],
        ['de-DE', 'de', 'de', 'Deutsch', 'German', 0],
        ['el-GR', 'el', 'gr', 'Ελληνικά', 'Greek', 0],
        ['en-GB', 'en', 'gb', 'English (UK)', 'English (UK)', 1],
        ['en-PI', 'en', 'pi', 'English (Pirate)', 'English (Pirate)', 0],
        ['en-UD', 'en', 'ud', 'English (Upside Down)', 'English (Upside Down)', 0],
        ['en-US', 'en', 'us', 'English (US)', 'English (US)', 1],
        ['eo-EO', 'eo', 'eo', 'Esperanto', 'Esperanto', 0],
        ['es-ES', 'es', 'es', 'Español (España)', 'Spanish (Spain)', 0],
        ['es-LA', 'es', 'la', 'Español', 'Spanish', 0],
        ['et-EE', 'et', 'ee', 'Eesti', 'Estonian', 0],
        ['eu-ES', 'eu', 'es', 'Euskara', 'Basque', 0],
        ['fa-IR', 'fa', 'ir', '‏فارسی‏', 'Persian', 0],
        ['fb-LT', 'fb', 'lt', 'Leet Speak', 'Leet Speak', 0],
        ['fi-FI', 'fi', 'fi', 'Suomi', 'Finnish', 0],
        ['fo-FO', 'fo', 'fo', 'Føroyskt', 'Faroese', 0],
        ['fr-CA', 'fr', 'ca', 'Français (Canada)', 'French (Canada)', 0],
        ['fr-FR', 'fr', 'fr', 'Français (France)', 'French (France)', 0],
        ['fy-NL', 'fy', 'nl', 'Frysk', 'Frisian', 0],
        ['ga-IE', 'ga', 'ie', 'Gaeilge', 'Irish', 0],
        ['gl-ES', 'gl', 'es', 'Galego', 'Galician', 0],
        ['he-IL', 'he', 'il', '‏עברית‏', 'Hebrew', 0],
        ['hi-IN', 'hi', 'in', 'हिन्दी', 'Hindi', 0],
        ['hr-HR', 'hr', 'hr', 'Hrvatski', 'Croatian', 0],
        ['hu-HU', 'hu', 'hu', 'Magyar', 'Hungarian', 0],
        ['hy-AM', 'hy', 'am', 'Հայերեն', 'Armenian', 0],
        ['id-ID', 'id', 'id', 'Bahasa Indonesia', 'Indonesian', 0],
        ['is-IS', 'is', 'is', 'Íslenska', 'Icelandic', 0],
        ['it-IT', 'it', 'it', 'Italiano', 'Italian', 0],
        ['ja-JP', 'ja', 'jp', '日本語', 'Japanese', 0],
        ['ka-GE', 'ka', 'ge', 'ქართული', 'Georgian', 0],
        ['km-KH', 'km', 'kh', 'ភាសាខ្មែរ', 'Khmer', 0],
        ['ko-KR', 'ko', 'kr', '한국어', 'Korean', 0],
        ['ku-TR', 'ku', 'tr', 'Kurdî', 'Kurdish', 0],
        ['la-VA', 'la', 'va', 'lingua latina', 'Latin', 0],
        ['lt-LT', 'lt', 'lt', 'Lietuvių', 'Lithuanian', 0],
        ['lv-LV', 'lv', 'lv', 'Latviešu', 'Latvian', 0],
        ['mk-MK', 'mk', 'mk', 'Македонски', 'Macedonian', 0],
        ['ml-IN', 'ml', 'in', 'മലയാളം', 'Malayalam', 0],
        ['ms-MY', 'ms', 'my', 'Bahasa Melayu', 'Malay', 0],
        ['nb-NO', 'nb', 'no', 'Norsk (bokmål)', 'Norwegian (bokmal)', 0],
        ['ne-NP', 'ne', 'np', 'नेपाली', 'Nepali', 0],
        ['nl-NL', 'nl', 'nl', 'Nederlands', 'Dutch', 0],
        ['nn-NO', 'nn', 'no', 'Norsk (nynorsk)', 'Norwegian (nynorsk)', 0],
        ['pa-IN', 'pa', 'in', 'ਪੰਜਾਬੀ', 'Punjabi', 0],
        ['pl-PL', 'pl', 'pl', 'Polski', 'Polish', 0],
        ['ps-AF', 'ps', 'af', '‏پښتو‏', 'Pashto', 0],
        ['pt-BR', 'pt', 'br', 'Português (Brasil)', 'Portuguese (Brazil)', 0],
        ['pt-PT', 'pt', 'pt', 'Português (Portugal)', 'Portuguese (Portugal)', 0],
        ['ro-RO', 'ro', 'ro', 'Română', 'Romanian', 0],
        ['ru-RU', 'ru', 'ru', 'Русский', 'Russian', 0],
        ['sk-SK', 'sk', 'sk', 'Slovenčina', 'Slovak', 0],
        ['sl-SI', 'sl', 'si', 'Slovenščina', 'Slovenian', 0],
        ['sq-AL', 'sq', 'al', 'Shqip', 'Albanian', 0],
        ['sr-RS', 'sr', 'rs', 'Српски', 'Serbian', 0],
        ['sv-SE', 'sv', 'se', 'Svenska', 'Swedish', 0],
        ['sw-KE', 'sw', 'ke', 'Kiswahili', 'Swahili', 0],
        ['ta-IN', 'ta', 'in', 'தமிழ்', 'Tamil', 0],
        ['te-IN', 'te', 'in', 'తెలుగు', 'Telugu', 0],
        ['th-TH', 'th', 'th', 'ภาษาไทย', 'Thai', 0],
        ['tl-PH', 'tl', 'ph', 'Filipino', 'Filipino', 0],
        ['tr-TR', 'tr', 'tr', 'Türkçe', 'Turkish', 0],
        ['uk-UA', 'uk', 'ua', 'Українська', 'Ukrainian', 0],
        ['vi-VN', 'vi', 'vn', 'Tiếng Việt', 'Vietnamese', 0],
        ['xx-XX', 'xx', 'xx', 'Fejlesztő', 'Developer', 0],
        ['zh-CN', 'zh', 'cn', '中文(简体)', 'Simplified Chinese (China)', 0],
        ['zh-HK', 'zh', 'hk', '中文(香港)', 'Traditional Chinese (Hong Kong)', 0],
        ['zh-TW', 'zh', 'tw', '中文(台灣)', 'Traditional Chinese (Taiwan)', 0],
    ];

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%language}}', [
            'language_id' => Schema::TYPE_STRING . '(5) NOT NULL',
            'language' => Schema::TYPE_STRING . '(3) NOT NULL',
            'country' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . '(32) NOT NULL',
            'name_ascii' => Schema::TYPE_STRING . '(32) NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'PRIMARY KEY (language_id)',
        ], $tableOptions);

        $this->batchInsert('{{%language}}', [
            'language_id',
            'language',
            'country',
            'name',
            'name_ascii',
            'status',
        ], $this->languages);

        $this->createTable('{{%language_source}}', [
            'id' => Schema::TYPE_PK,
            'category' => Schema::TYPE_STRING . '(32) DEFAULT NULL',
            'message' => Schema::TYPE_TEXT,
        ], $tableOptions);

        $this->createTable('{{%language_translate}}', [
            'id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'translation' => Schema::TYPE_TEXT,
            'PRIMARY KEY (id, language)',
        ], $tableOptions);

        $this->createIndex('language_translate_idx_language', '{{%language_translate}}', 'language');

        $this->addForeignKey('language_translate_ibfk_1', '{{%language_translate}}', ['language'], '{{%language}}', ['language_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('language_translate_ibfk_2', '{{%language_translate}}', ['id'], '{{%language_source}}', ['id'], 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%language_translate}}');
        $this->dropTable('{{%language_source}}');
        $this->dropTable('{{%language}}');
    }
}
