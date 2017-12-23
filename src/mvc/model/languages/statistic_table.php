<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/


if ($model->data['path'] && $id_project = $model->data['id_project']){
  $i18n = new \bbn\appui\i18n($model->db);
  $project = new \bbn\appui\project($model->db, $id_project);
  	$langs = $project->get_langs_code();
    $lang = $project->get_lang();
    

  foreach( $model->data['path'] as $m ){
    $id_option = $m['id_option'];
   	$path_code = $m['code'];
    

	
    //comments in model/strings_tab
    $parent = $model->inc->options->parent($id_option);
    $to_explore = constant($parent['code']).$path_code;
		$i18n->analyse_folder($to_explore, true);
    
    //$i18n->result() is the result of the strings found in the files of this path(id_option)
    $count_exp = 0;
    if ( !empty($todo = $i18n->result()) ){
    	foreach ( $todo as $i ){
        
        $count_exp += (int)$model->db->rselect('bbn_i18n', 'id', [ 'exp' => $i, 'lang' => $lang ]);
      
        
      }
      
    }
    
    	  
    }
 return [
  'count_exp' => $count_exp
	];   
}