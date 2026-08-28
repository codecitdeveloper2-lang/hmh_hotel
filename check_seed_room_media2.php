<?php
$lines = file('seed_coral_dubai_deira.php');
echo implode('', array_slice($lines, 100, 60));
