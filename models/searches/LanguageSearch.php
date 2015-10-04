<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lajax\translatemanager\models\Language;

/**
 * LanguageSearch represents the model behind the search form about `common\models\Language`.
 */
class LanguageSearch extends Language {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii'], 'safe'],
            [['status'], 'integer'],
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
        $query = Language::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'lower(language_id)', strtolower($this->language_id)])
                ->andFilterWhere(['like', 'lower(language)', strtolower($this->language)])
                ->andFilterWhere(['like', 'lower(country)', strtolower($this->country)])
                ->andFilterWhere(['like', 'lower(name)', strtolower($this->name)])
                ->andFilterWhere(['like', 'lower(name_ascii)', strtolower($this->name_ascii)]);

        return $dataProvider;
    }

}
