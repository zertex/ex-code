<?php

namespace app\modules\customers\entities\queries;

use app\modules\customers\entities\Customer;
use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => Customer::STATUS_ACTIVE]);
    }

    public function draft()
    {
        return $this->andWhere(['status' => Customer::STATUS_DRAFT]);
    }

    public function hasEmail()
    {
        return $this->andWhere(['!=', 'email', '']);
    }

    public function hasPhone()
    {
        return $this->andWhere(['!=', 'phone', '']);
    }

    public function subEmail()
    {
        return $this->andWhere(['unsub_email' => 0]);
    }

    public function subSms()
    {
        return $this->andWhere(['unsub_sms' => 0]);
    }

    public function their()
    {
        return $this->andWhere(['user_id' => \Yii::$app->user->id]);
    }
}