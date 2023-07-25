<?php

namespace frontend\modules\api\controllers;

use common\models\User;
use common\rbac\Rbac;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

/**
 * Site controller
 */
class ProfileController extends ActiveController
{

    public $modelClass = User::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::class,
            HttpBearerAuth::class,
            QueryParamAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actions(){
        $actions = parent::actions();
        unset($actions['create'],$actions['update'],$actions['delete']);
        return $actions;
    }

    protected function verbs()
    {
        return [
            'index' => ['get'],
        ];
    }

    public function actionIndex()
    {
        return $this->findModel();
    }

    private function findModel()
    {
        return User::findOne(Yii::$app->user->id);
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->can(Rbac::MANAGE_PROFILE,[
            'user_id' => Yii::$app->user->id
        ])) return true;
        return false;
    }

}
