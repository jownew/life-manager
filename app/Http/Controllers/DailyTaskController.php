<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\StoreDailyTaskRequest;
use App\Http\Requests\UpdateDailyTaskRequest;
use App\Models\DailyTask;
use Illuminate\Support\Facades\Request as FRequest;

class DailyTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('DailyTasks/Index', [
            'filters' => FRequest::all(['search', 'trashed', 'status']),
            'items' => DailyTask::orderBy('planned_time')
                ->where('user_id', FRequest::user()->id)
                ->filter(FRequest::only(['search', 'trashed', 'status']))
                ->paginate()
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDailyTaskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyTask $dailyTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyTask $dailyTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDailyTaskRequest $request, DailyTask $dailyTask)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyTask $dailyTask)
    {
        //
    }

    public function toggle(UpdateDailyTaskRequest $request, string $id)
    {
        $dailyTask = DailyTask::findOrFail($id);

        if ($dailyTask->completed_time == null) {
            $dailyTask->completed_time = now();
            $statusMessage = 'completed';
        } else {
            $dailyTask->completed_time = null;
            $statusMessage = 'to do';
        }

        if ($dailyTask->update($request->all())) {
            return redirect()->back()->with('message', "Task '$dailyTask->title' is marked as $statusMessage.");
        }

        return redirect()->back()->with('message', "There was an error updating the status of the event $dailyTask->title.");
    }

}
