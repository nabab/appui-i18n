<?php

/** @var $this \bbn\Mvc\Controller */

$ctrl->obj->url = APPUI_I18N_ROOT.'languages/user_history';
$id_user = $ctrl->inc->user->getId();
$userName =$ctrl->inc->user->getName($id_user);
$ctrl->combo($userName.'\'s translations');
