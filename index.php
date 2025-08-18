<?php
// Safety: make relative paths resolve as if we were inside /public
chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';
