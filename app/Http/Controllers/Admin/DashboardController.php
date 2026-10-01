<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\GalleryImage;
use App\Models\Lead;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'adminCount' => Admin::count(),
            'blogCount' => Blog::count(),
            'reviewCount' => Review::count(),
            'serviceCount' => Service::count(),
            'galleryCount' => GalleryImage::count(),
            'leadCount' => Lead::count(),
            'newLeadCount' => Lead::where('status', 'new')->count(),
        ]);
    }
}
