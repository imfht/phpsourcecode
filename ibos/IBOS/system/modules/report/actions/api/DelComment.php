<?php
/**
 * 删除评论或者回复接口
 */

namespace application\modules\report\actions\api;

use application\modules\message\core\Comment;

class DelComment extends \CAction
{

    public function run()
    {
        $comment = new Comment();
        $comment->delComment();
    }
}