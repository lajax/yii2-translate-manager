<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */

namespace lajax\translatemanager\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property int $status
 * @property LanguageTranslate $languageTranslate
 * @property LanguageSource[] $languageSources
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * Status of inactive language.
     */
    const STATUS_INACTIVE = 0;

    /**
     * Status of active language.
     */
    const STATUS_ACTIVE = 1;

    /**
     * Status of ‘beta’ language.
     */
    const STATUS_BETA = 2;

    /**
     * Array containing possible states.
     *
     * @var array
     * @translate
     */
    private static $_CONDITIONS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BETA => 'Beta',
    ];

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->getModule('translatemanager')->connection);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->getModule('translatemanager') ?
            Yii::$app->getModule('translatemanager')->languageTable : '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii', 'status'], 'required'],
            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'unique'],
            [['language_id'], 'match', 'pattern' => '/^([a-z]{2}[_-][A-Z]{2}|[a-z]{2})$/'],
            [['language', 'country'], 'string', 'max' => 2],
            [['language', 'country'], 'match', 'pattern' => '/^[a-z]{2}$/i'],
            [['name', 'name_ascii'], 'string', 'max' => 32],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => array_keys(self::$_CONDITIONS)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language_id' => Yii::t('model', 'Language ID'),
            'language' => Yii::t('model', 'Language'),
            'country' => Yii::t('model', 'Country'),
            'name' => Yii::t('model', 'Name'),
            'name_ascii' => Yii::t('model', 'Name Ascii'),
            'status' => Yii::t('model', 'Status'),
        ];
    }

    /**
     * Returns the list of languages stored in the database in an array.
     *
     * @param bool $active True/False according to the status of the language.
     *
     * @return array
     *
     * @deprecated since version 1.5.2
     */
    public static function getLanguageNames($active = false)
    {
        $languageNames = [];
        foreach (self::getLanguages($active, true) as $language) {
            $languageNames[$language['language_id']] = $language['name'];
        }

        return $languageNames;
    }

    /**
     * Returns language objects.
     *
     * @param bool $active True/False according to the status of the language.
     * @param bool $asArray Return the languages as language object or as 'flat' array
     *
     * @return Language|array
     *
     * @deprecated since version 1.5.2
     */
    public static function getLanguages($active = true, $asArray = false)
    {
        if ($active) {
            return self::find()->where(['status' => static::STATUS_ACTIVE])->asArray($asArray)->all();
        } else {
            return self::find()->asArray($asArray)->all();
        }
    }

    /**
     * Returns the state of the language (Active, Inactive or Beta) in the current language.
     *
     * @return string
     */
    public function getStatusName()
    {
        return Yii::t('array', self::$_CONDITIONS[$this->status]);
    }

    /**
     * Returns the names of possible states in an associative array.
     *
     * @return array
     */
    public static function getStatusNames()
    {
        return \lajax\translatemanager\helpers\Language::a(self::$_CONDITIONS);
    }

    /**
     * Returns the completness of a given translation (language).
     *
     * @return int
     */
    public function getGridStatistic()
    {
        static $statistics;
        if (!$statistics) {
            $count = LanguageSource::find()->count();
            if ($count == 0) {
                return 0;
            }

            $languageTranslates = LanguageTranslate::find()
                ->select(['language', 'COUNT(*) AS cnt'])
                ->andWhere('translation IS NOT NULL')
                ->groupBy(['language'])
                ->all();

            foreach ($languageTranslates as $languageTranslate) {
                $statistics[$languageTranslate->language] = floor(($languageTranslate->cnt / $count) * 100);
            }
        }

        return isset($statistics[$this->language_id]) ? $statistics[$this->language_id] : 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate()
    {
        return $this->hasOne(LanguageTranslate::className(), ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * @deprecated since version 1.4.5
     */
    public function getIds()
    {
        return $this->hasMany(LanguageSource::className(), ['id' => 'id'])
            ->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSources()
    {
        return $this->hasMany(LanguageSource::className(), ['id' => 'id'])
            ->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }
}
