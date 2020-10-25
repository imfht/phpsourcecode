<?php

/*
 * hook_page_content_bottom
 */

function back_to_top_page_content_bottom($data) {
    $data .= View::make("back_to_top::index")->render();
    return $data;
}
