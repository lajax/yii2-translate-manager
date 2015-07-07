<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\Module;

/**
 * LanguageSourceSearch represents the model behind the search form about `common\models\LanguageSource`.
 */
class LanguageSourceSearch extends LanguageSource
{

    /**
     * @var string Translated message.
     */
    public $translation;

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
        Yii::$app->session->setFlash('TM-language__id', $params['language_id']);
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
                    'label' => Yii::t('language', 'Translation')
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->joinWith('languageTranslateByLanguage');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category' => $this->category
        ]);

        $query->andFilterWhere(['like', 'message', $this->message]);

        $query->joinWith(['languageTranslateByLanguage' => function ($query) {
            if ($this->translation) {
                if ($this->translation == Module::getInstance()->searchEmptyCommand){
                    $query->andWhere(['or', ['translation'=>null], ['translation'=>'']]);
                }else{
                    $query->andWhere(['like', 'translation', $this->translation]);
                }
            }
        }]);

        return $dataProvider;
    }

}