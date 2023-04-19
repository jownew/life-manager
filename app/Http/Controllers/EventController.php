<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Currency;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of active events.
     */
    public function index()
    {
        return Inertia::render('Events/Index', [
            'items' => Event::orderBy('event_date')->where('status', 'active')->paginate(),
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
        $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'event_date' => ['required'],
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

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
}
