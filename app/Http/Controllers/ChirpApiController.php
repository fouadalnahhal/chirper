<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chirp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ChirpApiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['validateToken']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return csrf_token();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors(),
                ]
            )->setStatusCode(400);
        }

        $chirp = new Chirp;
        $chirp->message = $request->get('message');
        $chirp->user_id = $user->id;
        $chirp->save();

        return response()->json(['result' => 'Added Successfully', 'validator' => $request->all()])->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:255',
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors(),
                ]
            )->setStatusCode(400);
        }

        $chirp = Chirp::find($request->get('id'));
        if (is_null($chirp)) {
            return response()->json(['result' => "Chirp doesn't exsist.",])->setStatusCode(404);
        }
        if ($chirp->user_id != $user->id) {
            return response()->json(['result' => "Unauthorized.",])->setStatusCode(403);
        }
        $chirp->message = $request->get('message');
        $chirp->save();

        return response()->json(['result' => 'Updated successfully.',])->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors(),
                ]
            )->setStatusCode(400);
        }

        $chirp = Chirp::find($request->get('id'));
        if (is_null($chirp)) {
            return response()->json(['result' => "Chirp doesn't exsist.",])->setStatusCode(404);
        }
        if ($chirp->user_id != $user->id) {
            return response()->json(['result' => "Unauthorized.",])->setStatusCode(403);
        }
        $chirp->delete();

        return response()->json(['result' => 'Deleted successfully.',])->setStatusCode(200);
    }
}
