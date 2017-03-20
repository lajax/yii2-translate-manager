<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */

namespace lajax\translatemanager\models;

use Yii;

/**
 * This is the model class for table "language_translate".
 *
 * @property string $id
 * @property string $language
 * @property string $translation
 * @property LanguageSource $LanguageSource
 * @property Language $language0
 */
class LanguageTranslate extends \yii\db\ActiveRecord
{
    /**
     * @var int Number of translated language elements.
     */
    public $cnt;

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        $dbMessageSources = Yii::getObjectVars(Yii::$app->i18n->getMessageSource('DbMessageSource'));

        return $dbMessageSources['db'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $dbMessageSources = Yii::getObjectVars(Yii::$app->i18n->getMessageSource('DbMessageSource'));

        return isset($dbMessageSources['messageTable']) ? $dbMessageSources['messageTable'] : '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'targetClass' => '\lajax\translatemanager\models\LanguageSource'],
            [['language'], 'exist', 'targetClass' => '\lajax\translatemanager\models\Language', 'targetAttribute' => 'language_id'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'language' => Yii::t('model', 'Language'),
            'translation' => Yii::t('model', 'Translation'),
        ];
    }

    /**
     * Returnes language object by id and language_id. If not found, creates a new one.
     *
     * @param int $id LanguageSource id
     * @param string $languageId Language language_id
     *
     * @return LanguageTranslate
     *
     * @deprecated since version 1.2.7
     */
    public static function getLanguageTranslateByIdAndLanguageId($id, $languageId)
    {
        $languageTranslate = self::findOne(['id' => $id, 'language' => $languageId]);
        if (!$languageTranslate) {
            $languageTranslate = new self([
                'id' => $id,
                'language' => $languageId,
            ]);
        }

        return $languageTranslate;
    }

    /**
     * @return array The name of languages the language element has been translated into.
     */
    public function getTranslatedLanguageNames()
    {
        $translatedLanguages = $this->getTranslatedLanguages();

        $data = [];
        foreach ($translatedLanguages as $languageTranslate) {
            $data[$languageTranslate->language] = $languageTranslate->getLanguageName();
        }

        return $data;
    }

    /**
     * Returns the language element in all other languages.
     *
     * @return LanguageTranslate[]
     */
    public function getTranslatedLanguages()
    {
        return static::find()->where('id = :id AND language != :language', [':id' => $this->id, 'language' => $this->language])->all();
    }

    /**
     * @staticvar array $language_names caching the list of languages.
     *
     * @return string
     */
    public function getLanguageName()
    {
        static $language_names;
        if (!$language_names || empty($language_names[$this->language])) {
            $language_names = Language::getLanguageNames();
        }

        return empty($language_names[$this->language]) ? $this->language : $language_names[$this->language];
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * @deprecated since version 1.4.5
     */
    public function getId0()
    {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSource()
    {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0()
    {
        return $this->hasOne(Language::className(), ['language_id' => 'language']);
    }
}
