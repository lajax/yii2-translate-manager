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

    public function rules() {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii'], 'safe'],
            [['status'], 'integer'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

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

        $query->andFilterWhere(['like', 'language_id', $this->language_id])
                ->andFilterWhere(['like', 'language', $this->language])
                ->andFilterWhere(['like', 'country', $this->country])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'name_ascii', $this->name_ascii]);

        return $dataProvider;
    }

}
