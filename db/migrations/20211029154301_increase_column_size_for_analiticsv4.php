<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\BunkerAbstractMigration;

class IncreaseColumnSizeForAnaliticsv4 extends BunkerAbstractMigration
{
    /**
     * Bunker migration
     *
     * Features (methods):
     *  - getTableIndexes(table_name) - Return table index list
     *  - removeIndexesWithColumn(table_name, column_name) - Remove all index with specific column
     *  - supportUserId() - Return Support SystemUserId (throw exception if not found)
     *  - getPrimaryKeyFields($tb_name) - Return fields array in primary key. False otherwise.
     *
     * FOR DEFAULT PHINX MIGRATION USE
     *  --template=vendor/robmorgan/phinx/src/Phinx/Migration/Migration.template.php.dist
     */



    public function up()
    {
        $table = $this->table('analytics_google_ga4_audience');
        $table->changeColumn('gender', 'string', [
            'null' => false,
            'limit' => 10,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->save();
        $table = $this->table('analytics_google_ga4_audience_age');
        $table->changeColumn('gender', 'string', [
            'null' => false,
            'limit' => 10,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->changeColumn('age', 'string', [
            'null' => false,
            'limit' => 10,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->save();
        $this->table('analytics_google_ga4_search_used', [
            'id' => false,
            'primary_key' => ['profile_id', 'period_id', 'start_date', 'end_date', 'id_searchUsed'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('period_id', 'string', [
                'null' => false,
                'limit' => 5,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'profile_id',
            ])
            ->addColumn('start_date', 'datetime', [
                'null' => false,
                'after' => 'period_id',
            ])
            ->addColumn('end_date', 'datetime', [
                'null' => false,
                'after' => 'start_date',
            ])
            ->addColumn('id_searchUsed', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'end_date',
            ])
            ->addColumn('avg_search_per_session', 'float', [
                'null' => false,
                'after' => 'id_searchUsed',
            ])
            ->addIndex(['id_searchUsed'], [
                'name' => 'id_searchUsed',
                'unique' => false,
            ])
            ->create();

    }


    public function down()
    {
        $table = $this->table('analytics_google_ga4_audience');
        $table->changeColumn('gender', 'string', [
            'null' => false,
            'limit' => 6,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->save();
        $table = $this->table('analytics_google_ga4_audience_age');
        $table->changeColumn('gender', 'string', [
            'null' => false,
            'limit' => 6,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->changeColumn('age', 'string', [
            'null' => false,
            'limit' => 6,
            'collation' => 'utf8_general_ci',
            'encoding' => 'utf8',
        ])->save();
        $table = $this->table('analytics_google_ga4_search_used');
        if ($table->exists()) {
            $table->drop()->save();
        }
    }
}
