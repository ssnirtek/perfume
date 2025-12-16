<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;
use app\models\User;

class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Настройка CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];
        
        // Отключаем аутентификацию для публичных действий
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
            'except' => ['register', 'login', 'options'],
        ];
        
        return $behaviors;
    }
    
    /**
     * API: Регистрация пользователя
     * POST /api/register
     */
    public function actionRegister()
    {
        $request = Yii::$app->request;
        
        $fio = $request->post('fio');
        $phone = $request->post('phone');
        $email = $request->post('email');
        $password = $request->post('password');
        
        // Валидация
        $errors = [];
        if (!$fio) $errors['fio'] = ['The fio field is required.'];
        if (!$phone) $errors['phone'] = ['The phone field is required.'];
        if (!$email) $errors['email'] = ['The email field is required.'];
        if (!$password) $errors['password'] = ['The password field is required.'];
        
        if (!empty($errors)) {
            Yii::$app->response->statusCode = 422;
            return $this->formatError(422, 'Validation error', $errors);
        }
        
        // Проверка уникальности
        if (User::find()->where(['email' => $email])->exists()) {
            Yii::$app->response->statusCode = 409;
            return $this->formatError(409, 'User with this email already exists');
        }
        
        if (User::find()->where(['phone' => $phone])->exists()) {
            Yii::$app->response->statusCode = 409;
            return $this->formatError(409, 'User with this phone already exists');
        }
        
        // Создание пользователя
        $user = new User();
        $user->fio = $fio;
        $user->phone = $phone;
        $user->email = $email;
        $user->setPassword($password);
        $user->role = 'user';
        $user->creat_user = date('Y-m-d H:i:s');
        $user->access_token = Yii::$app->security->generateRandomString();
        $user->token_expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        if ($user->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'data' => [
                    'id_user' => $user->id_user,
                    'fio' => $user->fio,
                    'email' => $user->email,
                    'token' => $user->access_token,
                    'expires_at' => $user->token_expires_at
                ],
                'message' => 'User registered successfully'
            ];
        }
        
        Yii::$app->response->statusCode = 422;
        return $this->formatError(422, 'Validation error', $user->getErrors());
    }
    
    /**
     * API: Вход в систему
     * POST /api/login
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        
        $email = $request->post('email');
        $password = $request->post('password');
        
        if (!$email || !$password) {
            Yii::$app->response->statusCode = 422;
            return $this->formatError(422, 'Validation error', [
                'email' => $email ? null : ['Email is required'],
                'password' => $password ? null : ['Password is required']
            ]);
        }
        
        // Ищем пользователя
        $user = User::find()->where(['email' => $email])->one();
        
        if (!$user || !$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 401;
            return $this->formatError(401, 'Unauthorized', [
                'phone' => 'Invalid email or password'
            ]);
        }
        
        // Обновляем токен
        $user->access_token = Yii::$app->security->generateRandomString();
        $user->token_expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        $user->save();
        
        return [
            'success' => true,
            'data' => [
                'token' => $user->access_token,
                'expires_at' => $user->token_expires_at,
                'user' => [
                    'id_user' => $user->id_user,
                    'fio' => $user->fio,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]
        ];
    }
    
    /**
     * API: Получение информации о текущем пользователе
     * GET /api/me
     */
    public function actionMe()
    {
        $user = Yii::$app->user->identity;
        
        return [
            'success' => true,
            'data' => [
                'id_user' => $user->id_user,
                'fio' => $user->fio,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ]
        ];
    }
    
    /**
     * Форматирование ошибок
     */
    private function formatError($code, $message, $errors = [])
    {
        return [
            'error' => [
                'code' => $code,
                'message' => $message,
                'errors' => $errors
            ]
        ];
    }
    
    /**
     * CORS поддержка
     */
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }
}