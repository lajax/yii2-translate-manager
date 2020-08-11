<?php
use yii\db\Migration;

/**
 * @author Pavel Filippov <pofilippov@gmail.com>
 *
 * @since 1.0
 */
class m200811_080242_synchronization extends Migration
{
    public function up()
    {
        $this->addColumn('language_source', 'sync_id', $this->string(256)->defaultValue('')->after('id'));
        $this->addColumn('language_translate', 'status', $this->string(15)->defaultValue('done')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('language_source', 'sync_id');
        $this->dropColumn('language_translate', 'status');
    }
}
