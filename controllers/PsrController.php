<?php

namespace app\controllers;

use app\components\PsrAction;
use yii\base\InvalidConfigException;

/**
 * Class PsrController
 *
 * @package app\controllers
 */
abstract class PsrController extends ApiController
{
    /**
     * @param string id
     *
     * @return PsrAction|null
     * @throws InvalidConfigException
     */
    public function createAction($id): ?PsrAction
    {
        $action = parent::createAction($id);

        if (!$action instanceof PsrAction) {
            throw new InvalidConfigException("Action \"$id\" must be instance of " . PsrAction::class);
        }

        return $action;
    }
}
