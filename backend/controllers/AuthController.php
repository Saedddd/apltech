<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends ApiController
{
    public function actionLogin(): array
    {
        $params = Yii::$app->request->getBodyParams();
        $username = $params['username'] ?? null;
        $password = $params['password'] ?? null;

        if (!$username || !$password) {
            throw new BadRequestHttpException('Укажите username и password');
        }

        /** @var User $user */
        $user = User::findOne(['username' => $username]);

        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Неверный логин или пароль');
        }

        /** @var \sizeg\jwt\Jwt $jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $now = new \DateTimeImmutable();

        $token = $jwt->getBuilder()
            ->issuedBy('http://localhost:8080')
            ->permittedFor('http://localhost:5173')
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 day'))
            ->withClaim('uid', $user->getId())
            ->getToken($signer, $key);

        return [
            'status' => 'success',
            'token' => $token->toString(),
            'user' => ['id' => $user->getId(), 'username' => $user->username]
        ];
    }
}