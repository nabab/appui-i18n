<?php
/*
* Describe what it does!
*
**/

/** @var $this \bbn\mvc\model*/


$model->data['success'] = false;
$id_user = $model->inc->user->get_id();
$is_admin =  $model->db->val_by_id("bbn_users", "admin", $id_user);
if (!empty($id_user) ){
  if ( !empty($is_admin) ){
    //CASE admin

    //$project_count is the number of bbn_project
    if ( $projects = $model->get_model('internationalization/languages')['projects'] ){
      $project_count = count($projects);

      //number of projects having 'en' as source language
      $source_en = $model->db->count('bbn_projects', ['lang' => 'en']);


      //number of projects having 'fr' as source language
      $source_fr = $model->db->count('bbn_projects', ['lang' => 'fr']);


      //$best_translators the three most productive translators
      $query = <<<MYSQL
    SELECT `id_user`,
      COUNT(`id_user`) AS `value_occurrence` 
    FROM     `bbn_i18n`
    GROUP BY `id_user`
    ORDER BY `value_occurrence` DESC
    LIMIT 3
MYSQL;
      $best_translators = $model->db->get_rows($query);
      //take the name of the translator from bbn_users
      foreach ( $best_translators as $i => $b ){
        $best_translators[$i]['name'] = $model->db->get_val('bbn_users', 'nom', 'id', $b['id_user']);
        unset($best_translators[$i]['id_user']);
      }
      $model->data['success'] = true;
    }
  }

  //CASE !admin

  //langs of translation present in db
  $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp");

  //the complete array of primaries languages
  $primaries = $model->get_model('internationalization/languages')['primary'];

  //die(var_dump($model->data['source_lang']));
  $dropdown_langs = array_filter($primaries, function($i) use($langs_in_db, $source_lang) {

    return in_array($i['code'], $langs_in_db);
  });

  //array containing all source languages
  $source_langs = $model->db->get_column_values('bbn_i18n','lang');

  //array used for source of the second dropdown in statistic tab
  $source_dd_langs = array_filter($primaries, function($i) use($source_langs){
    return in_array($i['code'], $source_langs);
  });



  //total number of strings present in all projects
  $total_source_strings = $model->db->count('bbn_i18n');

  //$total_translated_strings -> the total number of translated strings
  $total_translated_strings = $model->db->count('bbn_i18n_exp');
  //percentage of translated stings
  $percentage_translated_strings = round(($total_translated_strings - $total_source_strings) / $total_source_strings *
      100, 2).'%';

  $model->data['success'] = true;
}



return [
  statistics => [
    'Number of BBN\'s projects in translation:'  => $project_count,
    'Languages of translation:' => $langs_in_db,
    'Number of project(s) having English as source language:' => $source_en,
    'Number of project(s) having French as source language' => $source_fr,
    'Number of strings in all projects:' => $total_source_strings,
    'Strings translated at least in one language:' => $percentage_translated_strings,
    'Most productive translator:' => $best_translators,
    
  ],
  'projects' => $projects,
  'langs_in_db' => $langs_in_db,
  'success'=> $model->data['success'],
  'dropdown_langs' => $dropdown_langs,
  'source_dd_langs' => $source_dd_langs
];