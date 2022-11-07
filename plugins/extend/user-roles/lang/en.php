<?php

return [
    // config
    'cfg.move_since' => 'Start in <small>(days)</small>',
    'cfg.move_until' => 'End in <small>(days)</small>',
    'cfg.auto_unlimited' => 'Unlimited role validity',
    'cfg.cron_interval' => 'Cleaning of expired roles',

    // privileges
    'priv.manageroles.title' => 'Administration - rights in user management',
    'priv.manageroles.label' => 'User role management',
    'priv.manageroles.help' => 'allow managing user roles',

    // administration
    'users.roles' => 'Roles',
    'users.manageroles' => 'User role management',
    'tpl.item' => 'User role',
    'list.main-group' => 'Main group',
    'list.borrowed-group' => 'Borrowed group',
    'list.validity' => 'Validity',
    'until.unlimited' => 'unlimited',
    'edit.borrow' => 'Borrow a group',
    'edit.since' => 'Validity from',
    'edit.until' => 'Validity to',
    'edit.until.hint' => 'Hint: empty = unlimited',
    'edit.unlimited' => 'Unlimited validity',
    'edit.error.time_paradox' => 'Validity cannot end before it begins.',
    'del.question' => 'Do you really want to remove this item?',
];
