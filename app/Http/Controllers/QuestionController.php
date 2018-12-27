<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Category;
 
 
 
class QuestionController extends Controller
{
  
    
    public function create(Request $request){
 
        $validator = Validator::make($request->all(), [
            'question'=>'required',
            'user_id'=>'required'
        ]);
 
        if($validator->fails()){
            return array(
                'error' => true,
                'message' => $validator->errors()->all()
            );
        }
        a
        $question = new Question;
        $question->question = $request->input('question');
        $question->user_id= $request->input('user_id');
        $question->category_id= $request->input('category_id');
        $question->save();
        return array('error'=>false, 'question'=>$question);
    }
 
    public function getAll(){
        $questions = Question::all();
 
        foreach($questions as $question){
            $question['answercount'] = count($question->answers);
            unset($question->answers);
        }
 
 
        return array('error'=>false, 'questions'=>$questions);
    }
 
    public function getByCategory($category_id){
        try{
            $questions = Category::findOrFail($category_id)->questions; 
 
            foreach($questions as $question){
                $question['answercount'] = count($question->answers);
                unset($question->answers);
            }
 
            return array('error'=>false, 'questions'=>$questions);
        }catch(ModelNotFoundException $e){
            return array('error'=>true, 'message'=>'Invalid Category ID');
        }
    }
  
}