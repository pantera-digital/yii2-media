<?php

use pantera\media\Module;
use yii\db\Migration;

/**
 * Class m200325_062523_make_model_id_as_string
 */
class m200325_062523_make_model_id_as_string extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(Module::getInstance()->tableName, 'model_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(Module::getInstance()->tableName, 'model_id', $this->integer()->null());
    }
}
