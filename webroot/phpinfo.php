<?php
echo '<h3>PHP設定確認（Apache経由）</h3>';
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . '<br>';
echo 'post_max_size: ' . ini_get('post_max_size') . '<br>';
echo 'memory_limit: ' . ini_get('memory_limit') . '<br>';
echo 'max_execution_time: ' . ini_get('max_execution_time') . '<br>';
echo 'loaded php.ini: ' . php_ini_loaded_file() . '<br>';