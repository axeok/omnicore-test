<?php

namespace app\controllers;

use app\components\PsrAction;
use Phly\Http\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
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

        $action->request = $this->createServerRequest();

        return $action;
    }

    /**
     * @return ServerRequestInterface
     */
    protected function createServerRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }
}
