<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 */
//transfer to $model
$source_langs = $ctrl->db->get_column_values('bbn_i18n', 'lang');
if ( !empty($ctrl->post['id_project'])){
  $ctrl->add_data([
    'id_project' => $ctrl->post['id_project'],
    'source_langs' => $source_langs
  ]);

}
$project = new \bbn\appui\project($ctrl->db, $ctrl->arguments[0]);
$ctrl->obj->url = 'internationalization/page/dashboard/' . $ctrl->arguments[0];
$ctrl->set_title($project->get_name());
$ctrl->obj->data = $ctrl->get_model('internationalization/page/dashboard/', ['id_project'=>$ctrl->arguments[0]]);

//$ctrl->combo(_('Projects Dashboard'), true);