<?php

namespace App;

use App\BaseModel;

class Data extends BaseModel
{
  protected $hidden = ['file', 'detail'];

	/*
	fft 快速傅里叶变换
	输入
		pr: 波形数据
		n: 数据个数
	输出
		pr: 频域数据，下标为频率
		注意，pr中原始数据会被破坏
	例如
		float data[DataCount];	//波形数据
		float pr[DataCount];

		memcpy(pr, data, DataCount * sizeof(float));
		fft(pr, DataCount);
	*/

  public static function fft(&$pr, &$pi = null, &$fr = null, &$fi = null) {
    $n = count($pr);
    if(!$pi) {
      $pi = array_fill(0, $n, 0);
      $fr = array_fill(0, $n, 0);
      $fi = array_fill(0, $n, 0);
    }
    $k = floor(log($n) / log(2));
    for($it = 0; $it < $n; $it++) {
      $m = $it; $iss = 0;
      for($i = 0; $i < $k; $i++) {
        $j = floor($m / 2); $iss = 2 * $iss + ($m - 2 * $j);
        $m = $j;
      }
      $fr[$it] = $pr[$iss];
      $fi[$it] = $pi[$iss];
    }
    $pr[0] = 1; $pi[0] = 0;
    $p = M_PI * 2 / $n;
    $pr[1] = cos($p); $pi[1] = -sin($p);
    //if($inverse) $pi[1] = -$pi[1];
    for($i = 2; $i < $n; $i++) {
      $p = $pr[$i - 1] * $pr[1]; $q = $pi[$i - 1] * $pi[1];
      $s = ($pr[$i - 1] + $pi[$i - 1]) * ($pr[1] + $pi[1]);
      $pr[$i] = $p - $q; $pi[$i]= $s - $p - $q; 
    }
    for($it = 0; $it < $n - 1; $it += 2) {
      $vr = $fr[$it]; $vi = $fi[$it];
      $fr[$it] = $vr + $fr[$it + 1]; $fi[$it] = $vi + $fi[$it + 1];
      $fr[$it + 1] = $vr - $fr[$it + 1]; $fi[$it + 1] = $vi - $fi[$it + 1];
    }
    $m = floor($n / 2); $nv = 2;
    for($l0 = $k - 2; $l0 >= 0; $l0--) {
      $m = floor($m / 2); $v = $nv; $nv *= 2;
      for($it = 0; $it <= ($m - 1) * $nv; $it += $nv) {
        for($j = 0; $j < $v - 1; $j++) {
          $p = $pr[$m * $j] * $fr[$it + $j + $v];
          $q = $pi[$m * $j] * $fi[$it + $j + $v];
          $s = $pr[$m * $j] + $pi[$m * $j];
          $s = $s * ($fr[$it + $j + $v] + $fi[$it + $j + $v]);
          $poddr = $p - $q; $poddi = $s - $p - $q;
          $fr[$it + $j + $v] = $fr[$it + $j] - $poddr;
          $fi[$it + $j + $v] = $fi[$it + $j] - $poddi;
          $fr[$it + $j] = $fr[$it + $j] + $poddr;
          $fi[$it + $j] = $fi[$it + $j] + $poddi;
        }
      }
    }
    /*if(inverse)
      for($i = 0; $i < $n; $i++) {
        $fr[$i] = $fr[$i] / $n;
        $fi[$i] = $fi[$i] / $n;
      }
    */
    for($i = 0; $i < $n; $i++) {
      $pr[$i] = sqrt($fr[$i] * $fr[$i] + $fi[$i] * $fi[$i]);
      if(abs($fr[$i]) < 0.000001 * abs($fi[$i]) || abs($fr[$i]) < 0.000001)
        if($fi[$i] * $fr[$i] > 0) $pi[$i] = 90;
        else $pi[$i] = - 90;
      else
        $pi[$i] = atan($fi[$i] / $fr[$i]) * 180 / M_PI;
      $pr[$i] = $pr[$i] * 2 / $n;
    }
  }
  
  public function save(array $options = []) {
    $r = ['data' => [], 'info' => ['frequency' => 200]];
    $fl = $this->fileSize;
    $b = $this->file;
    $l = Ord($b[0]) | Ord($b[1]) << 8;
    $c = 0;
    $div = function(&$v) {
      $v /= 9.8;
    };
    $mul = function(&$v) {
      $v *= 9.8;
    };
    $fix = function(&$v) {
      $v = round($v * 1000) / 1000;
    };
    if($l < 0x1000) {
      $r['info']['frequency'] = Ord($b[2]);
      $r['info']['userName'] = substr($b, 4, Ord($b[3]));
      $r['info']['position'] = substr($b, 5 + Ord($b[3]), Ord($b[4 + Ord($b[3])]));
      $this->userName = $r['info']['userName'];
      $this->position = $r['info']['position'];
      $c = $l;
    }
    while($c < $fl) {
      $l = Ord($b[$c]) | Ord($b[$c + 1]) << 8;
      $s = gzuncompress(substr($b, $c + 2, $l));
      $c += $l + 2;
      $l = 0; $dl = strlen($s);
      while($l < $dl) {
        $d = ['acc' => [[], []]];
        $t = unpack('N*', substr($s, $l, 8));
        $d['time'] = $t[2] | $t[1] << 32;
        $d['speed'] = round(unpack('f', strrev(substr($s, $l + 8, 4)))[1], 3);
        $d['pwxy'] = round(unpack('f', strrev(substr($s, $l + 12, 4)))[1], 3);
        $d['pwxz'] = round(unpack('f', strrev(substr($s, $l + 16, 4)))[1], 3);
        $ay = 0; $az = 0; $sy = 0; $sz = 0;
        for($i = $l + 20; $i < $l + 2048 * 4 + 20; $i += 8) {
          $t = round(unpack('f', strrev(substr($s, $i, 4)))[1], 3);
          $d['acc'][0][] = $t;
          $ay += $t;
          $t = round(unpack('f', strrev(substr($s, $i + 4, 4)))[1], 3);
          $d['acc'][1][] = $t;
          $az += $t;
        }
        $d['freq'][0] = $d['acc'][0];
        $d['freq'][1] = $d['acc'][1];
        array_walk($d['freq'][0], $div);
        array_walk($d['freq'][1], $div);
        static::fft($d['freq'][0]);
        static::fft($d['freq'][1]);
        array_splice($d['freq'][0], 512);
        array_splice($d['freq'][1], 512);
        array_walk($d['freq'][0], $mul);
        array_walk($d['freq'][1], $mul);
        array_walk($d['freq'][0], $fix);
        array_walk($d['freq'][1], $fix);
        $ay /= 1024;
        $az /= 1024;
        for($i = 0; $i < 1024; $i++) {
          $t = $d['acc'][0][$i] - $ay;
          $sy += $t * $t;
          $t = $d['acc'][1][$i] - $az;
          $sz += $t * $t;
        }
        $d['accy'] = sqrt($sy / 1024);
        $d['accz'] = sqrt($sz / 1024);
        $r['data'][] = $d;
        $l += 2048 * 4 + 20;
      }
    }
    $this->detail = gzcompress(json_encode($r));
    parent::save($options);
  }

  public function detail() {
    return gzuncompress($this->detail);
  }
}
