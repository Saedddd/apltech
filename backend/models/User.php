<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public static function findIdentity($id) { return static::findOne($id); }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        try {
            /** @var \sizeg\jwt\Jwt $jwt */
            $jwt = Yii::$app->jwt;
            $parsedToken = $jwt->loadToken($token);
            if ($jwt->validateToken($parsedToken)) {
                return static::findOne($parsedToken->claims()->get('uid'));
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function getId() { return $this->id; }
    public function getAuthKey() { return $this->auth_key; }
    public function validateAuthKey($authKey) { return $this->auth_key === $authKey; }
    public function validatePassword($password): bool { return Yii::$app->security->validatePassword($password, $this->password_hash); }
}