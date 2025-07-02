<?php

namespace App\Http\Middleware;

use App\Models\Organization;
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
            $organization_id = session('organization_id');
            if (isset($organization_id)) {
                $organization = Organization::whereId($organization_id)->first();
            } else {
                $organization = null;
            }
            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'organization_name' => $organization ? $organization->short_name : $user->organization->short_name,
                'organization_id' => $organization ? $organization->id : $user->organization->id,
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
