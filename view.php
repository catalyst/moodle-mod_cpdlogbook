<?php

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'cpdlogbook');

echo "Temp";