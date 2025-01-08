<?php

namespace App\Http\Controllers\Events;

use App\Constants\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCategoryController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $eventCategories = cache()->remember('event_categories', 60, function () {
                return EventCategory::all(['name', 'slug', 'icon']); // Select only name, slug, and icon
            });

            if (empty($eventCategories)) {
                return $this->sendError('No event categories found', [], 4004);
            }
            return $this->sendResponse($eventCategories, 'Event categories retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve event categories', [], 5000);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name',
            'icon' => 'required|string|max:255|url', // Validate icon as a URL
            'description' => 'required|string', // Validate icon as a URL
        ], [
            'name.unique' => 'Category already exists', // Custom error message
        ]);

        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:event_categories,name',
                'icon' => 'required|string|max:255|url', // Validate icon as a URL
            ]);
            $validatedData['slug'] = Str::slug($validatedData['name']);
            $eventCategory = EventCategory::create($validatedData);

            return $this->sendResponse(
                [
                    'name' => $eventCategory->name,
                    'slug' => $eventCategory->slug,
                    'icon' => $eventCategory->icon,
                ],
                SuccessMessages::SUCCESSFUL
            );
        } catch (Exception $e) {
            return $this->sendError(
                'Failed to create event category' . $e->getMessage(),
                [],
                5000
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $eventCategory = cache()->remember("event_category_{$slug}", 60, function () use ($slug) {
            return EventCategory::where('slug', $slug)->first(['name', 'slug', 'icon']); // Find by slug and select only name, slug, and icon
        });

        if (!$eventCategory) {
            return $this->sendError('Invalid category', [], 4004);
        }

        return $this->sendResponse(
            $eventCategory,
            SuccessMessages::SUCCESSFUL
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        try {
            $eventCategory = EventCategory::where('slug', $slug)->first();

            if (!$eventCategory) {
                return $this->sendError('Invalid category', [], 4004);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'icon' => 'required|string|max:255|url', // Validate icon as a URL

            ]);
            $validatedData['slug'] = Str::slug($validatedData['name']);

            $eventCategory->update($validatedData); // Update the category with validated data
            return $this->sendResponse(
                [
                    'name' => $eventCategory->name,
                    'slug' => $eventCategory->slug,
                    'icon' => $eventCategory->icon,
                ],
                SuccessMessages::SUCCESSFUL
            );
        } catch (Exception $e) {
            return $this->sendError('Failed to update event category', [], 5000);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        try {
            $eventCategory = EventCategory::where('slug', $slug)->first(); // Find by slug

            if (!$eventCategory) {
                return $this->sendError('Invalid category', [], 4004);
            }

            $eventCategory->delete(); // Delete the specified category
            return $this->sendMessage(SuccessMessages::SUCCESSFUL, 2000);
        } catch (Exception $e) {
            return $this->sendError('Failed to delete event category', [], 5000);
        }
    }
}
