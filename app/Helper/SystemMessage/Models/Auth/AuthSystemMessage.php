<?php


namespace App\Helper\SystemMessage\Models\Auth;


use App\Helper\SystemMessage\SystemMessage;

class AuthSystemMessage extends SystemMessage
{
    public function signUp()
    {
        return $this->messages['signUp'];
    }

    public function signIn()
    {
        return $this->messages['signIn'];
    }

    public function forgetPassword()
    {
        return $this->messages['forgetPassword'];
    }

    public function forgetPasswordFound()
    {
        return $this->messages['forgetPassword_found'];
    }

    public function updatePassword() {
        return $this->messages['updatePassword'];
    }

    public function profileNotCompleted() {
        return $this->messages['profileNotCompleted'];
    }
}
