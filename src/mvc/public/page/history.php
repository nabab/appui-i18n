<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 11/01/18
 * Time: 14.43
 */
$ctrl->obj->url = APPUI_I18N_ROOT.'page/history';
$is_dev = $ctrl->inc->user->isDev();



$ctrl->
  add_data([
    'is_dev' => $is_dev,
])
  ->combo('Users activity', true);
