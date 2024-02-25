<?php

namespace App\Http\Controllers\ToDo\APIs\V1;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Resources\ToDo\v1\TodoResource;
use App\Http\Resources\ToDo\v1\TodoCollection;
use App\Http\Traits\HttpResource;
use App\Http\Requests\ToDo\v1\TodoStoreRequest;
use App\Http\Requests\ToDo\v1\TodoUpdateRequest;

class TodoController extends Controller
{
    use  HttpResource;

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *   tags={"Todo"},
     *   path="/api/v1/todo",
     *   summary="Get all Todo's List",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function index()
    {
        $todoList = Todo::whereNull('deleted_at')->where('is_completed', 0)->orderBy('id', 'desc')->paginate();
        // return response()->json($todoList, 200);
        return $this->success('Todo List', new TodoCollection($todoList));
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
     *     path="/api/v1/todo",
     *     tags={"Todo"},
     *     summary="Creating new todo task",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Todo title",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Todo description",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User Id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Todo added successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Failed to add todo")
     * )
     */
    public function store(TodoStoreRequest $request)
    {
        try {
            if(!$request->wantsJson()){
                return $this->validation('Invalid data format, Its allow only json request.');
            }

            $data = $request->validated();
            $todo = new TodoResource(Todo::create($data));
            return $this->objectCreated('Todo task has been created Successfully!', $todo);

        } catch (\Throwable $th) {
            return $this->internalServer('Something wrong', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        if(!$todo){
            return $this->success('No User Details Found.');
        }

        $todo = new TodoResource($todo);
        return $this->success('Todo Details', $todo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo){}

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/v1/todo/{todo}",
     *     tags={"Todo"},
     *     summary="Update todo task",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Todo title",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Todo description",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="todo",
     *         in="path",
     *         description="Todo Id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Todo Update successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Failed to update todo")
     * )
     */
    public function update(TodoUpdateRequest $request, Todo $todo)
    {
        $data = $request->validated();
        if ($todo->update($data)) {
            return $this->success('Todo Updated Successfully.', new TodoResource($todo));
        }
        
        return $this->validation('Error while updating todo. Please try again later.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     tags={"Todo"},
     *     path="/api/v1/todo/{todo}",
     *     @OA\Parameter(
     *         name="todo",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     summary="Hard delete todo details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function destroy($todo)
    {
        $todo  = Todo::onlyTrashed()->find($todo);
        if($todo){
            $todo->forceDelete();
            return $this->noContent('Todo deleted successfully.');
        }

        return $this->validation('Invalid request method, please use delete method.');
    }

    /**
     * Remove the specified resource from storage as temp.
     */
    /**
     * @OA\Delete(
     *     tags={"Todo"},
     *     path="/api/v1/todo/trashed/{todo}",
     *     @OA\Parameter(
     *         name="todo",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     summary="Soft delete todo details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function delete($todo)
    {
        $todo = Todo::whereNull('deleted_at')->where('id', $todo)->first();
        if ($todo->delete()) {
            return $this->noContent('Todo deleted successfully.');
        }

        return $this->validation('Invalid request method, Please use Delete method.');
    }

    /**
     * Restore the deleted resource from storage.
     */
    /**
     * @OA\Patch(
     *     tags={"Todo"},
     *     path="/api/v1/todo/restore/{todo}",
     *     @OA\Parameter(
     *         name="todo",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     summary="Restore soft deleted todo details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function restore($todo)
    {
        $todo  = Todo::onlyTrashed()->find($todo);
        if($todo){
            $todo->restore();
            return $this->noContent('Todo restored successfully.');
        }

        return $this->validation('No trash data of this user.');
    }


    /**
     * @OA\Patch(
     *     tags={"Todo"},
     *     path="/api/v1/todo/isDone/{todo}/{status}",
     *     @OA\Parameter(
     *         name="todo",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         description="Todo Status",
     *         required=true,
     *         @OA\Schema(type="boolean")
     *     ),
     *     summary="Restore soft deleted todo details",
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function updateTodoStatus($todo, $status) {
        $todo  = Todo::find($todo);
        if($todo){
            $todo->is_completed = $status?1:0;
            $todo->save();
            return $this->noContent('Todo status updated successfully.', new TodoResource($todo));
        }

        return $this->validation('Invalid request method, please use patch method.');
    }
}
