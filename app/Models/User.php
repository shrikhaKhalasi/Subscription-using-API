<?php

namespace App\Models;

use App\Http\Resources\UsersResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use  Notifiable, HasApiTokens,Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     *
     * @param $query
     * @param $request
     * @return \App\Http\Resources\UsersResource
     */
    public function scopeRegister($query, $request)
    {
        $data = $request->all();

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return new UsersResource($user);
    }
    /**
     * Common Display Error Message.
     *
     * @param $message - message for users.
     * @param $errorCode - pass error code which you want.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getError($message, $errorCode = '')
    {
        if ($errorCode == '')
            $errorCode = config(422);

        return response()->json(['error' => $message], $errorCode);
    }
}
