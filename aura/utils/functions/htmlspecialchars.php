<?php
/**
 * Anti-XSS measures: escaping process.
 * @param ?string  $str
 * @return string htmlspecialchars($str)
 */
function h(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}