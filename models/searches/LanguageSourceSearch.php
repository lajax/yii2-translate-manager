<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lajax\translatemanager\models\LanguageSource;

/**
 * LanguageSourceSearch represents the model behind the search form about `common\models\LanguageSource`.
 */
class LanguageSourceSearch extends LanguageSource {

    /**
     * @var string Store language_id eg.: `en` en-US
     */
    private $_languageId;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['category', 'message'], 'safe'],
        ];
    }

    /**
     * The name of the default scenario. 
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array $params Search conditions.
     * @return ActiveDataProvider
     */
    public function search($params) {
        $this->_languageId = $params['language_id'];
        $query = LanguageSource::find()->with([
            'languageTranslate' => function($query) {
                $query->andWhere(['language' => $this->_languageId]);
            }
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
                ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }

}
