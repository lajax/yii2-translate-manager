<?php

namespace lajax\translatemanager\bundles;

/**
 * Contains javascript files necessary for translating javascript messages on the client side (`lajax.t()` calls).
 * This bundle includes files for **all** active languages.
 *
 * Usually you don't need this bundle. Register this if you need message files for all languages at the same
 * time (for example you want to combine/compress them in one file in production).
 *
 * @author Semenihin Maksim <semenihin.maksim@gmail.com>
 */
class FullTranslationPluginAsset extends TranslationPluginAsset
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'lajax\translatemanager\bundles\AllLanguageItemPluginAsset',
    ];
}
