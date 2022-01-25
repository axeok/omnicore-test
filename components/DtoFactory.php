<?php

namespace app\components;

use Yii;
use ReflectionClass;
use ReflectionException;
use Psr\Http\Message\RequestInterface;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

/**
 * Class DtoFactory
 *
 * @package app\components
 */
class DtoFactory
{
    /**
     * @param RequestInterface $request
     * @param array $fieldsMap
     *
     * @return mixed
     * @throws ReflectionException|InvalidConfigException|NotInstantiableException
     */
    public function constructDto(RequestInterface $request, array $fieldsMap = [])
    {
        $data = $this->getRequestData($request);

        $dto = Yii::$container->get('dto');

        $this->setDataToDto($data, $dto, $fieldsMap);

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
     * @param array $fieldsMap
     *
     * @return void
     * @throws ReflectionException
     */
    protected function setDataToDto(array $data, $dto, array $fieldsMap): void
    {
        $reflection = new ReflectionClass($dto);
        $properties = ArrayHelper::getColumn($reflection->getProperties(), 'name');

        foreach ($data as $key => $value) {
            $field = $fieldsMap[$key] ?? $key;

            if (!in_array($field, $properties)) {
                continue;
            }

            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($dto, $value);
        }
    }
}
