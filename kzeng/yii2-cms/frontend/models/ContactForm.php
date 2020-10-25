<?php

namespace frontend\models;

// use Yii;
// use yii\base\Model;

namespace frontend\models;

use Yii;
use frontend\models\ContactForm;


/**
 * ContactForm is the model behind the contact form.
 */
//class ContactForm extends Model
class ContactForm extends \yii\db\ActiveRecord
{
    // public $name;
    // public $email;
    // public $subject;
    // public $body;
    // public $tel;
    // public $address;
    // public $open;
    // public $idcode;
    public $file;


    public static function tableName()
    {
    	return 'contact_form';
    }

    public function rules()
    {
    	return [
    			[['name', 'tel', 'address', 'subject', 'body', 'open'], 'required'],
    			
    			[['name'], 'string', 'max' => 32],
                [['tel'], 'string', 'max' => 12],
                [['idcode'], 'string', 'max' => 18],
    			[['email'], 'string', 'max' => 64],
    			[['subject'], 'string', 'max' => 128],
    			[['body'], 'string', 'max' => 1024],
    			['email', 'email'],
    	];
    }
    
    
    /**
     * @inheritdoc
     */            
    public function attributeLabels()
    {
        return [
            // 'verifycode' => '验证码',
        	'name' => '姓名',
            'tel' => '手机号码',
            'idcode' => '身份证号',
        	'email' => '电子邮箱',
        	'address' => '户口地址',
            'subject' => '信件标题',
            'body' => '信件内容',
            'enclosure' => '附件',
            'open' => '公开意愿',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            //->setFrom([$this->email => $this->name])
        	->setFrom('zengkai001@qq.com')
        	
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }


    public static function contactformAjax($params)
    {
        $name = $params['name'];
        $tel = $params['tel'];
        $idcode = $params['idcode'];
        $email = $params['email'];
        $address = $params['address'];
        $subject = $params['subject'];
        $body = $params['body'];
        $open = $params['open'];

        if($name=="" || $tel=="" || $address=="" || $subject=="" || $body=="")
             return \yii\helpers\Json::encode(['code' => -1, 'msg' => '有红色星号的为必填项！']);

        if (!preg_match('/^1\d{10}$/', $tel) && !preg_match('/^01\d{10}$/', $tel)) {
            return \yii\helpers\Json::encode(['code' => -1, 'msg' => '无效的手机号！']);
        }

        $cf = new ContactForm;
        $cf->name = $name;
        $cf->tel = $tel;
        $cf->idcode = $idcode;
        $cf->email = $email;
        $cf->address = $address;
        $cf->subject = $subject;
        $cf->body = $body;
        $cf->open = intval($open);

        $cf->save(false);

        return \yii\helpers\Json::encode(['code' => 0, 'msg' => 'OK']);
    }



}
