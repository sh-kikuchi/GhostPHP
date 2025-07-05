<?php
/**
 * Anti-XSS measures: escaping process.
 * @param string  $str
 * @return string htmlspecialchars($str)
 */
function h($str) :string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
