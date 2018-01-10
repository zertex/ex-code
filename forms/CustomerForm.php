<?php

namespace app\modules\customers\forms;

use yii\base\Model;
use app\modules\customers\entities\Customer;
use yii\helpers\Json;
use yii\web\UploadedFile;
use Yii;

class CustomerForm extends Model
{
    public $id;
    public $base_id;
    public $name;
    public $company;
    public $photo;
    public $comment;
    public $city;
    public $country;
    public $website;
    public $files;
    public $status;
    public $user_id;
    public $account;

    public $_customer;

    public function __construct(Customer $customer = null, $config = [])
    {
	    $this->account = new CustomerAccountForm();

        if ($customer) {
            $this->id = $customer->id;
            $this->base_id = $customer->base_id;
            $this->name = $customer->name;
            $this->company = $customer->company;
            $this->photo = $customer->photo;
            $this->comment = $customer->comment;
            $this->city = $customer->city;
            $this->country = $customer->country;
            $this->website = $customer->website;
            $this->files = $customer->files;
            $this->status = $customer->status;

            if ($this->files) {
                $this->files = Json::decode($this->files);
            }
            $this->_customer = $customer;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [[
                'name',
                'company',
                'city',
                'country',
            ], 'string', 'max' => 255],
            ['files', 'each', 'rule' => ['file']],
            ['photo', 'image', 'extensions' => 'jpg,png,gif', 'skipOnEmpty' => true],
            ['comment', 'string'],
            ['website', 'url'],
            [['status', 'base_id', 'user_id'], 'integer'],
	        ['account', 'safe'],
        ];
    }

    /*public function emailUniqueValidator($attribute, $params)
    {
        if (!$this->hasErrors()){
            $unique = $this->_customer ? Customer::find()->where([$attribute => $this->$attribute, 'base_id' => $this->_customer->base_id])->andWhere(['<>','id',$this->_customer->id])->count() : Customer::find()->where([$attribute => $this->$attribute, 'base_id' => $this->base_id])->count();
            if ($unique)
            {
                $this->addError($attribute, Yii::t('customers', 'E-mail is already use'));
            }
        }
    }

    public function phoneUniqueValidator($attribute, $params)
    {
        if (!$this->hasErrors()){
            $unique = $this->_customer ? Customer::find()->where([$attribute => $this->$attribute, 'base_id' => $this->_customer->base_id])->andWhere(['<>','id',$this->_customer->id])->count() : Customer::find()->where([$attribute => $this->$attribute, 'base_id' => $this->base_id])->count();
            if ($unique)
            {
                $this->addError($attribute, Yii::t('customers', 'Phone is already use'));
            }
        }
    }*/

    public function attributeLabels()
    {
        return [
            'base_id' => Yii::t('customers', 'Database'),
            'name' => Yii::t('customers', 'ID Name'),
            'company' => Yii::t('customers', 'Company'),
            'photo' => Yii::t('customers', 'Photo'),
            'comment' => Yii::t('customers', 'Comment'),
            'city' => Yii::t('customers', 'City'),
            'country' => Yii::t('customers', 'Country'),
            'website' => Yii::t('customers', 'Web site'),
            'files' => Yii::t('customers', 'Files'),
            'status' => Yii::t('customers', 'Status'),
            'created_at' => Yii::t('customers', 'Created At'),
            'updated_at' => Yii::t('customers', 'Updated At'),
        ];
    }

	public function attributeHints() {
		return [
			'name' => Yii::t('customers', 'Company, title, name etc.'),
		];
	}

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->files = UploadedFile::getInstances($this, 'files');
            $this->photo = UploadedFile::getInstance($this, 'photo');
	        if ($this->account->birth_date) {
		        $this->account->birth_date = strtotime($this->account->birth_date);
	        }
            return true;
        }
        return false;
    }
}