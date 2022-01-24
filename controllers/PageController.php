<?php

namespace app\controllers;

use app\controllers\page\ValidateAction;

class PageController extends PsrController
{
    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'validate' => ValidateAction::class,
        ];
    }
}
