<?php

namespace lajax\translatemanager;

use Yii;

/**
 * Initialisation of the front end interactive translation tool.
 * The interface will only appear for users who were given the necessary privileges in the  configuration of the translatemanager module.
 *
 * Initialisation example:
 *
 * ~~~
 * 'bootstrap' => ['translatemanager'],
 * 'component' => [
 *      'translatemanager' => [
 *          'class' => 'lajax\translatemanager\Component'
 *      ]
 * ]
 * ~~~
 *
 * @author Lajos Molnar <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class Component extends \yii\base\Component
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_initTranslation();

        parent::init();
    }

    /**
     * Initialising front end translator.
     */
    private function _initTranslation()
    {
        $module = Yii::$app->getModule('translatemanager');

        if ($module->checkAccess() && $this->_checkRoles($module->roles)) {
            Yii::$app->session->set(Module::SESSION_KEY_ENABLE_TRANSLATE, true);
        }
    }

    /**
     * Determines if the current user has the necessary privileges for online translation.
     *
     * @param array $roles The necessary roles for accessing the module.
     *
     * @return bool
     */
    private function _checkRoles($roles)
    {
        if (!$roles) {
            return true;
        }

        foreach ($roles as $role) {
            if (Yii::$app->user->can($role)) {
                return true;
            }
        }

        return false;
    }
}
