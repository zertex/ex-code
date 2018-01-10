<?php
/**
 * Created by Error202
 * Date: 12.12.2017
 */

namespace app\modules\customers\entities;


use app\modules\customers\entities\queries\CustomerAccountQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "{{%customer_accounts}}".
 *
 * @property int $id
 * @property string $name
 * @property string $lastname
 * @property string $position
 * @property string $phone
 * @property string $email
 * @property string $photo
 * @property string $title
 * @property int $sub_email
 * @property int $sub_sms
 * @property string $sex
 * @property int $birth_date
 * @property string $comment
 * @property int $customer_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $unsub_email
 * @property int $unsub_sms
 *
 * @property Customer $customer
 */

class CustomerAccount extends ActiveRecord
{
	const SEX_MALE = 'm';
	const SEX_FEMALE = 'f';

	public static function create(
		$name,
		$lastname,
		$position,
		$phone,
		$email,
		$photo,
		$title,
		$sub_email,
		$sub_sms,
		$sex,
		$birth_date,
		$comment,
		$customer_id
	)
	{
		$account = new CustomerAccount();
		$account->name = $name;
		$account->lastname = $lastname;
		$account->position = $position;
		$account->phone = $phone;
		$account->email = $email;
		$account->photo = $photo;
		$account->title = $title;
		$account->sub_email = $sub_email;
		$account->sub_sms = $sub_sms;
		$account->sex = $sex;
		$account->birth_date = $birth_date;
		$account->comment = $comment;
		$account->customer_id = $customer_id;
		return $account;
	}

	public function edit(
		$name,
		$lastname,
		$position,
		$phone,
		$email,
		$photo,
		$title,
		$sub_email,
		$sub_sms,
		$sex,
		$birth_date,
		$comment,
		$customer_id
	)
	{
		$this->name = $name;
		$this->lastname = $lastname;
		$this->position = $position;
		$this->phone = $phone;
		$this->email = $email;
		$this->photo = $photo;
		$this->title = $title;
		$this->sub_email = $sub_email;
		$this->sub_sms = $sub_sms;
		$this->sex = $sex;
		$this->birth_date = $birth_date;
		$this->comment = $comment;
		$this->customer_id = $customer_id;
	}

	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}

	public static function tableName()
	{
		return '{{%customer_accounts}}';
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

	public static function find()
	{
		return new CustomerAccountQuery(static::class);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer()
	{
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
	}
}