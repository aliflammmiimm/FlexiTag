<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tag;
use App\Models\Tap;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('landing');
    }

    public function showTagProfile(Request $request, string $tag_code): View
    {
        $tag = Tag::with(['user.profile'])->where('tag_code', $tag_code)->firstOrFail();

        if ($tag->status === 'aktif' && $tag->user) {
            Tap::create([
                'tag_id' => $tag->id,
                'tapped_at' => now(),
                'source' => $request->header('sec-ch-ua-mobile') ? 'nfc' : 'link',
                'device_type' => $request->header('sec-ch-ua-mobile') ? 'mobile' : 'desktop',
                'region' => 'Makassar',
                'created_at' => now(),
            ]);
        }

        $links = $tag->user ? $tag->user->hasMany(Link::class, 'user_id')->where('is_visible', true)->orderBy('sort_order')->get() : collect();

        return view('user.public_profile', [
            'tag' => $tag,
            'user' => $tag->user,
            'profile' => $tag->user?->profile,
            'links' => $links,
        ]);
    }
}
