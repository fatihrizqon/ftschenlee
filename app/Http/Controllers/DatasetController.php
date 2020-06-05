<?php

namespace App\Http\Controllers;

use Auth;
use Excel;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index(){
        $data['dataset']= Dataset::all();
        return view('datasets',$data);
    }

    public function reset(){
      Dataset::truncate();
      return redirect(route('dataset'))->with('danger', 'Reset Data success!');
    }

    // Import Datasheet from Excel
    public function import(Request $request){
      if ($request->excel->getClientOriginalExtension() !='xlsx') {
          return redirect(route('home'))->with('warning', 'Wrong file format! Your file must be .xlsx!');
      }
      Excel::load($request->file('excel'), function ($reader) {
          $reader->each(function ($sheet) {
            if (empty($sheet->tanggal) || empty($sheet->data)) {
            }else {
              $data = Dataset::updateOrCreate(['tanggal' => $sheet->tanggal], ['data' => $sheet->data]);
            }
          });
      });
      return redirect(route('dataset'))->with('success', 'Import Data success!');
    }

    // Fuzzification
    public function fuzzification($dataset, $interval, $orde){
      $i=0;
      foreach ($dataset as $key) {
        $y=0;
        if ($i>=$orde) {
            foreach ($interval as $key2) {
              if ( $key->data >= $key2['bawah'] && $key->data < $key2['atas']) {
                $fuzzyfikasi = $y;
              }
            $y++;
            }
          for ($n=0; $n < $orde; $n++) {
            $temp_orde['orde'.$n] = $data[$i-$orde+$n]['fuzzification'];
          }
          $object = (object)($temp_orde);
          $data[$i]=['tanggal'=>$key->tanggal, 'data'=>$key->data, 'fuzzification'=>$fuzzyfikasi,'orde'=>$object];
    
        }else{
          foreach ($interval as $key2) {
            if ( $key->data >= $key2['bawah'] && $key->data < $key2['atas']) {
              $fuzzyfikasi = $y;
            }
            $y++;
          }
        $data[$i]=['tanggal'=>$key->tanggal, 'data'=>$key->data, 'fuzzification'=>$fuzzyfikasi,'orde'=>null];
        }
        $i++;
      }
      return $data;
    }

    // Model Chen
    public function model_chen($flr,$flrg){
      $i=0;
      $result=[];
      foreach ($flrg as $key) {
        $temp=explode(",",$key);
        $z=0;
        foreach($temp as $link)
          {
            if($link == '')
            {
                unset($temp[$z]);
            }
            $z++;
          }
        $object = (object)($temp);
        $n=0;
        $hasil=[];
        foreach ($flr as $key2) {
    
          if (!is_null($key2['orde'])) {
            $temp_orde='';
          foreach ($key2['orde'] as $key3) {
            $temp_orde.=$key3.',';
            }
            if ($key==$temp_orde) {
              $result[$n]=$key2['fuzzification'];
              $n++;
            }
          }
        }
    
        $result=array_unique($result);
        asort($result);
        $temp_result = $result;
        $result=[];
        $x=0;
        foreach ($temp_result as $key) {
          $result[$x]=$key;
          $x++;
        }
        $final_result = (object)($result);
        $chen[$i] = ['relation'=>$object,'result'=>$final_result];
        $i++;
      }
      return $chen;
    }

    // Model Lee
    public function model_lee($flr,$flrg){
      $i=0;
      $result=[];
      foreach ($flrg as $key) {
        $temp=explode(",",$key);
        $z=0;
        foreach($temp as $link)
          {
            if($link == '')
            {
                unset($temp[$z]);
            }
            $z++;
          }
        $object = (object)($temp);
        $n=0;
        $result=[];
        foreach ($flr as $key2) {
    
          if (!is_null($key2['orde'])) {
            $temp_orde='';
          foreach ($key2['orde'] as $key3) {
            $temp_orde.=$key3.',';
            }
            if ($key==$temp_orde) {
              $result[$n]=$key2['fuzzification'];
              $n++;
            }
          }
        }
    
        $result=array_count_values($result);
    
        $final_result = (object)($result);
        $lee[$i] = ['relation'=>$object,'result'=>$final_result];
        $i++;
      }
      return $lee;
    }

    // FLRG Chen
    public function flrg_chen($flr){
      $i=0;
      foreach ($flr as $key) {
        $temp='';
        if (!is_null($key['orde'])) {
          foreach ($key['orde'] as $key2) {
            $temp.=$key2.',';
          }
          $flrg[$i]=$temp;
          $i++;
        }
    
      }
      $flrg=array_unique($flrg);
      asort($flrg);
      $temp = $flrg;
      $flrg = [];
      $i=0;
      foreach ($temp as $key) {
        $flrg[$i]=$key;
        $i++;
      }
      $hasil=$this->model_chen($flr,$flrg);
      return $hasil;
    }

    // FLRG Lee
    public function flrg_lee($flr){
      $i=0;
      foreach ($flr as $key) {
        $temp='';
        if (!is_null($key['orde'])) {
          foreach ($key['orde'] as $key2) {
            $temp.=$key2.',';
          }
          $flrg[$i]=$temp;
          $i++;
        }
      }
      $flrg=array_unique($flrg);
      asort($flrg);
      $temp = $flrg;
      $flrg = [];
      $i=0;
      foreach ($temp as $key) {
        $flrg[$i]=$key;
        $i++;
      }
      $result=$this->model_lee($flr,$flrg);
      return $result;
    }

    // Chen Defuzzification
    public function defuzzification_chen($flrg, $interval){
      $n=0;
      foreach ($flrg as $key) {
        $relation = $key['relation'];
        $i=0;
        $temp=0;
        foreach ($key['result'] as $key2) {
          $temp+=$interval[$key2]['median'];
          $i++;
        }
        $prediction=$temp/$i;
        $defuzzification[$n]=['relation'=>$key['relation'],'result'=>$key['result'],'prediction'=>$prediction];
        $n++;
      }
      return $defuzzification;
    }

    // Lee Defuzzification
    public function defuzzification_lee($flrg, $interval){
      $n=0;
      foreach ($flrg as $key) {
        $relation = $key['relation'];
        $i=0;
        $temp=0;
        foreach ($key['result'] as $key2 => $value) {
          for ($x=0; $x < $value; $x++) {
            $temp+=$interval[$key2]['median'];
            $i++;
          }
        }
        $prediction=$temp/$i;
        $defuzzification[$n]=['relation'=>$key['relation'],'result'=>$key['result'],'prediction'=>$prediction];
        $n++;
      }
      return $defuzzification;
    }

    // Relation
    public function relation($flr){
      $i=0;
      foreach ($flr as $key) {
        $temp="";
        if (!is_null($key['orde'])) {
          foreach ($key['orde'] as $key2) {
            $temp.=$key2.",";
          }
        }else {
          $temp=null;
        }
    
        $relation[$i]=['tanggal'=>$key['tanggal'], 'data'=>$key['data'], 'fuzzification'=>$key['fuzzification'],'orde'=>$key['orde'], 'relation'=>$temp];
        $i++;
      }
      return $relation;
    }

    // Defuzzification Relation
    public function defuzzification_relation($defuzzification){
      $i=0;
      foreach ($defuzzification as $key) {
        $temp="";
        foreach ($key['relation'] as $key2) {
          $temp.=$key2.",";
        }
        $relation[$i]=['relation'=>$temp, 'prediction'=>$key['prediction']];
        $i++;
      }
      return $relation;
    }

    // Predicting
    public function prediction($flr, $defuzzification){
      $relation=$this->relation($flr);
      $defuzzification_relation=$this->defuzzification_relation($defuzzification);
      $i=0;

      foreach ($relation as $key) {
        if (!is_null($key['relation'])) {
          foreach ($defuzzification_relation as $key2) {
            if ($key['relation']==$key2['relation']) {
              $prediction[$i]=['tanggal'=>$key['tanggal'], 'data'=>$key['data'], 'fuzzification'=>$key['fuzzification'],'orde'=>$key['orde'], 'relation'=>$key['relation'],'prediction'=>$key2['prediction']];
            }
          }
        }else {
          $prediction[$i]=['tanggal'=>$key['tanggal'], 'data'=>$key['data'], 'fuzzification'=>$key['fuzzification'],'orde'=>$key['orde'], 'relation'=>$key['relation'],'prediction'=>null];
        }
        $i++;
      }
      return $prediction;
    }

    // Calculating Error
    public function error_calculation($prediction){
      $i=0;
      foreach ($prediction as $key) {
        if (!is_null($key['prediction'])) {
          $absolute = abs($key['data']-$key['prediction']);
          $afer = $absolute/$key['data'];
          $mse = pow($absolute,2);
        }else{
          $absolute = null;
          $afer = null;
          $mse =null;
        }
        $error[$i]=['absolute'=>$absolute,'afer'=>$afer,'mse'=>$mse,'tanggal'=>$key['tanggal'], 'data'=>$key['data'], 'fuzzification'=>$key['fuzzification'],'orde'=>$key['orde'], 'relation'=>$key['relation'],'prediction'=>$key['prediction']];
        $i++;
      }
      return $error;
    }    

    // Calculating Error Mean
    public function mean_error($error){
      $afer=0;
      $mse=0;
      $i=0;
      foreach ($error as $key) {
        if (!is_null($key['absolute'])) {
          $afer += $key['afer'];
          $mse += $key['mse'];
          $i++;
        }
      }

      $mean_afer=$afer/$i;
      $mean_mse=$mse/$i;
      $rmse = sqrt($mean_mse);
      $error = ['afer'=>$mean_afer,'mse'=> $mean_mse,'rmse'=>$rmse];
      return $error;
    }

    // Get Best Result
    public function best_result($chen, $lee)
    {
      if ($chen['afer']<$lee['afer']) {
        $model = "Chen";
        $accuration = 100-(round($chen['afer']*100,2));
        $result =['model'=>$model,'accuration'=>$accuration];
      }elseif ($chen['afer']>$lee['afer']) {
        $model = "Lee";
        $accuration = 100-(round($lee['afer']*100,2));
        $result =['model'=>$model,'accuration'=>$accuration];
      }else{
        $accuration = 100-(round($lee['afer']*100,2));
        $result =['model'=>'sama', 'accuration'=>$accuration];
      }
    
      return $result;
    }

    public function hitung_get(){
      return redirect(route('home'));
    }

    public function hitung(Request $request){
      $orde = $request->orde;
      $data_count= Dataset::all()->count();
      $dataset=Dataset::all();
      if ($data_count==0) {
        return redirect(route('dataset'))->with('warning', 'Dataset does not exists!');
      }
      $data['dataset'] = $dataset;
      $k=(int)round(1+3.3*log10($data_count));
      $data['k']=$k;
      $max=Dataset::max('data'); #find max data value
      $min=Dataset::min('data'); #find min data value
      $interval=round(($max-$min)/$k,2); #get interval value
      $data['range']=$interval;
      $i=0;
      $temp=$min;
      for ($i=0;$i<$k;$i++) {
        $bottom=$temp;
        $temp+=$interval;
        $data['interval'][$i] = ['bawah'=>$bottom,'atas'=>$temp,'median'=>($bottom+$temp)/2];
      }

      $data['flr']                  = $this->fuzzification($dataset, $data['interval'], $orde);
      $data['orde']                 = $orde;
      $data['flrg_lee']             = $this->flrg_lee($data['flr']);
      $data['flrg_chen']            = $this->flrg_chen($data['flr']);
      $data['defuzzification_chen'] = $this->defuzzification_chen($data['flrg_chen'], $data['interval']);
      $data['defuzzification_lee']  = $this->defuzzification_lee($data['flrg_lee'], $data['interval']);
      $data['chen_prediction']      = $this->prediction($data['flr'], $data['defuzzification_chen']);
      $data['lee_prediction']       = $this->prediction($data['flr'], $data['defuzzification_lee']);
      $data['error_chen']           = $this->error_calculation($data['chen_prediction']);
      $data['error_lee']            = $this->error_calculation($data['lee_prediction']);
      $data['mean_chen']            = $this->mean_error($data['error_chen']);
      $data['mean_lee']             = $this->mean_error($data['error_lee']);
      $data['hasil']                = $this->best_result($data['mean_chen'], $data['mean_lee']);

      return view('results', $data);
    }
}
