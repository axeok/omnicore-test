<?php

namespace app\components;

use Yii;
use ReflectionClass;
use ReflectionException;
use Psr\Http\Message\RequestInterface;
use yii\base\{Component, InvalidConfigException};
use yii\helpers\ArrayHelper;

/**
 * Class DtoFactory
 *
 * @package app\components
 */
class DtoFactory extends Component
{
    /** @var string */
    public $dtoClass;

    /** @var array */
    public $dtoFieldsMap = [];

    /**
     * @param RequestInterface $request
     *
     * @return mixed
     * @throws InvalidConfigException|ReflectionException
     */
    public function constructDto(RequestInterface $request)
    {
        $dto = Yii::createObject($this->dtoClass);

        $data = $this->getRequestData($request);

        $this->setDataToDto($data, $dto);

        return $dto;
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    protected function getRequestData(RequestInterface $request): array
    {
        return json_decode($request->getBody()->getContents(), true);
    }

    /**
     * @param array $data
     * @param $dto
     *
     * @return void
     * @throws ReflectionException
     */
    protected function setDataToDto(array $data, $dto): void
    {
        $reflection = new ReflectionClass($dto);
        $properties = ArrayHelper::getColumn($reflection->getProperties(), 'name');

        foreach ($data as $key => $value) {
            $field = $this->dtoFieldsMap[$key] ?? $key;

            if (!in_array($field, $properties)) {
                continue;
            }

            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($dto, $value);
        }
    }
}
