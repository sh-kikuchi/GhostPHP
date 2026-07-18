<?php
/**
 * Paginates an array of data into chunks for display per page.
 *
 * @param array $data        The full array of data to paginate.
 * @param int   $showPerPage Number of items to show per page.
 *
 * @return array{
 *     data: array,           // The sliced data for the current page.
 *     max_page: int          // Total number of pages.
 * }
 *
 * @example
 * // Usage example:
 * $result = paginate($users, 10);
 * foreach ($result['data'] as $user) { ... }
 * echo "Page 1 of " . $result['max_page'];
 */
function paginate(array $data, int $showPerPage){
    $return_data = [];

    define('MAX', $showPerPage);                           // show data per page  
    $data_count = count($data);                            // Total data
    $max_page = ceil($data_count / MAX);                   // Total templates
    $now = !isset($_GET['page_id'])? 1: $_GET['page_id'];  // What number?
    $start_no = ($now - 1) * MAX;                          // What number of the array should I get it from?
    $disp_data = array_slice($data, $start_no, MAX, true); // array_slice
    
    $return_data['data']     = $disp_data;
    $return_data['max_page'] = $max_page;

    return $return_data;
}