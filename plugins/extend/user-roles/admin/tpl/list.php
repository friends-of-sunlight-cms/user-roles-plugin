<?php
/** @var \AdminBread\Bread $this */

use Sunlight\Database\Database as DB;
use Sunlight\Router;

?>

<?= $this->renderButton('new', _lang('adminbread.tpl.action.create'), 'create', null, $self) ?>

    <form action="" method="post">
        <table class="list list-hover list-max">
            <thead>
            <tr>
                <th>#</th>
                <th><?= _lang('global.user') ?></th>
                <th><?= _lang('userroles.list.main-group') ?></th>
                <th><?= _lang('userroles.list.borrowed-group') ?></th>
                <th><?= _lang('userroles.list.validity') ?></th>
                <th><?= _lang('adminbread.tpl.column.actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= Router::userFromQuery($this->userQuery, $d) ?></td>
                    <td><?= $d['user_group_title'] . ' <small>(ID: ' . $d['user_group_id'] . ')</small>' ?></td>
                    <td><?= $d['borrowed_title'] . ' <small>(ID: ' . $d['borrowed_id'] . ')</small>' ?></td>
                    <td<?= (($d['until'] != null && $d['until'] < time()) ? ' style="background-color: red;"' : '') ?>><?= DB::datetime($d['since']) . ' - ' . ($d['until'] != null ? DB::datetime($d['until']) : _lang('userroles.list.until.unlimited')) ?></td>
                    <td class="actions">
                        <?= $this->renderButton('edit', _lang('adminbread.tpl.action.edit'), 'edit', [$d['id']], $self) ?>
                        <?= $this->renderButton('delete', _lang('adminbread.tpl.action.delete'), 'del', [$d['id']], $self) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>

<?= $paging['paging']; ?>