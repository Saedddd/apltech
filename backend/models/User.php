<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User extends ActiveRecord implements IdentityInterface
{
    private static $jwtSecret = 'your-secret-key-minimum-32-characters-long';
    
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public static function findIdentity($id) 
    { 
        return static::findOne($id); 
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        Yii::info('=== JWT DEBUG ===', 'auth');
        Yii::info('Token: ' . substr($token, 0, 50) . '...', 'auth');
        
        if (empty($token)) {
            return null;
        }

        try {
            
            $decoded = JWT::decode($token, new Key(self::$jwtSecret, 'HS256'));
            Yii::info('Decoded UID: ' . ($decoded->uid ?? 'null'), 'auth');
            
            if ($decoded && isset($decoded->uid)) {
                return static::findOne($decoded->uid);
            }
        } catch (\Exception $e) {
            Yii::error('JWT error: ' . $e->getMessage(), 'auth');
            return null;
        }
        
        return null;
    }

    public function getId() 
    { 
        return $this->id; 
    }
    
    public function getAuthKey() 
    { 
        return $this->auth_key; 
    }
    
    public function validateAuthKey($authKey) 
    { 
        return $this->auth_key === $authKey; 
    }
    
    public function validatePassword($password): bool 
    { 
        return Yii::$app->security->validatePassword($password, $this->password_hash); 
    }
}