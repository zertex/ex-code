<?php

namespace app\modules\customers\forms\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\customers\entities\Customer;

class CustomerSearch extends Model
{
    public $id;
    public $base_id;
    public $name;
    public $status;
    public $source;
    public $user_id;

    public function rules()
    {
        return [
            [['id', 'base_id', 'status', 'user_id'], 'integer'],
            [['name', 'source'], 'safe'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params)
    {
        $query = Customer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'base_id' => $this->base_id,
            'status' => $this->status,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'source', $this->source]);

        return $dataProvider;
    }
}
