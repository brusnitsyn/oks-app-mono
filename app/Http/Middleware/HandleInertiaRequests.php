<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = auth()->user();
        if ($user === null) $userInfo = null;
        else {
            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'organization_name' => $user->organization->short_name,
                'role' => $user->role->slug,
                'profile_photo_url' => $user->profile_photo_url,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userInfo
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'query' => $request->query(),
            ],
        ];
    }
}
