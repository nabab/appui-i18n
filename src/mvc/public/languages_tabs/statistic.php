<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */


$ctrl->obj->url = APPUI_I18N_ROOT.'languages/statistic';
//$ctrl->combo('Translations\'s statistics');
$ctrl->add_data(['id_user'=> $ctrl->inc->user->get_id()])->combo('Statistic of translations', true);


