<?php

namespace app\modules\customers\helpers;

use app\modules\customers\entities\Customer;
use app\modules\customers\entities\CustomerAccount;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

class CustomerHelper
{
	public static function sexList()
	{
		return [
			CustomerAccount::SEX_MALE => Yii::t('customers', 'Male'),
			CustomerAccount::SEX_FEMALE => Yii::t('customers', 'Female'),
		];
	}

	public static function sexName($sex)
	{
		return ArrayHelper::getValue(self::sexList(), $sex);
	}

    public static function statusList()
    {
        return [
            Customer::STATUS_ACTIVE => Yii::t('customers', 'Active'),
            Customer::STATUS_DRAFT => Yii::t('customers', 'Draft'),
        ];
    }

    public static function statusName($status)
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    public static function statusLabel($status)
    {
        switch ($status) {
            case Customer::STATUS_DRAFT:
                $class = 'label label-default';
                break;
            case Customer::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }

    public static function normalizePhone($phone) {
        return str_replace('-', '', str_replace(' ', '', str_replace('(', '', str_replace(')', '', $phone))));
    }

    public static function beautyPhone($phone)
    {
        $p = str_split($phone);
        return $p[0].$p[1].' ('.$p[2].$p[3].$p[4].') '.$p[5].$p[6].$p[7].'-'.$p[8].$p[9].$p[10].$p[11];
    }
}