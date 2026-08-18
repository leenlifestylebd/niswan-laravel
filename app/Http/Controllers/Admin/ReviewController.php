<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews', [
            'reviews' => Review::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image_url' => ['required', 'string'],
        ], [
            'image_url.required' => 'ছবি দরকার',
        ]);

        Review::create([
            'image_url'  => $data['image_url'],
            'sort_order' => (int) Review::max('sort_order') + 1,
        ]);

        return back()->with('status', 'রিভিউ যোগ হয়েছে');
    }

    public function destroy(int $id)
    {
        Review::findOrFail($id)->delete();

        return back()->with('status', 'রিভিউ মুছে ফেলা হয়েছে');
    }
}
