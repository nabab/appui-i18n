<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 */

//transfer to $model
$source_langs = $ctrl->db->get_rows("
  SELECT DISTINCT (lang) FROM bbn_i18n
");


$ctrl->add_data([
  'id_project' => $ctrl->post['id_project'],
  'source_langs' => $source_langs
])
->combo('', true);