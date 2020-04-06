<?php

$ctrl->obj->success = false;
if ( !empty($ctrl->post['id_project']) && !empty($ctrl->post['language']) ){
	$project = new \bbn\appui\project($ctrl->db, $ctrl->post['id_project']);
	$ctrl->obj->success = $project->change_project_lang($ctrl->post['language']);
}