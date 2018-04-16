<?php

use yii\db\Migration;

/**
 * Handles adding bucket to table `media`.
 */
class m180416_021224_add_bucket_column_to_media_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{media}}', 'bucket', $this->string()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{media}}', 'bucket');
    }
}
