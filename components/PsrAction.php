<?php

namespace app\components;

use Psr\Http\Message\ServerRequestInterface;
use yii\base\Action;

/**
 * Class PsrAction
 *
 * @package app\components
 */
class PsrAction extends Action
{
    /** @var ServerRequestInterface */
    public ServerRequestInterface $request;

    /**
     * @inheritDoc
     *
     * @param ServerRequestInterface $request
     */
    public function __construct($id, $controller, ServerRequestInterface $request, $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->request = $request;
    }
}
