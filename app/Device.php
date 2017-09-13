<?php

namespace App;

use App\BaseModel;

class Device extends BaseModel
{
  public function data()
  {
    return $this->hasMany('App\Data', 'deviceId', 'id');
  }
}
