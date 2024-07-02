<?php


namespace App\Helper\SystemMessage\Models\Bookmark;


use App\Helper\SystemMessage\SystemMessage;

class BookmarkSystemMessage extends SystemMessage
{
    public function bookmarked() {
        return $this->messages['bookmarked'];
    }

    public function bookmarkRemoved() {
        return $this->messages['bookmarkRemoved'];
    }

}
