<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Currency;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FRequest;

class EventController extends Controller
{
    /**
     * Display a listing of active events.
     */
    public function index(Request $request)
    {
        return Inertia::render('Events/Index', [
            'filters' => FRequest::all(['search', 'trashed', 'status']),
            'items' => Event::orderBy('event_date')
                ->where('user_id', $request->user()->id)
                ->filter(FRequest::only(['search', 'trashed', 'status']))
                ->paginate()
                ->withQueryString(),
            'currencies' => Currency::all(),
            'showAll' => false,
        ]);
    }

    /**
     * Display a listing of all events.
     */
    public function showAll()
    {
        return Inertia::render('Events/Index', [
            'items' => Event::orderBy('event_date')->paginate(),
            'currencies' => Currency::all(),
            'showAll' => true,
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
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\App\Models\Event
     */
    public function store(StoreEventRequest $request)
    {
        $frequency = $request->frequency > 0 ? $request->frequency : 1;

        $request
            ->validate(
                [
                    'title' => 'required',
                    'description' => '',
                    'event_date' => 'required',
                    'status' => 'required',
                    'intervals' => 'required|integer',
                    'reminder' => 'required|integer',
                ],
                [
                    'intervals' => 'A valid interval in months is required.'
                ]
            );

        $user = $request->user();
        for ($i = 0; $i < $frequency; $i++) {
            $computedDate = date('Y-m-d', strtotime($request->event_date . '+' . ($i * $request->intervals) . ' months'));

            $event = new Event($request->all());
            $event->event_date = $computedDate;
            $event->user_id = $user->id;
            $event->save();
        }

        if (request()->wantsJson()) {
            return $event;
        }

        return redirect()->back()->with('message', "Event $event->title added successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::find($id);

        if (request()->wantsJson()) {
            return $event;
        }

        return Inertia::render('Events/Show', [
            'item' => $event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        $validated = $request->validate([
            'title' => ['required'],
            'description' => [],
            'event_date' => ['required'],
        ]);

        $event = Event::findOrFail($id);
        $event->update($validated);

        if ($request->next) {
            if ($this->createNext($event)) {
                return redirect()->back()->with('message', "Next event created.");
            } else {
                return redirect()->back()->with('message', "There was an error creating the next event.");
            }
        }

        return redirect()->back()->with('message', "Event $event->title updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(UpdateEventRequest $request, string $id)
    {
        $request->validate([
            'status' => ['required', 'max:20'],
        ]);

        $event = Event::findOrFail($id);
        if ($event->update($request->all())) {
            return redirect()->back()->with('message', "Event $event->title status updated successfully");
        };

        return redirect()->back()->with('message', "There was an error updating the status of the event $event->title.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);

        if ($event->delete()) {
            return redirect()->back()->with('message', "$event->title deleted successfully");
        }

        return redirect()->back()->with('message', "$event->title delete failed due an error.");
    }

    /**
     * Remove the selected resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMany(Request $request)
    {
        if (Event::whereIn('id', $request->items)->delete()) {
            return redirect()->back()->with('message', "Selected items deleted successfully");
        }

        return redirect()->back()->with('message', "There was an error in deleting the selected items");
    }
}
