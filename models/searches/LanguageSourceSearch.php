<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * LanguageSourceSearch represents the model behind the search form about `common\models\LanguageSource`.
 */
class LanguageSourceSearch extends LanguageSource
{

    /**
     *
     * @var string
     */
    public $translation;

    /**
     * @var string Store language_id eg.: `en` en-US
     */
    private $_languageId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['category', 'message', 'translation'], 'safe'],
        ];
    }

    /**
     * The name of the default scenario. 
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array $params Search conditions.
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->_languageId = $params['language_id'];
        $query = LanguageSource::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'category',
                'message',
                'translation' => [
                    'asc' => ['translation' => SORT_ASC],
                    'desc' => ['translation' => SORT_DESC],
                    'label' => \Yii::t('language', 'Translation')
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['languageTranslate' => function($query) {
                $query->andWhere(['language' => $this->_languageId]);
                $query->orWhere([LanguageTranslate::tableName() . '.id' => null]);
            }]);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
                ->andFilterWhere(['like', 'message', $this->message]);

        $query->joinWith(['languageTranslate' => function ($query) {
            $query->andWhere(['language' => $this->_languageId]);
            if ($this->translation) {
                $query->andWhere(['like', 'translation', $this->translation]);
            } else {
                $query->orWhere([LanguageTranslate::tableName() . '.id' => null]);
            }
        }]);

        return $dataProvider;
    }

}