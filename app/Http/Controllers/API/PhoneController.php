<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\PhoneResource;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\PostResource;
use App\Models\Phone;
use Illuminate\Support\Facades\Auth;
use Validator;

class PhoneController extends BaseController
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

        $query = Phone::limit(10);
        if($request->filled('sort')) {
            $query = $query->orderBy('phone', $request->get('sort'));
        }


        if ($request->filled('filter')) {
            $phone = $request->get('filter');
            $query->where('phone', 'like', "%$phone%");
        }

        $collection = $query->get();

        return $this->sendResponse(PhoneResource::collection($collection), 'Phones retrieved successfully.');
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
            'phone' => 'required',
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $phone = Phone::create($input);

        return $this->sendResponse(new PostResource($phone), 'Phone created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $phone = Phone::where('id', $id)->first();

        if (is_null($phone)) {
            return $this->sendError('Phone not found.');
        }

        return $this->sendResponse(new PhoneResource($phone), 'Phone retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Phone $phone)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'body' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $phone->phone = $input['phone'];
        $phone->save();

        return $this->sendResponse(new PhoneResource($phone), 'Phone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phone $phone)
    {
        $phone->delete();

        return $this->sendResponse([], 'Phone deleted successfully.');
    }
}
