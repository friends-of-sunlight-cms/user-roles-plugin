<?php

use Sunlight\Database\Database as DB;
use Sunlight\Plugin\PluginInstaller;

return new class extends PluginInstaller {

    protected function doInstall(): void
    {
        $this->loadSqlDump(__DIR__ . '/Resources/db/install.sql');
    }

    protected function doUninstall(): void
    {
        $this->loadSqlDump(__DIR__ . '/Resources/db/uninstall.sql');
    }

    protected function verify(): bool
    {
        $tables = $this->checkTables([DB::table('user_role')]);
        return (count($tables) === 0);
    }
};