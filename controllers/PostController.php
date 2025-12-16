<?php

namespace app\controllers;

use app\models\Fragrances;
use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use app\models\Post;

use app\models\Category;
use app\models\User;

class PostController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
       
        
        // Аутентификация Bearer Token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view', 'options'], // Разрешаем GET без аутентификации
        ];
        
        return $behaviors;
    }
    
    /**
     * Создание отзыва к конкретному аромату
     * POST posts
     */
    public function actionCreate()
    {
        // Получаем данные из запроса
        $request = Yii::$app->request;
        $fragrances_id = $request->post('fragrances_id');
        
        // Проверяем наличие ID аромата
        if (!$fragrances_id) {
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => [
                        'fragrances_id' => ['The fragrances_id field is required.']
                    ]
                ]
            ];
        }
        
    
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
        
        
        $raiting = $request->post('raiting');
        $text = $request->post('text');
        
        if (!$raiting || !$text) {
            Yii::$app->response->statusCode = 422;
            $errors = [];
            if (!$raiting) $errors['raiting'] = ['The raiting field is required.'];
            if (!$text) $errors['text'] = ['The text field is required.'];
            
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $errors
                ]
            ];
        }
        
       
        $post = new Post();
        $post->fragrances_id = $fragrances_id;
        $post->raiting = (int)$raiting;
        $post->text = $text;
       
        $post->user_id = Yii::$app->user->identity->id_user;
        
        $post->category_id = $fragrance->category_id;
        
       
        if ($post->save()) {
            
            $category = Category::findOne($fragrance->category_id);
            $user = User::find()->where(['id_user' => $post->user_id])->one();

            $responseData = [
                'id_post' => $post->id_post,
                'user_id' => $post->user_id,
                'fio' => $user ? $user->fio : 'Неизвестный',
                'category_name' => $category ? $category->category_name : 'Не указано',
                'product_name' => $fragrance->product_name,
                'raiting' => (int)$post->raiting,
                'text' => $post->text,
                'created_at' => $post->creat_post,
            ];
            
            Yii::$app->response->statusCode = 201;
            return $responseData;
        } else {
           
            Yii::$app->response->statusCode = 422;
            return [
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $post->getErrors()
                ]
            ];
        }
    }
    
    /**
     * Получение всех отзывов для аромата
     * GET /api/posts?fragrances_id={id}
     */
    public function actionIndex()
    {
        $fragrances_id = Yii::$app->request->get('fragrances_id');
        
        if (!$fragrances_id) {
            Yii::$app->response->statusCode = 400;
            return [
                'error' => [
                    'code' => 400,
                    'message' => 'Не указан ID аромата'
                ]
            ];
        }
        
        // Проверяем существование аромата
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
        
        $posts = Post::find()
            ->where(['fragrances_id' => $fragrances_id])
            ->with(['category', 'user']) // Жадная загрузка связей
            ->orderBy(['creat_post' => SORT_DESC])
            ->all();
        
        $result = [];
        foreach ($posts as $post) {
            $result[] = [
                'id_post' => $post->id_post,
                'user_id' => $post->user_id,
                'user_fio' => $post->user ? $post->user->fio : 'Неизвестный',
                'category_name' => $post->category ? $post->category->category_name : 'Не указано',
                'product_name' => $fragrance->product_name,
                'raiting' => (int)$post->raiting,
                'text' => $post->text,
                'created_at' => $post->creat_post,
            ];
        }
        
        return [
            'data' => $result,
            'meta' => [
                'total' => count($result),
                'fragrance_id' => $fragrances_id,
                'product_name' => $fragrance->product_name,
                'average_rating' => $this->calculateAverageRating($posts)
            ]
        ];
    }
    
    /**
     * Просмотр конкретного отзыва
     * GET /api/posts/{id}
     */
    public function actionView($id)
    {
        $post = Post::find()
            ->where(['id_post' => $id])
            ->with(['category', 'user', 'fragrance'])
            ->one();
        
        if (!$post) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Review not found'
                ]
            ];
        }
        
        return [
            'id_post' => $post->id_post,
            'user_id' => $post->user_id,
            'user_fio' => $post->user ? $post->user->fio : 'Неизвестный',
            'fragrances_id' => $post->fragrances_id,
            'product_name' => $post->fragrance ? $post->fragrance->product_name : 'Аромат не найден',
            'category_name' => $post->category ? $post->category->category_name : 'Не указано',
            'raiting' => (int)$post->raiting,
            'text' => $post->text,
            'created_at' => $post->creat_post,
        ];
    }
    
    /**
     * Удаление отзыва
     * DELETE /api/posts/{id}
     */
    public function actionDelete($id)
    {
        $post = Post::findOne($id);
        
        if (!$post) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Review not found'
                ]
            ];
        }
        
        // Проверяем права: пользователь может удалять только свои отзывы
        if ($post->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 403;
            return [
                'error' => [
                    'code' => 403,
                    'message' => 'You can only delete your own reviews'
                ]
            ];
        }
        
        if ($post->delete()) {
            Yii::$app->response->statusCode = 204;
            return null;
        }
        
        Yii::$app->response->statusCode = 500;
        return [
            'error' => [
                'code' => 500,
                'message' => 'Ошибка при удалении отзыва'
            ]
        ];
    }
    
    /**
     * Расчет среднего рейтинга
     */
    private function calculateAverageRating($posts)
    {
        if (empty($posts)) {
            return 0;
        }
        
        $total = 0;
        $count = 0;
        
        foreach ($posts as $post) {
            $total += (int)$post->raiting;
            $count++;
        }
        
        return $count > 0 ? round($total / $count, 1) : 0;
    }
    
    /**
     * CORS поддержка
     */
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }
}