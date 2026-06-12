<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m260610_135649_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'category_name' => $this->string()->notNull(),
            'brand_name' => $this->string()->notNull(),
            'price' => $this->integer()->notNull(),
            'rrp_price' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }
}
