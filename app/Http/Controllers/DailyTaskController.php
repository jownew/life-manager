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
                ->userOwned()
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
        $validated = $request->validate(
            [
                'title' => ['required'],
                'description' => [],
            ],
            [
                'title.required' => 'Please enter a valid task.',
            ]
        );

        $validated['user_id'] = $request->user()->id;
        $validated['planned_time'] = now();
        $dailyTask = DailyTask::create($validated);

        if ($dailyTask->exists()) {
            $message = "Task '$dailyTask->title' added successfully.";
        } else {
            $message = "There was an error in adding the task Task '$dailyTask->title'.";
        }

        return redirect()->back()->with('message', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyTask $dailyTask)
    {
        return $dailyTask;
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
        $validated = $request->validate(
            [
                'title' => ['required'],
                'description' => [],
            ],
            [
                'title.required' => 'Please enter a valid task.',
            ]
        );

        if ($dailyTask->update($validated)) {
            $message = "Task '$dailyTask->title' updated successfully.";
        } else {
            $message = "There was an error in updating the task Task '$dailyTask->title'.";
        }

        return redirect()->back()->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyTask $dailyTask)
    {
        if ($dailyTask->delete()) {
            $message = "Task '$dailyTask->title' deleted successfully.";
        } else {
            $message = "There was an error in deleting the task Task '$dailyTask->title'.";
        }

        return redirect()->back()->with('message', $message);
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

    public function snooze(UpdateDailyTaskRequest $request, string $id)
    {
        $dailyTask = DailyTask::findOrFail($id);

        $dailyTask->planned_time = date("Y-m-d H:i:s", strtotime("+1 hours"));

        if ($dailyTask->update($request->all())) {
            return redirect()->back()->with('message', "Task '$dailyTask->title' is set to do later.");
        }

        return redirect()->back()->with('message', "There was an error updating the status of the event $dailyTask->title.");
    }

    public function prioritise(UpdateDailyTaskRequest $request, DailyTask $dailyTask)
    {
        $earliestDailyTask = DailyTask::orderBy('planned_time')
            ->userOwned()
            ->where('completed_time', null)
            ->first();

        if (!$earliestDailyTask || $earliestDailyTask->id == $dailyTask->id) {
            return redirect()->back()->with([
                'message_type' => 'error',
                'message' => "This task is already at the highest priority."
            ]);
        }

        $dailyTask->planned_time = date('Y-m-d H:i:s', strtotime("{$earliestDailyTask->planned_time} - 1 minute"));
        if ($dailyTask->save()) {
            return redirect()->back()->with('message', "Successfully prioritised the task: '$dailyTask->title'");
        }

        return redirect()->back()->with([
            'message_type' => 'error',
            'message' => "There was an error prioritising the task: '$dailyTask->title'"
        ]);
    }

    public function destroyMany()
    {
        $data = FRequest::all();

        if (!isset($data['items']) || count($data['items']) == 0) {
            return redirect()->back()->with('message', 'No items to delete.');
        }

        if (DailyTask::whereIn('id', $data['items'])->delete()) {
            return redirect()->back()->with('message', "Selected items deleted successfully");
        }

        return redirect()->back()->with('message', "There was an error in deleting the selected items");
    }
}
