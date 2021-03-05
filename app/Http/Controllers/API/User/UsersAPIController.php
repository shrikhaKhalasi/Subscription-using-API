<?php

namespace App\Http\Controllers\API\User;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Users Controller
|--------------------------------------------------------------------------
|
| This controller handles the Roles of
register,
index,
show,
store,
update,
destroy,
export Methods.
|
*/

class UsersAPIController extends Controller
{

    public function register(UsersRequest $request)
    {
        return User::Register($request);
    }

}
