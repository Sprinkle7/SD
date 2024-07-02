<?php

namespace App\Http\Controllers;

use App\Helper\Response\Response;
use Illuminate\Http\Request;

class test extends Controller
{
    public function test() {
        return Response::response200('test');
    }
}
