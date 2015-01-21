<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

$this->title = Yii::t('language', 'Scanning project');
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="w2-info" class="alert-info alert fade in">
<?= Yii::t('language', '{n, plural, =0{No new entries} =1{One new entry} other{# new entries}} were added!', ['n' => $items_count])?>
</div>
<div class="language-default-index">
    <h1><?= $this->title ?></h1>
</div>