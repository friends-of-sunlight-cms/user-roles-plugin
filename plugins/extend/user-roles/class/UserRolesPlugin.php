<?php

namespace SunlightExtend\UserRoles;

use Sunlight\Database\Database as DB;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\ExtendPlugin;
use Sunlight\Router;
use Sunlight\User;
use SunlightExtend\UserRoles\Component\RoleReader;

class UserRolesPlugin extends ExtendPlugin
{
    public const ROLE_TABLE = 'user_role';

    /**
     * Register cron task
     */
    public function cronInit(array $args): void
    {
        $args['tasks'] += [
            'user_role_cleanup' => [
                'interval' => $this->getConfig()->offsetGet('cron_interval'),
                'callback' => [$this, 'cleanupExpiredRoles']
            ]
        ];
    }

    /**
     * Cron task
     */
    public function cleanupExpiredRoles(int $last): void
    {
        DB::delete(self::ROLE_TABLE, 'until<' . time() . ' AND (until!=-1 OR until IS NOT NULL)');
    }

    public function onUserAuthSuccess(array $args): void
    {
        $roleReader = new RoleReader(self::ROLE_TABLE, $args['user']['id']);

        // id of the group that the user is a tenant
        $groupIds = $roleReader->getRoleIds(true);

        if ($groupIds !== null) {
            // column names (privileges) by map
            $columns = implode(',', array_keys(User::getPrivilegeMap()));

            // all permissions from selected groups
            $groups = DB::queryRows(
                "SELECT " . $columns . " FROM " . DB::table('user_group')
                . " WHERE id IN(" . DB::arr($groupIds) . ")"
            );

            // filtering of only active privileges from groups (deactivation from another 'role' is not desirable)
            $borrowedPrivilege = [];
            foreach ($groups as $group) {
                $borrowedPrivilege = array_filter(
                    $group,
                    function ($value) {
                        return $value == "1";
                    }
                );
            }

            // merged privileges - leased privileges to users
            $args['group'] = array_merge($args['group'], $borrowedPrivilege);
        }
    }

    public function onUserDeleteAfter(array $args): void
    {
        // delete borrowed groups after deleting a user
        DB::delete(self::ROLE_TABLE, "user_id=" . DB::val($args['user']['id']));
    }

    public function onUsersGroupsAfter(array $args): void
    {
        if (!User::hasPrivilege('manageroles')) {
            return;
        }

        $role_module = '<a class="button block" href="' . _e(Router::admin('users-roles')) . '">'
            . '<img src="' . _e($this->getWebPath() . '/public/images/roles.png') . '" alt="new" class="icon">' . _lang('userroles.users.manageroles')
            . "</a>\n";

        $args['output'] .= '<h2>' . _lang('userroles.users.roles') . '</h2>' . $role_module;
    }

    public function onRegUsersSubmodule(array $args): void
    {
        // add children to users
        $args['admin']->modules['users']['children'][] = 'users-roles';
        // reg role module
        $args['admin']->modules['users-roles'] = [
            'title' => _lang('userroles.users.manageroles'),
            'access' => User::hasPrivilege('manageroles'),
            'parent' => 'users',
            'script' => __DIR__ . DIRECTORY_SEPARATOR . '../admin/script.php',
        ];
    }

    public function onRegPrivileges(array $args): void
    {
        $args['privileges'] = array_merge(
            $args['privileges'],
            [
                'manageroles' => true,
            ]
        );
    }

    public function onAdminEditGroupRights(array $args): void
    {
        $args['rights'][] = [
            'title' => _lang('userroles.priv.manageroles.title'),
            'rights' => [
                [
                    'name' => 'manageroles',
                    'label' => _lang('userroles.priv.manageroles.label'),
                    'help' => _lang('userroles.priv.manageroles.help'),
                    'dangerous' => true,
                ],
            ],
        ];
    }

    /**
     * ============================================================================
     *  EXTEND CONFIGURATION
     * ============================================================================
     */

    protected function getConfigDefaults(): array
    {
        return [
            'move_since' => 0,
            'move_until' => 30,
            'auto_unlimited' => false,
            'cron_interval' => 43200, // default: 43200 => 12h
        ];
    }

    public function getAction(string $name): ?PluginAction
    {
        if ($name === 'config') {
            return new ConfigAction($this);
        }
        return parent::getAction($name);
    }
}
