<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.3
 */

/* @var $this yii\web\View */
/* @var $model lajax\translatemanager\models\Language */

$this->title = Yii::t('language', 'Create {modelClass}', [
    'modelClass' => 'Language',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>