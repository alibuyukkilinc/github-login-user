<?php

include 'Github';

$Github = new Github();
$login_url = $Github->LOGIN_URL;// users github login url

?>

<!-- Example -->
<a href="<?php echo $login_url; ?>">Login With Github</a>