<?php

namespace app\controllers;

use app\models\Fragrances;
use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use app\models\Orders;


class OrdersController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Настройка CORS
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];
        
        // Аутентификация Bearer Token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'], // Все действия требуют аутентификации
        ];
        
        return $behaviors;
    }
    
    /**
     * Создание заказа через API
     * POST /api/orders
     */
    public function actionCreate()
    {
        // Проверяем авторизацию (уже сделано через authenticator)
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 401;
            return [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ];
        }
        
        $request = Yii::$app->request;
        
        // Проверяем обязательные поля
        $fragrances_id = $request->post('fragrances_id');
        $adress = $request->post('adress');
        $pay_metod = $request->post('pay_metod');
        
        // Валидация обязательных полей
        $errors = [];
        if (!$fragrances_id) $errors['fragrances_id'] = ['The fragrances_id field is required.'];
        if (!$adress) $errors['adress'] = ['The adress field is required.'];
        if (!$pay_metod) $errors['pay_metod'] = ['The pay_metod field is required.'];
        
        if (!empty($errors)) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $errors
                ]
            ];
        }
        
        // Проверяем существование товара
        $fragrance = Fragrances::findOne($fragrances_id);
        if (!$fragrance) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Product not found'
                ]
            ];
        }
        
        // Создаем заказ
        $order = new Orders();
        $order->user_id = Yii::$app->user->id;
        $order->fragrances_id = $fragrances_id;
        $order->adress = $adress;
        $order->pay_metod = $pay_metod;
        $order->status = Orders::STATUS_CONFIRMED;
        
        // Опциональное поле comment
        if ($request->post('comment')) {
            $order->comment = $request->post('comment');
        }
        
        // Устанавливаем дату создания
        $order->creat_orders = date('Y-m-d H:i:s');
        
        // Сохраняем заказ
        if ($order->save()) {
            // Возвращаем данные созданного заказа
            $responseData = [
                'id_orders' => $order->id_orders,
                'user_id' => $order->user_id,
                'fragrances_id' => $order->fragrances_id,
                'adress' => $order->adress,
                'status' => $order->status,
                'pay_metod' => $order->pay_metod,
                'comment' => $order->comment,
                'created_at' => $order->creat_orders,
            ];
            
            Yii::$app->response->statusCode = 201;
            return $responseData;
        } else {
            // Ошибки валидации при сохранении
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $order->getErrors()
                ]
            ];
        }
    }
    
    /**
     * Получение истории заказов пользователя
     * GET /api/orders
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $orders = Orders::find()
            ->where(['user_id' => $userId])
            ->with('fragrance') // Жадная загрузка связи с товаром
            ->orderBy(['creat_orders' => SORT_DESC])
            ->all();
        
        $result = [];
        foreach ($orders as $order) {
            $result[] = [
                'id_orders' => $order->id_orders,
                'fragrances_id' => $order->fragrances_id,
                'product_name' => $order->fragrance ? $order->fragrance->product_name : 'Товар не найден',
                'adress' => $order->adress,
                'status' => $order->status,
                'pay_metod' => $order->pay_metod,
                'comment' => $order->comment,
                'price' => $order->fragrance ? $order->fragrance->price : 0,
                'created_at' => $order->creat_orders,
            ];
        }
        
        return [
            'data' => $result,
            'meta' => [
                'total' => count($result),
                'user_id' => $userId
            ]
        ];
    }
    
    /**
     * Просмотр конкретного заказа
     * GET /api/orders/{id}
     */
    public function actionView($id)
    {
        $order = Orders::find()
            ->where(['id_orders' => $id])
            ->with('fragrance')
            ->one();
        
        if (!$order) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Order not found'
                ]
            ];
        }
        
        // Проверяем, что пользователь может просматривать только свои заказы
        if ($order->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 403;
            return [
                'error' => [
                    'code' => 403,
                    'message' => 'Access denied'
                ]
            ];
        }
        
        return [
            'id_orders' => $order->id_orders,
            'user_id' => $order->user_id,
            'fragrances_id' => $order->fragrances_id,
            'product_name' => $order->fragrance ? $order->fragrance->product_name : 'Товар не найден',
            'adress' => $order->adress,
            'status' => $order->status,
            'pay_metod' => $order->pay_metod,
            'comment' => $order->comment,
            'price' => $order->fragrance ? $order->fragrance->price : 0,
            'volume_ml' => $order->fragrance ? $order->fragrance->volume_ml : 0,
            'created_at' => $order->creat_orders,
        ];
    }
    
    /**
     * Обновление статуса заказа (только для администраторов)
     * PUT /api/orders/{id}/status
     */
    public function actionUpdateStatus($id)
    {
        // Проверяем, что пользователь - администратор
        // Здесь должна быть ваша логика проверки роли администратора
        
        $order = Orders::findOne($id);
        
        if (!$order) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Order not found'
                ]
            ];
        }
        
        $status = Yii::$app->request->post('status');
        
        if (!$status) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => [
                        'status' => ['The status field is required.']
                    ]
                ]
            ];
        }
        
        // Проверяем допустимость статуса
        $allowedStatuses = [
            Orders::STATUS_CONFIRMED,
            Orders::STATUS_ASSEMBLY,
            Orders::STATUS_SENT,
            Orders::STATUS_RECEIVED
        ];
        
        if (!in_array($status, $allowedStatuses)) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => [
                        'status' => ['Invalid status value.']
                    ]
                ]
            ];
        }
        
        $order->status = $status;
        
        if ($order->save()) {
            return [
                'id_orders' => $order->id_orders,
                'status' => $order->status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $order->getErrors()
                ]
            ];
        }
    }
    
    /**
     * CORS поддержка
     */
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }
}