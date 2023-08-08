<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AutoResource;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Validator;

class PostController extends BaseController
{

    public function __construct()
    {
        $this->middleware('trust.post.checker')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::limit(10);

        if($request->filled('sort')) {
            $query = $posts->orderBy('title', $request->get('sort'));
        }


        if ($request->filled('filter')) {
            $body = $request->get('filter');
            $posts->where('body', 'like', "%$body%");
        }

        $collection = $query->get();
        return $this->sendResponse(PostResource::collection($collection), 'Post retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'body' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input['user_id'] = Auth::user()->id;


        $post = Post::create($input);



        return $this->sendResponse(new PostResource($post), 'Post created successfully.');
    }

    public function update(Request $request, Post $post)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'body' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $post->title = $input['title'];
        $post->body = $input['body'];
        $post->save();

        return $this->sendResponse(new PostResource($post), 'Post updated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::where('id', $id)->first();

        if (is_null($post)) {
            return $this->sendError('Post not found.');
        }

        return $this->sendResponse(new PostResource($post), 'Post retrieved successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return $this->sendResponse([], 'Post deleted successfully.');
    }

}
