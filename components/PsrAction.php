<?php

namespace app\components;

use Yii;
use Phly\Http\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

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
     * @throws InvalidConfigException|NotInstantiableException
     */
    public function init(): void
    {
        parent::init();

        $this->request = $this->createServerRequest();
    }

    /**
     * @return ServerRequestInterface
     * @throws InvalidConfigException|NotInstantiableException
     */
    protected function createServerRequest(): ServerRequestInterface
    {
        $server = ServerRequestFactory::normalizeServer($_SERVER);
        $files = ServerRequestFactory::normalizeFiles($_FILES);
        $headers = ServerRequestFactory::marshalHeaders($server);

        $request = Yii::$container->get(ServerRequestInterface::class, [
            $server,
            $files,
            ServerRequestFactory::marshalUriFromServer($server, $headers),
            ServerRequestFactory::get('REQUEST_METHOD', $server, 'GET'),
            'php://input',
            $headers
        ]);

        return $request
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST);
    }
}
