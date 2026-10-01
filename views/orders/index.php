<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Orders[] $orders */

$this->title = 'Мои заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $orders,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id_orders',
            'fragrances_id',
            'adress',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusLabel();
                },
            ],
            [
                'attribute' => 'pay_metod',
                'value' => function($model) {
                    return $model->getPayMethodLabel();
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('Просмотр', ['view', 'id' => $model->id_orders], [
                            'class' => 'btn btn-sm btn-primary',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>