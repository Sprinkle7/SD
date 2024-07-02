<?php


namespace App\Helper\SystemMessage\Models\Comment;


use App\Helper\SystemMessage\SystemMessage;

class CommentSystemMessage extends SystemMessage
{
    public function addReply()
    {
        return $this->messages['addReplay'];
    }
}
