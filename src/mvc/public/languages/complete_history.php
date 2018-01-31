<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 11/01/18
 * Time: 14.43
 */
$ctrl->obj->url = APPUI_I18N_ROOT.'languages/complete_history';
$is_admin = $ctrl->inc->user->is_admin();


$ctrl->add_data(['is_admin' => $is_admin])->combo('Complete history of translations', true);
