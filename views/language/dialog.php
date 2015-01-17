<?php
use yii\helpers\Html;
?>
<div id="translate-manager-dialog">
    <div class="translate-manager-message">
        <div class="clearfix">
            <?= Html::label(Yii::t('language', 'Choosing the language of translation'), 'translate-manager-language-source')?>
            <?= Html::dropDownList('translate-manager-language-source', '', array_merge(['' => Yii::t('language', 'Source')], $languageTranslate->getTranslatedLanguageNames()), ['id' => 'translate-manager-language-source']) ?>
        </div>
        <div class="clearfix">
            <?= Html::label(Yii::t('language', 'Text to be translated') , 'translate-manager-message') ?>
            <?= Html::textarea('translate-manager-message', $languageSource->message, ['readonly' => 'readonly', 'id' => 'translate-manager-message']) ?>
        </div>
    </div>
    <div class="translate-manager-message">
        <div class="clearfix">
            <?= Html::hiddenInput('translate-manager-language_id', $languageTranslate->language, ['id' => 'translate-manager-language_id'])?>
            <?= Html::hiddenInput('translate-manager-id', $languageSource->id, ['id' => 'translate-manager-id'])?>
            <?= Html::label(Yii::t('language', 'Translated text'), 'translate-manager-translation') ?>
            <?= Html::textarea('translate-manager-translation', $languageTranslate->translation, ['id' => 'translate-manager-translation'])?>
        </div>
    </div>
</div>
