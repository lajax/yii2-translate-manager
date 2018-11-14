<?php

namespace lajax\translatemanager\translation;

use Yii;
use lajax\translatemanager\translation\client\TranslatorApiClient;

/**
 * Base class for translators.
 *
 * @author moltam
 */
abstract class BaseTranslator extends \yii\base\Object implements Translator
{
    /**
     * @var array|TranslatorApiClient The client used for communication.
     */
    public $apiClient = [
        'class' => 'lajax\translatemanager\translation\client\CurlApiClient',
    ];

    public function init()
    {
        parent::init();

        $this->apiClient = Yii::createObject($this->apiClient);
    }
}
