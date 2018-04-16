<?php

namespace pantera\media\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class MediaSearch extends Media
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'size', 'model_id'], 'integer'],
            [['name', 'model', 'file', 'type', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Media::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'size' => $this->size,
            'model_id' => $this->model_id,
            'created_at' => $this->created_at,
        ]);
        $query->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->type])
            ->andFilterWhere(['like', 'model', $this->type]);
        return $dataProvider;
    }
}
