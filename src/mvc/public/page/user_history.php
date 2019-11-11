<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\controller */

$ctrl->obj->url = APPUI_I18N_ROOT.'languages/user_history';
$id_user = $ctrl->inc->user->get_id();
$userName =$ctrl->inc->user->get_name($id_user);
$ctrl->combo($userName.'\'s translations');
