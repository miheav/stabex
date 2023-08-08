<?php

namespace App\Http\Controllers\API;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\PositionResource;

class PositionController extends BaseController
{

    public function __construct()
    {
        $this->middleware('admin.role.checker')->except('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Position::all();
        if($request->filled('sort')) {
            $query = $query->orderBy('title', $request->get('sort'));
        }


        if ($request->filled('filter')) {
            $body = $request->get('filter');
            $query->where('title', 'like', "%$body%");
        }

        $collection = $query->get();

        return $this->sendResponse(PositionResource::collection($collection), 'Post retrieved successfully.');
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
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $post = Position::create($input);



        return $this->sendResponse(new PositionResource($post), 'Position created successfully.');
    }

    public function update(Request $request, Position $position)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $position->title = $request->title;
        $position->save();

        return $this->sendResponse(new PositionResource($position), 'Position updated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $position = Position::where('id', $id)->first();

        if (is_null($position)) {
            return $this->sendError('Position not found.');
        }

        return $this->sendResponse(new PositionResource($position), 'Position retrieved successfully.');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return $this->sendResponse([], 'Position deleted successfully.');
    }

}
