<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\Character;
use App\Http\Resources\TaskListResource;
use App\Http\Resources\CharacterResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Helpers\AchievementHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request): JsonResponse{
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;

        Task::create($validated);
        AchievementHandler::checkForAchievement('TASKS_MADE', Auth::user());

        $taskLists = TaskListResource::collection(TaskList::where('user_id', Auth::user()->id)->get());

        return new JsonResponse(['message' => ['message' => ["Task successfully created."]], 'data' => $taskLists], Response::HTTP_OK);
    }

    /*
    Will likely be changed to account for TaskList
    */
    public function show(Task $task) {
        // #30
    }

    public function showTasks(){
        // #30
    }
    /*

    */

    //TODO UpdateTaskRequest
    public function update(Task $task, UpdateTaskRequest $request){
        $validated = $request->validated();
        $task->update($validated);

        $taskLists = TaskListResource::collection(Auth::user()->taskLists);
        
        return new JsonResponse(['message' => ['message' => ["Task successfully updated."]], 'data' => $taskLists], Response::HTTP_OK);
    }

    public function destroy(Task $task): JsonResponse
    {
        if(Auth::user()->id === $task->user_id){
            $task->subTasks()->delete();
            $task->delete();

            $taskLists = TaskListResource::collection(Auth::user()->taskLists);
            return new JsonResponse(['message' => ['message' => ["Task successfully deleted."]], 'data' => $taskLists], Response::HTTP_OK);
        } else {
            return new JsonResponse(['errors' => ['error' => ["You are not authorized to delete this task"]]], Response::HTTP_FORBIDDEN);
        }
    }

    public function complete(Task $task){
        $user = Auth::user();
        if($user->id === $task->user_id){
            if($task->repeatable != 'NONE'){
                $task->completeRepeatable();
                $this->completeRepeatable($task);
            } else {
                $task->completed = Carbon::now();
                $task->update();
            }

            AchievementHandler::checkForAchievement('TASKS_COMPLETED', $user);
            
            $taskLists = TaskListResource::collection($user->taskLists);
            if($user->rewards == 'CHARACTER'){
                $character = $user->getActiveCharacter();
                $returnValue = $character->applyReward($task);
                return new JsonResponse(['message' => $returnValue->message, 'data' => $taskLists, 'character' => new CharacterResource($character->fresh())], Response::HTTP_OK);
            } else {
                return new JsonResponse(['message' => ['message' => ['Task completed.']], 'data' => $taskLists], Response::HTTP_OK);
            }
        } else {
            return new JsonResponse(['errors' => ['error' => ["You are not authorized to complete this task"]]], Response::HTTP_FORBIDDEN);
        }
    }

    private function completeRepeatable($task){
        $date = null;
        switch($task->repeatable){
            case 'DAILY':
                $date = Carbon::tomorrow();
                break;
            case 'WEEKLY':
                $date = new Carbon('next monday');
                break;
            case 'MONTHLY':
                $date = new Carbon('first day of next month midnight');
                break;
            case 'INFINITE':
                $date = new Carbon();
                break;
        }
        $task->repeatable_active = $date;
        $task->update();
        AchievementHandler::checkForAchievement('REPEATABLE_COMPLETED', Auth::user());
    }
}
