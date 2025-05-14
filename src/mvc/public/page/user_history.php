<?php

$userName =$ctrl->inc->user->getManager()->getName($ctrl->inc->user->getId());
$ctrl->combo($userName.'\'s translations');
