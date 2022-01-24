<?php

namespace app\controllers;

use Yii;
use yii\filters\Cors;
use yii\rest\Controller;

/**
 * Class ApiController
 *
 * @package app\controllers
 */
abstract class ApiController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['cors'] = [
            'class' => Cors::class,
        ];

        if (Yii::$app->request->isOptions) {
            unset($behaviors['verbFilter']);
        }

        unset($behaviors['authenticator']);

        return $behaviors;
    }
}
