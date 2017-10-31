<?php

namespace BetaPeak\Testing\Traits;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseMigrationsWithSeeding
{
    /**
     * If set to true, the migration will be of type 'refresh', not 'fresh'
     *
     * @var boolean
     */
    protected $useRefreshMigrations;

    /**
     * A seeder class name
     *
     * @var string
     */
    protected $seederClass;

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan("migrate:{$this->migrationType()}", $this->migrationArguments());

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });
    }

    /**
     * If a $useRefreshMigrations property on the test instance is set to true, migration type is 'refresh'.
     * Defaults to migration type is 'fresh'.
     *
     * @return string
     */
    protected function migrationType() {
        return (isset($this->useRefreshMigrations) && $this->useRefreshMigrations === true) ? 'refresh' : 'fresh';
    }

    /**
     * If a $seederClass property is set on the test instance, this is being used as the seeder name.
     * If $seederClass is not specified, it defaults to 'DatabaseSeeder'
     *
     * @return array
     */
    protected function migrationArguments() {
        $migrationArguments = ['--seed' => ''];

        if(isset($this->seederClass)){
            $migrationArguments['--seeder'] = $this->seederClass;
        }

        return $migrationArguments;
    }
}