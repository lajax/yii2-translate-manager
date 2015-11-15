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
use lajax\translatemanager\models\LanguageTranslate;

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
     * @var string Source message.
     */
    public $source;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['category', 'message', 'translation', 'source'], 'safe'],
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
        $translateLanguage = Yii::$app->request->get('language_id', Yii::$app->sourceLanguage);
        $sourceLanguage = $this->_getSourceLanguage();

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
                    'asc' => ['lt.translation' => SORT_ASC],
                    'desc' => ['lt.translation' => SORT_DESC],
                    'label' => Yii::t('language', 'Translation')
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['languageTranslate' => function($query) use ($translateLanguage) {
                $query->from(['lt' => LanguageTranslate::tableName()])->onCondition(['lt.language' => $translateLanguage]);
            }]);
            $query->joinWith(['languageTranslateByLanguage' => function($query) use ($sourceLanguage) {
                $query->from(['ts' => LanguageTranslate::tableName()])->onCondition(['ts.language' => $sourceLanguage]);
            }]);            

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category' => $this->category
        ]);

        $query->andFilterWhere(['or', ['like', 'lower(message)', mb_strtolower($this->message)], ['like', 'lower(ts.translation)', mb_strtolower($this->message)]]);

        $query->joinWith(['languageTranslate' => function ($query) use ($translateLanguage)  {
            $query->from(['lt' => LanguageTranslate::tableName()])->onCondition(['lt.language' => $translateLanguage])
                  ->andFilterWhere(['like', 'lower(lt.translation)', mb_strtolower($this->translation)]);
        }]);

        $query->joinWith(['languageTranslateByLanguage' => function ($query) use ($sourceLanguage)  {
            $query->from(['ts' => LanguageTranslate::tableName()])->onCondition(['ts.language' => $sourceLanguage]);
            if ($this->message) {
                $query->orFilterWhere(['like', 'lower(ts.translation)', mb_strtolower($this->message)]);
            }
        }]);

        return $dataProvider;
    }

    /**
     * Returns the language of message source.
     * @return string
     */
    private function _getSourceLanguage()
    {
        $languageSourceSearch = \Yii::$app->request->get('LanguageSourceSearch', []);
        return isset($languageSourceSearch['source']) ? $languageSourceSearch['source'] : \Yii::$app->sourceLanguage;
    }
}
