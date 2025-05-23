<?php
if ($ctrl->hasArguments()) {
  $ctrl->addData([
    'project' => $ctrl->arguments[0],
    'path' => $ctrl->arguments[1]
  ])
  ->setIcon('nf nf-md-translate')
  ->combo(_('Translate'), true);
}
