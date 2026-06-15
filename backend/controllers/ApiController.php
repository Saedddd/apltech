<?php

namespace app\controllers;

use Yii;  
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\Response;

class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        unset($behaviors['authenticator']);
        
        // CORS настройки
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:5173', 'http://localhost:5174', 'https://apltech-front-eight.vercel.app'], 
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];
        
        
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        return $behaviors;
    }
    
    // Обработка OPTIONS запросов для CORS
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->headers->set('Allow', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        return [];
    }
}