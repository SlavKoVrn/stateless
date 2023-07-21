<?php

namespace frontend\modules\api\controllers;

use frontend\modules\api\models\LoginForm;
use Yii;
use yii\rest\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function actionIndex()
    {
        return 'api';
    }

    public function actionLogin()
    {
        $model = new LoginForm;
        $model->load(Yii::$app->request->bodyParams,'');
        if ($token = $model->auth()){
            return [
                'token' => $token->token,
                'expired' => date(DATE_RFC3339,$token->expired_at),
            ];
        }else{
            return $model;
        }
    }

    protected function verbs()
    {
        return [
            'login' => ['post'],
        ];
    }
}
