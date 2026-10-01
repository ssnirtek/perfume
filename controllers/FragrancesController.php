<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Fragrances;
use app\models\Category;

class FragrancesController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        
        return $behaviors;
    }
    
    /**
     * Поиск ароматов
     * GET /fragrances?gender=Male&category=1&min_price=1000&max_price=5000
     */
    public function actionSearch()
    {
        $request = Yii::$app->request;
        
        $query = Fragrances::find();
        
        
        if ($gender = $request->get('gender')) {
            $query->andWhere(['gender' => $gender]);
        }
        
        
        if ($category_id = $request->get('category')) {
            $query->andWhere(['category_id' => $category_id]);
        }
        
        
        if ($min_price = $request->get('min_price')) {
            $query->andWhere(['>=', 'price', $min_price]);
        }
        
        if ($max_price = $request->get('max_price')) {
            $query->andWhere(['<=', 'price', $max_price]);
        }
        
        if ($search = $request->get('search')) {
            $query->andWhere(['like', 'product_name', $search]);
        }
        
        
        $sort = $request->get('sort', 'id_fragrances');
        $order = $request->get('order', 'ASC');
        $query->orderBy([$sort => $order]);
        

        $page = $request->get('page', 1);
        $per_page = $request->get('per_page', 20);
        $offset = ($page - 1) * $per_page;
        
        $total = $query->count();
        $fragrances = $query->offset($offset)->limit($per_page)->all();
        
        
        $result = [];
        foreach ($fragrances as $fragrance) {
            $category = Category::findOne($fragrance->category_id);
            
            $result[] = [
                'id' => $fragrance->id_fragrances,
                'product_name' => $fragrance->product_name,
                'category_id' => $fragrance->category_id,
                'category_name' => $category->category_name,
                'description' => $fragrance->description,
                'price' => (float)$fragrance->price,
                'volume_ml' => (int)$fragrance->volume_ml,
                'gender' => $fragrance->gender,
                'raiting_f' => (int)$fragrance->raiting_f,
                'image_f' => $fragrance->image_f,
            ];
        }
        
        return [
            'total' => $total,
            'page' => (int)$page,
            'per_page' => (int)$per_page,
            'pages' => ceil($total / $per_page),
            'data' => $result,
        ];
    }
    
    /**
     * Получить один аромат
     * GET /fragrances/5
     */
    public function actionView($id)
    {
        $fragrance = Fragrances::find()->where(['category_id'=> $id])->all();
        
        if (!$fragrance) {
            Yii::$app->response->statusCode = 404;
            return [
                'error' => [
                    'code' => 404,
                    'message' => 'Product not found'
                ]
            ];
        }
        
      
        return $fragrance;
    }
    
    /**
     * CORS поддержка
     */
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }
}