<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.3
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use lajax\translatemanager\models\Language;

/* @var $this yii\web\View */
/* @var $model lajax\translatemanager\models\Language */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-form col-sm-6">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'language_id')->textInput(['maxlength' => 5]) ?>

    <?= $form->field($model, 'language')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 32]) ?>

    <?= $form->field($model, 'name_ascii')->textInput(['maxlength' => 32]) ?>

    <?= $form->field($model, 'status')->dropDownList(Language::getStatusNames()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('language', 'Create') : Yii::t('language', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>