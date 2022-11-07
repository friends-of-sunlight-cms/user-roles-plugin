<?php

namespace SunlightExtend\UserRoles\Component;

use Sunlight\Database\Database as DB;

class RoleReader
{
    /** @var string */
    protected $table;
    /** @var int */
    protected $userId;
    /** @var string */
    private $activeRoleCond = '1';

    public function __construct(string $tableName, int $userId)
    {
        $this->table = $tableName;
        $this->userId = $userId;

        // prepare filter for active roles
        $time = time();
        $this->activeRoleCond = "since<" . $time . " AND (until IS NULL OR until=-1 OR until>" . $time . ")";
    }

    public function getRoleIds(bool $onlyActiveRoles = true): ?array
    {
        $q = DB::queryRows(
            "SELECT DISTINCT group_id FROM " . DB::table($this->table)
            . " WHERE user_id=" . $this->userId
            . ($onlyActiveRoles ? " AND " . $this->activeRoleCond : ""),
            null,
            'group_id'
        );
        $ids = null;
        if ($q !== false && count($q) > 0) {
            // convert string array to int array
            $ids = array_map('intval', $q);
        }
        return $ids;
    }

    private function getRoles(bool $onlyActiveRoles = true): ?array
    {
        $roles = DB::queryRows(
            "SELECT * FROM " . DB::table($this->table)
            . " WHERE user_id=" . $this->userId
            . ($onlyActiveRoles ? " AND " . $this->activeRoleCond : "")
        );

        return ($roles !== false && count($roles) > 0 ? $roles : null);
    }
}