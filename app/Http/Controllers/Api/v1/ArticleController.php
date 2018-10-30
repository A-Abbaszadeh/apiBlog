<?php

namespace App\Http\Controllers\Api\v1;

use App\Article;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * Display a listing of the article.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = Article::orderBy('id','DESC')-> paginate(5);
//        $article = Article::all();
        return response()->json($article,200);
    }


    /**
     * Return rules for other methods
     *
     */

    public function rules($method)
    {
        switch($method) {
            case 'store':
                {
                    return [
                        'title' => 'required',
                        'body' => 'required',
                    ];
                }
            case 'update':
                {
                    return [
                        'title' => 'required',
                        'body' => 'required|max:10',
                    ];
                }
            case 'storeComment':
                {
                    return [
                        'body' => 'required',
                    ];
                }
            default:
                break;
        }
    }

    /**
     * Store a newly created article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        switch($user['type']) {
            //Admin
            case 1:
                $valid_data = $this->validate($request,$this->rules('store'));

                // Insert Validated data to database
                if (Article::create($valid_data)){
                    return response()->json(['message' => 'Article sent successfully'],200);
                } else {
                    return response()->json(['message' => 'Something is wrong!'],400);
                }

            //Normal user
            case 0:
                return response()->json(['message' => 'Access is denied'],400);
            default:
                break;
        }

    }

    /**
     * Display the specified article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        $response = [
            'article' => $article,
            'comments' => $article->comments()->orderBy('id','DESC')->paginate(2)
        ];
        return response()->json($response,200);
    }


    /**
     * Update the specified article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $user = auth()->user();

        switch($user['type']) {
            //Admin
            case 1:
                $valid_data = $this->validate($request,$this->rules('update'));

                // Update Validated data to database
                if ($article->update($valid_data)){
                    return response(['message' => 'Article updated successfully'],200);
                } else {
                    return response()->json(['message' => 'Something is wrong!'],400);
                }

            //Normal user
            case 0:
                return response()->json(['message' => 'Access is denied'],400);
            default:
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();

        switch($user['type']) {
            //Admin
            case 1:
                if (!is_null(Article::find($id))){
                    Article::find($id)->delete();

                    return response(['message' => 'Article deleted successfully'],200);
                }

                return response(['message' => 'Your request is wrong'],400);

            //Normal user
            case 0:
                return response()->json(['message' => 'Access is denied'],400);
            default:
                break;
        }
    }

    public function storeComment(Request $request, Article $article)
    {
        $valid_data = $this->validate($request,$this->rules('storeComment'));

        $article->comments()->create([
            'author' => auth()->user()['name'],
            'body' => $valid_data['body']
        ]);
        if ($article->save()){
            return response(['message' => 'Comment sent successfully'],200);
        } else {
            return response()->json(['message' => 'Something is wrong!'],400);
        }
    }
}
