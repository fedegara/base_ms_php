<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class AddTablesAnalyticsGoogleGa4 extends AbstractMigration
{
    public function up()
    {
        $this->createTableBrowser();
        $this->createTableDaily();
        $this->createTablePeriodData();
        $this->createTableDevice();
        $this->createTableTraffic();
        $this->createTableTrafficMedium();
        $this->createTableAudienceByAge();
        $this->createTableAudience();
        $this->createTableAudienceLocation();
    }

    private function deleteTable($table)
    {
        $table = $this->table($table);
        if ($table->exists()) {
            $table->drop()->save();
        }
    }

    public function down()
    {
        $this->deleteTable('analytics_google_ga4_browser');
        $this->deleteTable('analytics_google_ga4_daily');
        $this->deleteTable('analytics_google_ga4_period_data');
        $this->deleteTable('analytics_google_ga4_device');
        $this->deleteTable('analytics_google_ga4_traffic');
        $this->deleteTable('analytics_google_ga4_traffic_medium');
        $this->deleteTable('analytics_google_ga4_audience_age');
        $this->deleteTable('analytics_google_ga4_audience');
        $this->deleteTable('analytics_google_ga4_audience_location');
    }

    private function createTableBrowser()
    {
        $this->table('analytics_google_ga4_browser', [
            'id' => false,
            'primary_key' => ['id_browser', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('id_browser', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'userEngagementDuration',
            ])
            ->addColumn('browser', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_browser',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'browser',
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
            ->addIndex(['id_browser'], [
                'name' => 'id_browser',
                'unique' => false,
            ])
            ->addIndex(['profile_id'], [
                'name' => 'profile_id',
                'unique' => false,
            ])
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();

    }
    private function createTableDaily()
    {
        $this->table('analytics_google_ga4_daily', [
            'id' => false,
            'primary_key' => ['profile_id', 'date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'userEngagementDuration',
            ])
            ->addColumn('date', 'date', [
                'null' => false,
                'after' => 'profile_id',
            ])
            ->addIndex(['profile_id'], [
                'name' => 'profile_id',
                'unique' => false,
            ])
            ->addIndex(['date'], [
                'name' => 'date',
                'unique' => false,
            ])
            ->create();
    }
    private function createTablePeriodData()
    {
        $this->table('analytics_google_ga4_period_data', [
            'id' => false,
            'primary_key' => ['profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'userEngagementDuration',
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
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableDevice()
    {
        $this->table('analytics_google_ga4_device', [
            'id' => false,
            'primary_key' => ['id_deviceCategory', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('id_deviceCategory', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'userEngagementDuration',
            ])
            ->addColumn('devicecategory', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_deviceCategory',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'devicecategory',
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
            ->addIndex(['id_deviceCategory'], [
                'name' => 'id_deviceCategory',
                'unique' => false,
            ])
            ->addIndex(['profile_id'], [
                'name' => 'profile_id',
                'unique' => false,
            ])
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableTraffic()
    {
        $this->table('analytics_google_ga4_traffic', [
            'id' => false,
            'primary_key' => ['id_medium', 'id_source', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('id_medium', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'userEngagementDuration',
            ])
            ->addColumn('medium', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_medium',
            ])
            ->addColumn('id_source', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'medium',
            ])
            ->addColumn('source', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_source',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'source',
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
            ->addIndex(['id_medium'], [
                'name' => 'id_medium',
                'unique' => false,
            ])
            ->addIndex(['id_source'], [
                'name' => 'id_source',
                'unique' => false,
            ])
            ->addIndex(['profile_id'], [
                'name' => 'profile_id',
                'unique' => false,
            ])
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->addIndex(['profile_id', 'start_date', 'end_date', 'period_id', 'id_medium'], [
                'name' => 'idx_source_stats',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableTrafficMedium()
    {
        $this->table('analytics_google_ga4_traffic_medium', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('totalUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
            ])
            ->addColumn('newUsers', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'totalUsers',
            ])
            ->addColumn('screenPageViews', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'newUsers',
            ])
            ->addColumn('engagedSessions', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'screenPageViews',
            ])
            ->addColumn('userEngagementDuration', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'engagedSessions',
            ])
            ->addColumn('id_medium', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'userEngagementDuration',
            ])
            ->addColumn('medium', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_medium',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'medium',
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
            ->addIndex(['id_medium'], [
                'name' => 'id_medium',
                'unique' => false,
            ])
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->addIndex(['profile_id', 'start_date', 'end_date', 'period_id'], [
                'name' => 'idx_source_stats',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableAudienceByAge()
    {
        $this->table('analytics_google_ga4_audience_age', [
            'id' => false,
            'primary_key' => ['age', 'gender', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('age', 'string', [
                'null' => false,
                'limit' => 6,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
            ])
            ->addColumn('gender', 'string', [
                'null' => false,
                'limit' => 6,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'age',
            ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'gender',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
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
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableAudience()
    {
        $this->table('analytics_google_ga4_audience', [
            'id' => false,
            'primary_key' => ['gender', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('gender', 'string', [
                'null' => false,
                'limit' => 6,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
            ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'gender',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
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
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();
    }
    private function createTableAudienceLocation()
    {
        $this->table('analytics_google_ga4_audience_location', [
            'id' => false,
            'primary_key' => ['country', 'city', 'profile_id', 'period_id', 'start_date', 'end_date'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('country', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
            ])
            ->addColumn('city', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'country',
            ])
            ->addColumn('sessions', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'city',
            ])
            ->addColumn('profile_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'sessions',
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
            ->addIndex(['period_id'], [
                'name' => 'period_id',
                'unique' => false,
            ])
            ->create();
    }
}
