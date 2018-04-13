<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 */

$ctrl->obj->url = APPUI_I18N_ROOT.'page/dashboard';

$ctrl->add_data([
  'url' => $ctrl->obj->url ,
  'id_project' => $ctrl->post['id_project']
])
->combo('', true);