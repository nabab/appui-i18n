<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 *
 *  @var $ctrl \bbn\Mvc\Controller
 */

if (!empty($ctrl->arguments[0])
  && !empty($ctrl->arguments[1])
) {
  $ctrl->addData([
    'project' => $ctrl->arguments[0],
    'option' => $ctrl->arguments[1],
  ])->combo('$pageTitle', true);
}
