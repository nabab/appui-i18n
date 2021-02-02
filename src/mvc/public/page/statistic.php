<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */


$ctrl->obj->url = APPUI_I18N_ROOT.'languages/statistic';
//$ctrl->combo('Translations\'s statistics');
$ctrl->addData(['id_user'=> $ctrl->inc->user->getId()])->combo('Statistics', true);


