<?php

use yii\db\Migration;

/**
 * Handles the creation of table `media`.
 */
class m171204_011217_create_media_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{media}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'type' => $this->string()->null(),
            'size' => $this->integer()->null(),
            'model' => $this->string()->null(),
            'model_id' => $this->integer()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('media-model', '{{media}}', 'model');
        $this->createIndex('media-model-model_id', '{{media}}', [
            'model',
            'model_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{media}}');
    }
}
