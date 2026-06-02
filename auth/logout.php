<?php
session_start();
session_destroy();
header('Location: /php-ecommerce-project/index.php');
exit;
