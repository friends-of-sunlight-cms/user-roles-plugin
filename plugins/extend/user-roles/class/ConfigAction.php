<?php

namespace SunlightExtend\UserRoles;

use Fosc\Plugin\Action\ConfigAction as FoscConfigAction;

class ConfigAction extends FoscConfigAction
{
    protected function getFields(): array
    {
        $langPrefix = "%p:userroles.cfg";

        $fields = [];

        $fields += $this->generateField('move_since', $langPrefix, '%number', ['class' => 'inputsmall', 'min' => 0]);
        $fields += $this->generateField('move_until', $langPrefix, '%number', ['class' => 'inputsmall', 'min' => 0]);
        $fields += $this->generateField('auto_unlimited', $langPrefix, '%checkbox');
        $fields += $this->generateField('cron_interval', $langPrefix, '%select', [
            'class' => 'inputsmall',
            'select_options' => [
                3600 => '1h', 7200 => '2h', 18000 => '5h',
                43200 => '12h', 86400 => '24h', 259200 => '3d',
                604800 => '7d', 1209600 => '14d', 2592000 => '30d',
            ],
        ], 'text');
        return $fields;
    }
}
