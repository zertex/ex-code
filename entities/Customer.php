<?php
/**
 * Created by Error202
 * Date: 12.08.2017
 */

namespace app\modules\customers\entities;

use app\entities\User;
use app\modules\customers\entities\queries\CustomerQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * Customer model
 *
 * @property integer $id
 * @property integer $base_id
 * @property string $name
 * @property string $company
 * @property string $photo
 * @property string $comment
 * @property string $city
 * @property string $country
 * @property string $website
 * @property string $files
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $unsub_email
 * @property integer $unsub_sms
 * @property integer $user_id
 *
 */
class Customer extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;

    const UN_SUB_EMAIL = 1;
    const UN_SUB_SMS = 1;

    public static function create(
        $base_id,
        $user_id,
        $name,
        $company,
        $photo,
        $comment,
        $city,
        $country,
        $website,
        $files,
        $status
    )
    {
        $customer = new Customer();
        $customer->base_id = $base_id;
        $customer->user_id = $user_id;
        $customer->name = $name;
        $customer->company = $company;
        $customer->photo = $photo;
        $customer->comment = $comment;
        $customer->city = $city;
        $customer->country = $country;
        $customer->website = $website;
        $customer->files = $files;
        $customer->status = $status;
        return $customer;
    }

    public function edit(
        $base_id,
        $name,
        $company,
        $photo,
        $comment,
        $city,
        $country,
        $website,
        $files,
        $status
    )
    {
        $this->base_id = $base_id;
        $this->name = $name;
        $this->company = $company;
        $this->photo = $photo;
        $this->comment = $comment;
        $this->city = $city;
        $this->country = $country;
        $this->website = $website;
        $this->files = $files;
        $this->status = $status;
    }

    /**
     * Проверка статуса DRAFT
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Проверка статуса ACTIVE
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('customers', 'ID Name'),
            'base_id' => Yii::t('customers', 'Database'),
            'user_id' => Yii::t('customers', 'Manager'),
            'company' => Yii::t('customers', 'Company'),
            'photo' => Yii::t('customers', 'Photo'),
            'comment' => Yii::t('customers', 'Comment'),
            'city' => Yii::t('customers', 'City'),
            'website' => Yii::t('customers', 'Web site'),
            'files' => Yii::t('customers', 'Files'),
            'country' => Yii::t('customers', 'Country'),
            'status' => Yii::t('customers', 'Status'),
            'created_at' => Yii::t('customers', 'Created At'),
            'updated_at' => Yii::t('customers', 'Updated At'),
        ];
    }

	public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getBase()
    {
        return $this->hasOne(CustomerBase::className(), ['id' => 'base_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function find()
    {
        return new CustomerQuery(static::class);
    }

    public function getAccounts()
    {
    	return $this->hasMany(CustomerAccount::className(), ['customer_id' => 'id']);
    }
}