<?php

namespace App\Http\Controllers\Auth\APIs\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\HttpResonse;
use App\Http\Resources\v1\UserResource;
use App\Http\Resources\v1\UserCollection;
use App\Http\Requests\Users\v1\UserRequest;
use App\Http\Requests\Users\v1\UserUpdateRequest;
use App\Http\Requests\Auth\v1\AuthRequest;
use Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResonse;

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/api/v1/user",
     *   summary="Get all Users List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function index()
    {
        $users = new UserCollection(User::paginate());
        return $this->success('Users List', $users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){}

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/v1/user",
     *     tags={"Users"},
     *     summary="Creating new user",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="email")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="password")
     *     ),
     *     @OA\Response(response="200", description="User created successful"),
     *     @OA\Response(response="401", description="Failed to create user")
     * )
     */
    public function store(UserRequest $request)
    {
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            $data = $request->validated();
            if(!empty($data['password'])) $data['Password'] = Hash::make($data['password']);

            $user = new UserResource(User::create($data));
            return $this->objecteCreated('User Create', $user);

        } catch (\Throwable $th) {
            return $this->internalServer('Somthing worng', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     tags={"Users"},
     *     path="/api/v1/user/{user}",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     summary="Get logged-in user details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function show(User $user)
    {
        if(!$user){
            return $this->success('No User Details Found.');
        }

        $user = new UserResource($user);
        return $this->success('User Details', $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user){}

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/v1/user/{user}",
     *     tags={"Users"},
     *     summary="Update existing user",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User Id",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="User updated successful"),
     *     @OA\Response(response="401", description="Failed to update user")
     * )
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        if($user->email !== $request->email){
            $checkEmail = User::where('email', $request->email)->where('id', '!=', $user->id)->get()->count();
            if($checkEmail){
                return $this->validation('Email is already exists.');       
            }
        }

        $newData = [];
        if(!empty($data['password']) && isset($data['password'])){
            $newData['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }
        $data = array_merge($data, $newData);
        if ($user->update($data)) {
            return $this->success('User Updated Successfully.', new UserResource($user));
        }
        
        return $this->validation('Error while updating user. Please try again later.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     tags={"Users"},
     *     path="/api/v1/user/{user}",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     summary="Hard delete user details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function destroy($user)
    {
        $user  = User::onlyTrashed()->find($user);
        if($user){
            $user->forceDelete();
            return $this->noContent('User deleted successfully.');
        }

        return $this->validation('User not found or already deleted.');
    }
    
    /**
     * Remove the specified resource from storage as tempery.
     */
    /**
     * @OA\Delete(
     *     tags={"Users"},
     *     path="/api/v1/user/{user}/trashed",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     summary="Soft delete user details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function delete(User $user)
    {
        if ($user->delete()) {
            return $this->noContent('User deleted temporarily.');
        }

        return $this->validation('Invalid Request. This user is already deleted.');
    }

    /**
     * Restore the deleted resource from storage.
     */
    /**
     * @OA\Patch(
     *     tags={"Users"},
     *     path="/api/v1/user/{user}/restore",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     summary="Retore soft deleted user details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function restore($user)
    {
        $user  = User::onlyTrashed()->find($user);
        if($user){
            $user->restore();
            return $this->noContent('User restored successfully.');
        }

        return $this->validation('No trash data of this user.');
    }


    //Auth User
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Authenticate user and generate token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(AuthRequest $request){
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            $data = $request->validated();
            
            if(!Auth::attempt($data)){
                return $this->unauthorized('Invalid login details.');
            }

            $user = $request->user();
            $tokenResult = $user->createToken('PersonalAccessToken_'.$user->id);
            $user->accessToken = $tokenResult->plainTextToken;
            $user->token_type = 'Bearer';
            $user = new UserResource($user);
            return $this->success('Login Success', $user);
        } catch (\Throwable $th) {
            return $this->internalServer('Somthing worng', $th->getMessage());
        }
    }
}
