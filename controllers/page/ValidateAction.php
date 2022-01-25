<?php

namespace app\controllers\page;

use app\components\{DtoFactory, PsrAction};
use Psr\Http\Message\ServerRequestInterface;
use Yii;
use UserDto;
use yii\base\{DynamicModel, InvalidConfigException};
use ReflectionException;

/**
 * Class ValidateAction
 *
 * @package app\controllers\page
 */
class ValidateAction extends PsrAction
{
    /** @var string[] */
    protected const FIELDS_MAP = [
        'email' => 'emailAddress',
    ];

    /** @var array */
    protected array $errors = [];

    /** @var DtoFactory */
    protected DtoFactory $factory;

    /**
     * @inheritDoc
     *
     * @param DtoFactory $factory
     */
    public function __construct($id, $controller, ServerRequestInterface $request, DtoFactory $factory, $config = [])
    {
        parent::__construct($id, $controller, $request, $config);

        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        Yii::$container->set('dto', UserDto::class);
    }

    /**
     * @return array
     * @throws InvalidConfigException|ReflectionException
     */
    public function run(): array
    {
        $dto = $this->createDto();

        $this->validateDto($dto);
        $this->setHttpStatus();

        return $this->errors;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException|ReflectionException
     */
    protected function createDto()
    {
        return $this->factory->constructDto($this->request, self::FIELDS_MAP);
    }

    /**
     * @param UserDto $dto
     */
    protected function validateDto(UserDto $dto): void
    {
        $dynamicModel = new DynamicModel([
            'id' => $dto->getId(),
            'firstName' => $dto->getFirstName(),
            'lastName' => $dto->getLastName(),
            'email' => $dto->getEmailAddress(),
            'phoneNumber' => $dto->getPhoneNumber(),
        ]);

        $dynamicModel->addRule(['id', 'firstName', 'lastName', 'email', 'phoneNumber'], 'required');
        $dynamicModel->addRule(['firstName', 'lastName'], 'string');
        $dynamicModel->addRule(['email'], 'email');
        $dynamicModel->addRule(['phoneNumber'], 'match', ['pattern' => '/\+380\d{9}/s']);

        $dynamicModel->validate();

        $this->errors = $dynamicModel->errors;
    }

    /**
     * @return void
     */
    protected function setHttpStatus(): void
    {
        if (!empty($this->errors)) {
            Yii::$app->response->setStatusCode(422);
        }
    }
}
