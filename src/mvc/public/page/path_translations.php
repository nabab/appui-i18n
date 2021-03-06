<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 *
 *  @var $ctrl \bbn\Mvc\Controller
 */

if (!empty($ctrl->arguments[0])) {
  //$ctrl->obj->url = APPUI_I18N_ROOT.'page/path_translation';
  if(\bbn\Str::isUid($ctrl->arguments[0])) {
    $id_option = $ctrl->arguments[0];
  }
  else {
    // die(var_dump($ctrl->arguments[1], $ctrl->arguments[0]));
    $id_option  = $ctrl->arguments[1];
    $id_project = $ctrl->inc->options->fromCode($ctrl->arguments[0], 'list', 'project', 'appui');
  }

  /** add id_option to data and routes needed to instantiate the class ide */
  $ctrl->addData(
    [
    'id_project' => $id_project ?? false,
    'id_option' => $id_option,
    ]
  )->combo('$pageTitle', true);
}
