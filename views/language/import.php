<?php

use lajax\translatemanager\models\ImportForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ImportForm */

$this->title = Yii::t('language', 'Import');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="language-export col-sm-6">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>

    <?= $form->field($model, 'importFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('language', 'Import'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>