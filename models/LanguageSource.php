<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models;

use Yii;

/**
 * This is the model class for table "language_source".
 *
 * @property string $id
 * @property string $category
 * @property string $message
 *
 * @property LanguageTranslate $languageTranslate
 * @property Language[] $languages
 */
class LanguageSource extends \yii\db\ActiveRecord {

    const INSERT_LANGUAGE_ITEMS_LIMIT = 10;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        $dbMessageSources = Yii::getObjectVars(Yii::$app->i18n->getMessageSource('DbMessageSource'));
        return isset($dbMessageSources['sourceMessageTable']) ? $dbMessageSources['sourceMessageTable'] : '{{%source_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'category' => Yii::t('model', 'Category'),
            'message' => Yii::t('model', 'Message'),
        ];
    }

    /**
     * Inserting new language elements into the language_source table.
     * @param array $languageItems
     * @return integer The number of new language elements.
     */
    public function insertLanguageItems($languageItems) {

        $data = [];
        foreach ($languageItems as $category => $messages) {
            foreach ($messages as $message => $value) {
                $data[] = [
                    $category,
                    $message
                ];
            }
        }

        $builder = static::getDb()->queryBuilder;

        $count = count($data);
        for ($i = 0; $i < $count; $i += self::INSERT_LANGUAGE_ITEMS_LIMIT) {
            $command = $builder->batchInsert(self::tableName(), ['category', 'message'], array_slice($data, $i, self::INSERT_LANGUAGE_ITEMS_LIMIT));
            static::getDb()->createCommand($command)->execute();
        }

        return $count;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate() {
        return $this->hasOne(LanguageTranslate::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages() {
        return $this->hasMany(Language::className(), ['language_id' => 'language'])
                ->viaTable(LanguageTranslate::tableName(), ['id' => 'id']);
    }

}
