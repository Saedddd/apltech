<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use Firebase\JWT\JWT;


class AuthController extends ApiController
{
    public $enableCsrfValidation = false;
    
    private $jwtSecret = 'your-secret-key-minimum-32-characters-long';
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        return $behaviors;
    }
    
    public function actionLogin()
    {
        $params = Yii::$app->request->getBodyParams();
        $username = $params['username'] ?? null;
        $password = $params['password'] ?? null;

        if (!$username || !$password) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Укажите username и password'];
        }

        $user = User::findOne(['username' => $username]);

        if (!$user || !$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Неверный логин или пароль'];
        }

       
        $payload = [
            'uid' => $user->id,
            'username' => $user->username,
            'iat' => time(),
            'exp' => time() + 86400
        ];
        
        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return [
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
            ]
        ];
    }
    
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->headers->set('Allow', 'POST, OPTIONS');
        return [];
    }
}