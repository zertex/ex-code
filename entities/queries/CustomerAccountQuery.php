<?php
/**
 * Created by Error202
 * Date: 14.12.2017
 */

namespace app\modules\customers\entities\queries;


use yii\db\ActiveQuery;

class CustomerAccountQuery extends ActiveQuery
{
	public function onEmail()
	{
		return $this->andWhere(['sub_email' => 1]);
	}

	public function onSms()
	{
		return $this->andWhere(['sub_sms' => 1]);
	}

	public function subEmail()
	{
		return $this->andWhere(['unsub_email' => 0]);
	}

	public function subSms()
	{
		return $this->andWhere(['unsub_sms' => 0]);
	}

	public function hasEmail()
	{
		return $this->andWhere(['!=', 'email', '']);
	}

	public function hasPhone()
	{
		return $this->andWhere(['!=', 'phone', '']);
	}
}