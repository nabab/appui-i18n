<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */



if( !empty($model->data['id_option'])){
  $parent = $model->inc->options->parent($model->data['id_option']);
  if(defined($parent['code'])){
    $to_explore = constant($parent['code']).$model->inc->options->code($model->data['id_option']);
    $i18n = new \bbn\appui\i18n($model->db);
    $i18n->analyse_folder($to_explore, true);
    $todo = $i18n->result();

    $id_project = $model->db->select_one('bbn_projects_assets', 'id_project', ['id_option' => $model->data['id_option']]);

    $project = new \bbn\appui\project($model->db, $id_project);
    $lang = $project->get_lang();
     
		$source_glossary = $model->db->rselect_all(
      'bbn_i18n_exp',	['id_exp','expression','lang'], [ 'lang' => $lang ]);
    //$new_strings = [];
   
foreach( $todo as $t ){
  $new_strings = $model->db->rselect_all('bbn_i18n_exp', ['id_exp','expression','lang'], [ 'lang' => $lang , 'expression' => $t ] );
   die(var_dump('jkhj', count($new_strings)));
      //i$todo che non sono contenuti in source_glossary
  	if( !empty($new_strings) ){
      foreach( $new_strings as $n ){
        $id_user = $model->inc->user->get_id();
        $model->db->insert('bbn_i18n', [
          'exp' => $n,
          'last_modified' => date('Y-m-d H:i:s'),
          'id_user' => hex2bin($id_user),
        ]);
        $new_id_exp = $model->db->last_id();
        //die(var_dump($n, $id_user,date('Y-m-d H:i:s'), $new_id_exp));
        if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [ 'id_exp' => $new_id_exp, 'lang' => $lang ]) ) ){
          $new_strings_updated += (int)$model->db->insert('bbn_i18n_exp', [
            'id_exp' => hex2bin($new_id_exp),
            'lang' => $lang,
            'expression' => $n
          ]);
        }
      }
    }
  	
    else{
      $exp_in_db = [];
//LUI VA A CERCARE DI NUOVO NEL DB DOPO L'INSERT? SOURCE_GLOSSARY CONTIENE LE NUOVE?
      foreach ( $todo as $t ){

        if( $exp = array_filter($source_glossary, function($v){
          return [
            $v['lang'] => $lang,
            $v['expression'] => $t
          ];
       	 })	

        ){
          //devo prendere solo i linguaggi configurati per il progetto
          if( !empty($project->get_langs() ) ){
            foreach ($project->get_langs() as $p){
                 if($other_langs = $model->db->rselect_all('bbn_i18n_exp', ['expression', 'lang', 'id_exp'],[
              ['id_exp', '=', $exp['id_exp']],
              ['lang', '=', $p]
            ])){
              foreach ( $other_langs as $o ){
                $exp[$lang] = $o['expression'];
              }
              $exp_in_db[] = $exp;
            }
            }
          }
        }
      };
    }
  }  
  return [
    'newStringUpdated' => $new_strings_updated,
    'pageTitle' => 'Strings to translate in '.$lang,
    'strings_in_db' => $exp_in_db,
    'source_lang' => $lang,
    'this_path' => $model->inc->options->text($model->data['id_option']),
    'configured_langs' => $project->get_langs()
  ];
}}