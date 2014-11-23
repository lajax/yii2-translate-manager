<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = Yii::t('language', 'Translate {language_id}', ['language_id' => $language_id]);
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>
<?= $this->title ?>
</h1>
    <?= Html::hiddenInput('language_id', $language_id, ['id' => 'language_id']); ?>
<div id="translates">
    <?php
    Pjax::begin([
        'id' => 'translates'
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'category',
            [
                'format' => 'text',
                'attribute' => 'message',
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'message'],
                'label' => Yii::t('language', 'Source'),
                'content' => function ($data) {
            return Html::activeTextarea($data, 'message', ['name' => 'LanguageSource[' . $data->id . ']', 'class' => 'form-control source', 'readonly' => 'readonly']);
        },
            ],
            [
                'format' => 'text',
                'attribute' => 'translation',
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'translation'],
                'label' => Yii::t('language', 'Translation'),
                'content' => function ($data) {
            if ($data->languageTranslate === null) {
                return Html::textarea('LanguageTranslate[' . $data->id . ']', '', ['class' => 'form-control translation', 'tabindex' => $data->id]);
            }

            return Html::activeTextarea($data->languageTranslate, 'translation', ['name' => 'LanguageTranslate[' . $data->id . ']', 'class' => 'form-control translation', 'data-id' => $data->id, 'tabindex' => $data->id]);
        },
            ],
            [
                'format' => 'html',
                'attribute' => Yii::t('language', 'Action'),
                'content' => function ($data) {
                    return Html::button(Yii::t('language', 'Save'), ['type' => 'button', 'data-id' => $data['id'], 'class' => 'btn btn-lg btn-success']);
                },
                    ],
                ],
        ]);
        Pjax::end();
        ?>
</div>