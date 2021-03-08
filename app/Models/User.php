<?php

namespace App\Models;

use App\Http\Resources\DataResource;
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
     *  Common manual pagination method
     *
     * @param $items - Object collection
     * @param $perPage - Per page
     * @param int $page - Page
     * @param array $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function paginateCollection($items, $perPage, $page = 1, $options = [])
    {
        $perPage = is_null($perPage) ? 10 : $perPage;
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);
        return new \Illuminate\Pagination\LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
        //ref for array_values() fix: https://stackoverflow.com/a/38712699/3553367
    }
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
    /**
     * custom common code for pagination and search and sort on associative each array key.
     *
     * @param $request
     * @param $filteredArray - data array
     * @param array $csvArray - if export csv pass csv data array and in request pass is_export = '1'
     * @return DataResource|\Illuminate\Pagination\LengthAwarePaginator
     */
    public static function customPaginationWithSearchAndSort($request, $filteredArray, $csvArray = [])
    {
        $filteredArray = collect($filteredArray);

        if ($request->filled('search')) {
            $search = $request->get('search');

            $filteredArray = $filteredArray->filter(function ($values) use ($search) {
                foreach ($values as $key => $value) {
                    if (!is_array($value)) {
                        if (stripos($value, $search) !== false) {
                            return true;
                        }
                    }
                }
            });
        }

        if ($request->filled('sort')) {
            if ($request->filled('order_by') && ($request->get('order_by') == 'desc' || $request->get('order_by') == 'DESC'))
                $filteredArray = $filteredArray->sortByDesc($request->get('sort'));
            else
                $filteredArray = $filteredArray->sortBy($request->get('sort'));
        }

        $filteredArray = $filteredArray->values()->all();
        if ($request->filled('is_light')) {
            return new DataResource($filteredArray);
        } else {
            return self::paginateCollection($filteredArray, $request->get('per_page'), $request->get('page'), ['path' => $request->url()]);// get pagination
        }
    }
}
