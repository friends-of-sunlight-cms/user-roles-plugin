<?php
use Sunlight\Xsrf;
?>
<form action="" method="post">
    <p><?= _lang('userroles.del.question') ?></p>
    <?= Xsrf::getInput() ?>
    <input type="submit" name="<?= $submit_trigger; ?>" value="<?= $submit_text; ?>">
</form>