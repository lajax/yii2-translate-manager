<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.4
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $oldDataProvider \yii\data\ArrayDataProvider */

?>
<?php if ($oldDataProvider->totalCount > 1) : ?>

    <?= Html::button(Yii::t('language', 'Select all'), ['id' => 'select-all', 'class' => 'btn btn-primary']) ?>

    <?= Html::button(Yii::t('language', 'Delete selected'), ['id' => 'delete-selected', 'class' => 'btn btn-danger']) ?>

<?php endif ?>

<?php if ($oldDataProvider->totalCount > 0) : ?>

    <?=

    GridView::widget([
        'id' => 'delete-source',
        'dataProvider' => $oldDataProvider,
        'columns' => [
            [
                'format' => 'raw',
                'attribute' => '#',
                'content' => function ($languageSource) {
                    return Html::checkbox('LanguageSource[]', false, ['value' => $languageSource['id'], 'class' => 'language-source-cb']);
                },
            ],
            'id',
            'category',
            'message',
            'languages',
            [
                'format' => 'raw',
                'attribute' => Yii::t('language', 'Action'),
                'content' => function ($languageSource) {
                    return Html::a(Yii::t('language', 'Delete'), Url::toRoute('/translatemanager/language/delete-source'), ['data-id' => $languageSource['id'], 'class' => 'delete-item btn btn-xs btn-danger']);
                },
            ],
        ],
    ]);

    ?>

<?php endif ?>