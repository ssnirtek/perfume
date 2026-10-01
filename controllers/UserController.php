<?php
namespace app\controllers;
use Yii;
use app\models\User;


class UserController extends FunctionController
{
    public $modelClass = 'app\models\User'; 

 public function actionCreate()
 {
    $data=Yii::$app->request->post();
    $user=new User();
    
    $user->load ($data, '');
    if (!$user->validate()) return $this->validation($user);
    $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);;
    $user->save();
   return $this->send(204, null);
    } 
   
    public function actionLogin()
    {
        $data = Yii::$app->request->post();
        
       
        if (empty($data['email']) || empty($data['password'])) {
            return $this->send(422, ['error' => 'Email and password required']);
        }
        
    
        $user = User::findOne(['email' => $data['email']]);
        
        if (!$user) {
            return $this->send(403, ['error' => 'User not found']);
        }
        
       
        if (!Yii::$app->getSecurity()->validatePassword($data['password'], $user->password)) {
            return $this->send(403, ['error' => 'Invalid password']);
        }
        
       
        $token =  Yii::$app->getSecurity()->generateRandomString();
        
       
        $user->access_token = $token;
       
        $user->save();
        
        return $this->send(200, [
            'success' => true,
            'token' => $token,
            'expires_in' => 3600,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'fio' => $user->fio
            ]
        ]);
    }

}