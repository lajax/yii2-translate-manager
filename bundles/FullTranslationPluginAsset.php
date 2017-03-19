<?php

namespace lajax\translatemanager\bundles;

/**
 * Translation Plugin asset bundle
 *
 * @author Semenihin Maksim <semenihin.maksim@gmail.com>
 *
 * @since 1.0
 * will include all active languages
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
