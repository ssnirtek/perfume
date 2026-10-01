<?php
namespace app\controllers;

use app\models\User;
use Yii;


class UserController extends FunctionController
{
    public $modelClass = 'app\models\User'; 

 public function actionCreate()
 {
    $data=Yii::$app->request->post();
    $user=new User();
    $user->load($data, '');
    if (!$user->validate()) return $this->validation($user);
    $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);;
    $user->save();
   return $this->send(204, null);
    } 
   
}
