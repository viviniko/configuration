<?php

namespace Viviniko\Configuration\Console\Commands;

use Viviniko\Support\Console\CreateMigrationCommand;

class ConfigurationTableCommand extends CreateMigrationCommand
{
    /**
     * @var string
     */
    protected $name = 'configuration:table';

    /**
     * @var string
     */
    protected $description = 'Create a migration for the configuration service table';

    /**
     * @var string
     */
    protected $stub = __DIR__.'/stubs/configuration.stub';

    /**
     * @var string
     */
    protected $migration = 'create_configuration_table';
}
