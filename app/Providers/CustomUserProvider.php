<?php
namespace App\Providers;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class CustomUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return Administrator::where('id', $identifier)->first();
    }

    public function retrieveByToken($identifier, $token)
    {

        return Administrator::where(['id'=> $identifier,'remember_token'=>$token])->first();
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if ( empty($credentials)) {
            return null;
        }

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $where[$key]=$value;
            }
        }
        return Administrator::where($where)->first();

        // 用$credentials里面的用户名密码去获取用户信息，然后返回Illuminate\Contracts\Auth\Authenticatable对象
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return Hash::check($plain, $user->getAuthPassword());
        // 用$credentials里面的用户名密码校验用户，返回true或false
    }
}