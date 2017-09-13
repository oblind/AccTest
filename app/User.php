<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
  use Authenticatable, Authorizable;

  /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
  protected $fillable = [
    'name', 'email',
  ];

  /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
  protected $hidden = [
    'password',
  ];

  public function detail()
  {
    return static::where('id', $this->id)->with('group', 'device', 'device.data')->first();
  }

  public static function register($args, &$err)
  {
    $v = Validator::make($args, [
      'name' => 'required',
      'email' => 'email|unique:users|max:32',
      'password' => 'required|min:6|confirmed',
      'password_confirmation' => 'required|min:6'
    ]);
    if($v->fails())
      $err = $v->messages();
    else {
      $u = new User;
      $u->name = $args['name'];
      $u->email = $args['email'];
      $u->password = Hash::make($args['password']);
      $u->groupId = 1;
      $u->save();
      return $u->detail();
    }        
  }

  public static function login($args, &$err)
  {
    $v = Validator::make($args, [
      'email' => 'required|email',
      'password' => 'required'
    ]);
    if($v->fails())
      $err = $v->messages();
    else {
    $u = static::where(['email' => $args['email']])->first();
    if(Hash::check($args['password'], $u->password))
      return $u->detail();
    else
      $err = ['error' => [trans('auth.incorrect')]];
    }
  }

  public function group()
  {
    return $this->belongsTo('App\Group', 'groupId', 'id');
  }

  public function device()
  {
    return $this->hasMany('App\Device', 'userId', 'id');
  }
}
