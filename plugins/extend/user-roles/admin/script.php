<?php

use AdminBread\Action\CreateAction;
use AdminBread\Action\DeleteAction;
use AdminBread\Action\EditAction;
use AdminBread\Action\ListAction;
use AdminBread\Bread;
use AdminBread\Util\Columns;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Message;
use Sunlight\User;
use Sunlight\Util\Form;
use SunlightExtend\UserRoles\UserRolesPlugin;

/* --- kontrola jadra --- */
defined('SL_ROOT') or exit;

class UserRolesModule extends Bread
{
    /** @var array */
    protected $columnsDefinition = [];

    // parts of SQL query to load user data
    protected $userQuery;

    /**
     * Predpriprava sloupcu
     */
    protected function initColumns()
    {
        // load plugin config
        $pluginConfig = Core::$pluginManager->getPlugins()->getExtend('user-roles')->getConfig();

        $this->columnsDefinition = [
            'user_id' => '',
            'group_id' => User::GUEST_GROUP_ID, // preset the lowest possible group
            'since' => (time() + (60 * 60 * 24 * $pluginConfig['move_since'])),
            // null = unlimited
            'until' => $pluginConfig['auto_unlimited'] ? null : (time() + (60 * 60 * 24 * $pluginConfig['move_until'])),
        ];
    }

    protected function setup()
    {
        $this->module = 'users-roles';
        $this->table = UserRolesPlugin::ROLE_TABLE;
        $this->path = __DIR__;

        $this->initColumns();
        $columns = Columns::withAliasImploded($this->tableAlias,
            array_keys($this->columnsDefinition)
        );


        /** @var ListAction $listAction */
        $listAction = $this->actions[ListAction::getIdentifier()];
        $listAction->title = _lang('adminbread.action.title.list');
        $listAction->columns[] = $columns;
        // custom query variant
        $this->userQuery = User::createQuery('t.user_id');
        $listAction->query = 'SELECT %columns%, bg.id AS borrowed_id, bg.title AS borrowed_title,' . $this->userQuery['column_list']
            . ' FROM %table% %table_alias%'
            . ' JOIN ' . DB::table('user_group') . ' AS bg ON(t.group_id=bg.id)'
            . ' ' . $this->userQuery['joins']
            . '  WHERE(%cond%)';

        $listAction->paginator_size = 15;
        $listAction->query_order_by = 't.user_id ASC';


        /** @var CreateAction $createAction */
        $createAction = $this->actions[CreateAction::getIdentifier()];
        $createAction->title = _lang('adminbread.action.title.create');
        $createAction->initial_data = $this->columnsDefinition;
        $createAction->handler = [$this, 'editHandler'];


        /** @var EditAction $editAction */
        $editAction = $this->actions[EditAction::getIdentifier()];
        $editAction->title = _lang('adminbread.action.title.edit');
        $editAction->handler = [$this, 'editHandler'];


        /** @var DeleteAction $delAction */
        $delAction = $this->actions[DeleteAction::getIdentifier()];
        $delAction->title = _lang('adminbread.action.title.delete');
        $delAction->extra_columns[] = 't.*';


        foreach ($this->actions as $action) {
            $action->on_before = function (&$params, &$action, $bread) {
                $params['item_name'] = _lang('userroles.tpl.item');
            };
        }
    }

    /**
     * @param $args
     * @return array|mixed
     */
    public function editHandler($args)
    {
        // validate the data sent by the method
        $validateResult = $this->validate($_POST, $args);
        $errors = [];
        do {
            // if there are errors in the processing ... stop processing
            if (count($validateResult[0]) > 0) {
                $errors[] = Message::list($validateResult[0]);
                break;
            }
            // everything okay?
            $args['success'] = true;
            return $validateResult[1];
        } while (false);
        return $errors;
    }

    /**
     * @param array $post
     * @param array $actionArgs
     * @return array
     */
    function validate(array $post, array $actionArgs)
    {
        $createMode = $actionArgs['create'];
        $errors = [];

        // remove triggers
        unset($post['_list_' . $this->uid], $post['_edit_' . $this->uid], $post['_del_' . $this->uid]);

        $since = Form::loadTime('since');
        $until = Form::loadTime('until');

        // check time paradox
        if ($until != null && $until < $since) {
            $errors[] = _lang('userroles.edit.error.time_paradox');
        }

        $validatedData = [
            'user_id' => $post['user_id'],
            'group_id' => $post['group_id'],
            'since' => $since,
            'until' => $until,
        ];

        return [$errors, $validatedData];
    }
}

$breadPlugin = new UserRolesModule();
$output .= '<br>' . $breadPlugin->run();
