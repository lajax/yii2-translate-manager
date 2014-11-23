<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
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
 *
 * @property LanguageSource $id0
 * @property Language $language0
 */
class LanguageTranslate extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        $dbMessageSources = Yii::getObjectVars(Yii::$app->i18n->getMessageSource('DbMessageSource'));
        return isset($dbMessageSources['messageTable']) ? $dbMessageSources['messageTable'] : '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'language' => Yii::t('model', 'Language'),
            'translation' => Yii::t('model', 'Translation'),
        ];
    }

    /**
     * Returnes language object by id and language_id. If not found, creates a new one.
     * @param integer $id
     * @param string $language_id
     * @return \common\models\LanguageTranslate
     */
    public static function getLanguageTranslateByIdAndLanguageId($id, $language_id) {
        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $language_id]);
        if ($languageTranslate === null) {
            $languageTranslate = new LanguageTranslate;
            $languageTranslate->id = $id;
            $languageTranslate->language = $language_id;
        }

        return $languageTranslate;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0() {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0() {
        return $this->hasOne(Language::className(), ['language_id' => 'language']);
    }

}
