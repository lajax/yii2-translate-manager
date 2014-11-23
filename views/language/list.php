<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use lajax\translatemanager\models\Language;
use yii\widgets\Pjax;

$this->title = Yii::t('language', 'List of languages');
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>
    <?= $this->title ?>
</h1>
<div id="languages">

    <?php
    Pjax::begin([
        'id' => 'languages',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'language_id',
            'name_ascii',
            [
                'format' => 'text',
                'filter' => Language::getStatusNames(),
                'attribute' => 'status',
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'status'],
                'label' => Yii::t('language', 'Status'),
                'content' => function ($language) {
            return Html::activeDropDownList($language, 'status', Language::getStatusNames(), ['class' => 'status', 'id' => $language->language_id]);
        },
            ],
            [
                'format' => 'html',
                'attribute' => Yii::t('language', 'Statistic'),
                'content' => function ($language) {
                    return '<span class="statistic"><span style="width:' . $language->getGridStatistic() . '%"></span><i>' . $language->getGridStatistic() . '%</i></span>';
                },
            ],
            [
                'format' => 'html',
                'attribute' => Yii::t('language', 'Translate'),
                'content' => function ($language) {
                    return Html::a(Yii::t('language', 'Translate'), Url::toRoute(['language/translate', 'language_id' => $language->language_id]), ['class' => 'translate btn btn-xs btn-success']);
                },
                    ],
                ],
            ]);
            Pjax::end();
            ?>
</div>