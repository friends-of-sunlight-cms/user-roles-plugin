<?php

use Sunlight\Admin\Admin;
use Sunlight\User;
use Sunlight\Util\Form;
use Sunlight\Xsrf;

?>
<form action="" method="post">

    <table class="list">
        <tr>
            <td><?= _lang('global.user') ?></td>
            <td><?= Admin::userSelect('user_id', [
                    'selected' => Form::post('user_id', $data['user_id']),
                    'class' => 'inputmedium'
                ]) ?></td>
        </tr>
        <tr>
            <td><?= _lang('userroles.edit.borrow') ?></td>
            <td><?= Admin::userSelect('group_id', [
                    'selected' => Form::post('group_id', $data['group_id']),
                    'group_cond' => 'level<' . User::getLevel(), 'select_groups' => true,
                    'class' => 'inputmedium'
                ]) ?></td>
        </tr>
        <tr>
            <td><?= _lang('userroles.edit.since') ?></td>
            <td><?= Form::editTime('since', (int)Form::post('since', $data['since'])) ?></td>
        </tr>
        <tr>
            <td><?= _lang('userroles.edit.until') ?></td>
            <td>
                <?= Form::editTime('until', ($data['until'] != null ? (int)Form::post('until', $data['until']) : null)) ?>
                <br><br>
                <input type="button" id="unlimited" name="unlimited" value="<?= _lang('userroles.edit.unlimited') ?>">
                <script lang="javascript">
                    $('input#unlimited').click(function () {
                        $("input[name^=until]").each(function () {
                            $(this).val('');
                        });
                    });
                </script>
                <small><?= _lang('userroles.edit.until.hint') ?></small>
            </td>
        </tr>

    </table>

    <?= Xsrf::getInput() ?>
    <input type="submit" name="<?= $submit_trigger; ?>" value="<?= $submit_text; ?>">
</form>