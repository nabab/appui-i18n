<?php

$ctrl->obj->success = false;
if ( !empty($ctrl->post['id_project']) && !empty($ctrl->post['language']) ){
	$project = new \bbn\Appui\Project($ctrl->db, $ctrl->post['id_project']);
	$ctrl->obj->success = $project->changeProjectLang($ctrl->post['language']);
}