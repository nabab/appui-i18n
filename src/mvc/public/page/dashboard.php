<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 */
//transfer to $model
$source_langs = $ctrl->db->getColumnValues('bbn_i18n', 'lang');
if ( !empty($ctrl->post['id_project'])){
  $ctrl->addData([
    'id_project' => $ctrl->post['id_project'],
    'source_langs' => $source_langs
  ]);
}
$ctrl->combo(_('Projects Dashboard'), true);