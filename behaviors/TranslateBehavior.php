<?php

namespace lajax\translatemanager\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use lajax\translatemanager\helpers\Language;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * TranslateManager Database translate behavior.
 *
 * Installation:
 *
 * ~~~
 * [
 *      'class' => lajax\translatemanager\behaviors\TranslateBehavior::className(),
 *      'translateAttributes' => ['names of multilingual fields'],
 * ],
 * ~~~
 *
 * or If the category is the database table name.
 *
 * ~~~
 * [
 *      'class' => lajax\translatemanager\behaviors\TranslateBehavior::className(),
 *      'translateAttributes' => ['names of multilingual fields'],
 *      'category' => static::tableName(),
 * ],
 * ~~~
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.5.3
 */
class TranslateBehavior extends AttributeBehavior
{
    /**
     * @var array|string
     */
    public $translateAttributes;

    /**
     * @var string Category of message.
     */
    public $category = 'database';

    /**
     * @var BaseActiveRecord the owner model of this behavior
     */
    public $owner;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->category = str_replace(['{', '%', '}'], '', $this->category);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'translateAttributes',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'saveAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'saveAttributes',
        ];
    }

    /**
     * Translates the attributes to the current language.
     *
     * @param \yii\base\Event $event
     */
    public function translateAttributes($event)
    {
        foreach ($this->translateAttributes as $attribute) {
            $this->owner->{$attribute} = Yii::t($this->category, $this->owner->attributes[$attribute]);
        }
    }

    /**
     * Saves new language element by category.
     *
     * @param \yii\base\Event $event
     */
    public function saveAttributes($event)
    {
        $isAppInSourceLanguage = Yii::$app->sourceLanguage === Yii::$app->language;

        foreach ($this->translateAttributes as $attribute) {
            if (!$this->owner->isAttributeChanged($attribute)) {
                continue;
            }

            if ($isAppInSourceLanguage || !$this->saveAttributeValueAsTranslation($attribute)) {
                Language::saveMessage($this->owner->attributes[$attribute], $this->category);
            }
        }
    }

    /**
     * @param string $attribute The name of the attribute.
     *
     * @return bool Whether the translation is saved.
     */
    private function saveAttributeValueAsTranslation($attribute)
    {
        $sourceMessage = $this->owner->getOldAttribute($attribute);
        $translatedMessage = $this->owner->attributes[$attribute];

        // Restore the original value, so it won't be replaced with the translation in the database.
        $this->owner->{$attribute} = $sourceMessage;

        $translateSource = $this->findSourceMessage($sourceMessage);
        if (!$translateSource) {
            return false; // The source does not exist, the message cannot be saved as translation.
        }

        $translation = new LanguageTranslate();
        foreach ($translateSource->languageTranslates as $tmpTranslate) {
            if ($tmpTranslate->language === Yii::$app->language) {
                $translation = $tmpTranslate;
                break;
            }
        }

        if ($translation->isNewRecord) {
            $translation->id = $translateSource->id;
            $translation->language = Yii::$app->language;
        }

        $translation->translation = $translatedMessage;
        $translation->save();

        return true;
    }

    /**
     * Finds the source record with case sensitive match.
     *
     * @param string $message
     *
     * @return LanguageSource|null Null if the source is not found.
     */
    private function findSourceMessage($message)
    {
        $sourceMessages = LanguageSource::findAll(['message' => $message, 'category' => $this->category]);

        foreach ($sourceMessages as $source) {
            if ($source->message === $message) {
                return $source;
            }
        }
    }
}
