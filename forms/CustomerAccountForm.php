<?php

namespace app\modules\customers\forms;

use yii\base\Model;
use app\modules\customers\entities\CustomerAccount;
use app\modules\customers\entities\Customer;
use yii\web\UploadedFile;
use Yii;

class CustomerAccountForm extends Model
{
    public $id;
    public $name;
    public $lastname;
    public $position;
    public $photo;
    public $email;
    public $phone;
    public $comment;
    public $title;
    public $sub_email;
    public $sub_sms;
    public $sex;
    public $birth_date;
    public $customer_id;

    private $_account;

    public function __construct(CustomerAccount $account = null, $config = [])
    {
        if ($account) {
            $this->id = $account->id;
            $this->name = $account->name;
	        $this->lastname = $account->lastname;
            $this->position = $account->position;
            $this->photo = $account->photo;
            $this->email = $account->email;
            $this->phone = $account->phone;
            $this->comment = $account->comment;
            $this->title = $account->title;
            $this->sub_email = $account->sub_email;
            $this->sub_sms = $account->sub_sms;
            $this->sex = $account->sex;
            $this->birth_date = $account->birth_date;
			$this->customer_id = $account->customer_id;

            $this->_account = $account;
        }
        parent::__construct($config);
    }

	public function rules()
	{
		return [
			[['name', 'customer_id'], 'required'],
			[['title', 'comment', 'lastname'], 'string'],
			[['sub_email', 'sub_sms', 'customer_id', 'birth_date'], 'integer'],
			[['name', 'position'], 'string', 'max' => 64],
			[['phone'], 'string', 'max' => 24],
			[['email'], 'email'],
			['photo', 'image', 'extensions' => 'jpg,png,gif', 'skipOnEmpty' => true],
			[['sex'], 'string', 'max' => 1],
			[['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
			[['email'], 'emailUniqueValidator', 'skipOnEmpty' => true],
			[['phone'], 'phoneUniqueValidator', 'skipOnEmpty' => true],
		];
	}

    public function emailUniqueValidator($attribute, $params)
    {
        if (!$this->hasErrors()){
            $unique = $this->_account ? CustomerAccount::find()->where([$attribute => $this->$attribute, 'customer_id' => $this->_account->customer_id])->andWhere(['<>','id',$this->_account->id])->count() : CustomerAccount::find()->where([$attribute => $this->$attribute, 'customer_id' => $this->customer_id])->count();
            if ($unique)
            {
                $this->addError($attribute, Yii::t('customers', 'E-mail is already use'));
            }
        }
    }

    public function phoneUniqueValidator($attribute, $params)
    {
        if (!$this->hasErrors()){
            $unique = $this->_account ? CustomerAccount::find()->where([$attribute => $this->$attribute, 'customer_id' => $this->_account->customer_id])->andWhere(['<>','id',$this->_account->id])->count() : CustomerAccount::find()->where([$attribute => $this->$attribute, 'customer_id' => $this->customer_id])->count();
            if ($unique)
            {
                $this->addError($attribute, Yii::t('customers', 'Phone is already use'));
            }
        }
    }

	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => Yii::t('customers', 'Name'),
			'lastname' => Yii::t('customers', 'Last Name'),
			'position' => Yii::t('customers', 'Position'),
			'phone' => Yii::t('customers', 'Phone'),
			'email' => Yii::t('customers', 'E-mail'),
			'photo' => Yii::t('customers', 'Photo'),
			'title' => Yii::t('customers', 'E-mail title'),
			'sub_email' => Yii::t('customers', 'Enable E-mail'),
			'sub_sms' => Yii::t('customers', 'Enable SMS'),
			'sex' => Yii::t('customers', 'Sex'),
			'birth_date' => Yii::t('customers', 'Birth Date'),
			'comment' => Yii::t('customers', 'Comment'),
			'customer_id' => Yii::t('customers', 'Customer'),
			'created_at' => Yii::t('customers', 'Created At'),
			'updated_at' => Yii::t('customers', 'Updated At'),
		];
	}

	public function attributeHints() {
		return [
			'title' => Yii::t('customers', 'This header is inserted at the beginning of the letter. For example: Dear Ivan Ivanovich!'),
		];
	}

	public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->photo = UploadedFile::getInstance($this, 'photo');
            if ($this->birth_date) {
		        $this->birth_date = strtotime($this->birth_date);
	        }
            return true;
        }
        return false;
    }
}