<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Answer; 
    
class AnswerController extends Controller
{
 
    public function submit(Request $request){
 
        $validator = Validator::make($request->all(), [
            'answer'=>'required',
            'user_id'=>'required',
            'question_id'=>'required'
        ]);
 
        if($validator->fails()){
            return array(
                'error' => true,
                'message' => $validator->errors()->all()
            );
        }
 
        $answer = new Answer;
        $answer->answer = $request->input('answer');
        $answer->question_id= $request->input('question_id');
        $answer->user_id= $request->input('user_id');
        $answer->save();
        return array('error'=>false, 'answer'=>$answer);
    }
}