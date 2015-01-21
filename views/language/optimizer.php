<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

$this->title = Yii::t('language', 'Optimise database');
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="w2-info" class="alert-info alert fade in">
<?= Yii::t('language', '{n, plural, =0{No entries} =1{One entry} other{# entries}} were removed!', ['n' => $items_count])?>
</div>
<div class="language-default-index">
    <h1><?= $this->title ?></h1>
</div>