<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class User extends Model 
{
 public function questions(){
 return $this->hasMany('App\Question');
 }   
}