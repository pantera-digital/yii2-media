<?php

use pantera\media\Module;
use yii\db\Migration;

/**
 * Handles adding sort to table `media`.
 */
class m180626_011113_add_sort_column_to_media_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(Module::getInstance()->tableName, 'sort', $this->integer()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn(Module::getInstance()->tableName, 'sort');
    }
}
