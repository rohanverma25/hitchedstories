<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private ImageService $images)
    {
    }

    /**
     * List the services.
     */
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::orderBy('sort_order')->orderBy('title')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a service.
     */
    public function create(): View
    {
        return view('admin.services.create', ['service' => new Service(['is_published' => true])]);
    }

    /**
     * Store a new service.
     */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $data = [...$data, ...$this->images->store($request->file('image'), 'services')];
        }

        Service::create($data);

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    /**
     * Show the form for editing a service.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', ['service' => $service]);
    }

    /**
     * Update a service.
     */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $this->images->delete($service->image, $service->thumbnail);
            $data = [...$data, ...$this->images->store($request->file('image'), 'services')];
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($service->image, $service->thumbnail);
            $data = [...$data, 'image' => null, 'thumbnail' => null];
        }

        $service->update($data);

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    /**
     * Delete a service and its images.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $this->images->delete($service->image, $service->thumbnail);
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }
}
