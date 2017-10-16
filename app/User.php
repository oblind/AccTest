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
    'password', 'iconFile',
  ];

  public function detail()
  {
    if($this->groupId == 255)
      $r = User::with('device', 'device.data')->get()->all();
    else
      $r = User::where('id', $this->id)->with('device', 'device.data')->get()->all();
    array_walk($r, function(&$v) {
      $v['info'] = json_decode($v['info']);
    });
    return $r;
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
      $u->icon = 'img/user16.png';
      $u->info = json_encode([['prefix' => '', 'startTrain' => 1, 'endTrain' => 10, 'count' => 6, 'position' => ['一位端左侧', '一位端右侧', '二位端左侧', '二位端右侧']]]);
      $u->save();
      return $u;
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
    if($u && Hash::check($args['password'], $u->password))
      return $u;
    else
      $err = ['error' => [trans('auth.incorrect')]];
    }
  }

  public function saveIcon($f, &$err) { //设置头像
    $fn = strtolower($f->getClientOriginalName());
    $f = (string)$f;
    if(preg_match('/\.jpg$/', $fn) || preg_match('/\.jpeg$/', $fn))
      $src = imagecreatefromjpeg($f);
    elseif(preg_match('/\.png$/', $fn))
      $src = imagecreatefrompng($f);
    else {
      $err = trans('error.imageFormatError');
      return;
    }
    list($w0, $h0) = getimagesize($f);
    $w = $w0;
    $h = $h0;
    if($w0 > $h0) {
      if($w > 128) {
        $h *= 128 / $w;
        $w = 128;
      }
    } elseif($h > 128) {
      $w *= 128 / $h;
      $h = 128;
    }
    $img = imagecreatetruecolor($w, $h);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    imagecopyresampled($img, $src, 0, 0, 0, 0, $w, $h, $w0, $h0);
    $f = 'img/user/' . $this->id . '.png';
    imagepng($img, $f);
    imagedestroy($img);
    $this->icon = $f;
    $this->save();
    return true;
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
