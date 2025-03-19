<?php
function escapeGET($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}


?>