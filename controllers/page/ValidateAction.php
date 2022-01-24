<?php

namespace app\controllers\page;

use app\components\{DtoFactory, PsrAction};
use Yii;
use UserDto;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use ReflectionException;

/**
 * Class ValidateAction
 *
 * @package app\controllers\page
 */
class ValidateAction extends PsrAction
{
    /** @var string */
    protected const DTO_CLASS = UserDto::class;

    /** @var UserDto */
    protected $dto;

    /** @var array */
    protected array $errors = [];

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function run(): array
    {
        $dto = $this->createDto();

        $this->validateDto($dto);
        $this->setHttpStatus();

        return $this->errors;
    }

    /**
     * @return UserDto
     * @throws InvalidConfigException
     * @throws ReflectionException
     */
    protected function createDto(): UserDto
    {
        $factory = Yii::createObject([
            'class' => DtoFactory::class,
            'dtoClass' => self::DTO_CLASS,
            'dtoFieldsMap' => [
                'email' => 'emailAddress',
            ]
        ]);

        return $factory->constructDto($this->request);
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
            Yii::$app->response->setStatusCode(400);
        }
    }
}
