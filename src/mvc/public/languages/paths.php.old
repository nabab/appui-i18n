<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 23/11/17
 * Time: 16.52
 *
 * @var $ctrl \bbn\mvc\controller
 */

/* when I click on the button cfg of the paths table in homepage, before to open the popup I make a post here to receive the path of the row.
*/

if($ctrl->post['id']){
  die(var_dump('vidja'));
}

if(!empty( $ctrl->post['path']) ){
  $ctrl->obj->data['path'] = $ctrl->post['path'];


  $langs = $ctrl->get_model('internationalization/languages/home')['langs'];
  $primary = $ctrl->get_model('internationalization/languages/home')['primary'];
  if( !empty($langs) && !empty($primary) ){
    $merged_data = array_merge($langs,$primary);
    $ctrl->obj->data = [
      $ctrl->post['path'] =>  $merged_data
    ];
    //I created a file test.js in APST_DATA,temporary I store in this file the cfg of each root
    file_put_contents(BBN_DATA_PATH.'test.json', json_encode($ctrl->obj->data));
  }
  //temporary I use the json file to take cfg of each root
}

$array_json = json_decode(file_get_contents(BBN_DATA_PATH.'test.json'),true);

if ( !empty($ctrl->arguments[0]) ){
 // die(var_dump($array_json));
 // die(var_dump($ctrl->arguments[0]));
  if ( $array_json[$ctrl->arguments[0]] ){
    $ctrl->add_data([
      'path' => $ctrl->arguments[0],
      'langs_path' => [ $ctrl->arguments[0] => $array_json['langs_path']],
    ]);
    $ctrl->combo('$title', true);
  }
  else{
    $ctrl->add_script('bbn.fn.alert("Please configure languages for this root using the form \'Cfg Root\' in the last column of this row :(" );');
    $ctrl->combo('$title', true);
  }


}
