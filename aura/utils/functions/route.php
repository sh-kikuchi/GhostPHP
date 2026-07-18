<?php
/**
 * Generates a relative URL based on the current script's directory and a target route.
 *
 * @param string $target_route The target route or path to append (e.g., 'user/edit').
 *
 * @return string The constructed relative URL (e.g., '/app/user/edit').
 *
 * @example
 * echo route('posts/show'); // Outputs something like '/myapp/posts/show'
 */
function route($target_route){
  $url = dirname($_SERVER['SCRIPT_NAME']) . '/' . $target_route;
  return $url;
}