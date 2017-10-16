<?php

$payload = json_decode($_POST['payload']);

$choosenLeaveType = $payload->actions[0]->selected_options[0]->value;
var_dump($choosenLeaveType);