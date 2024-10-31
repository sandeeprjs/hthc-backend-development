<?php

namespace App\Http\Middleware;

use App\Permission;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $roles
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        $roles = $user->roles;

        $segment = $request->segment(2);

        if ($segment == 'master' || $segment == 'manifests') {
            if (!($segment == 'manifests' && $request->segment(3) == null)) {
                $segment = $request->segment(3);
            }
        }

        foreach ($roles as $role) {
            $module = $role->hasSegment($segment);

            if (!$module) {
                return abort(403);
            }
            $permission = Permission::where('role_id', $role->id)->where('module_id', $module->id)->first();

            $urlSegments = explode('/', $request->url());

            if (array_search('edit', $urlSegments)  && $permission->update == 1) {
                return $next($request);
            }

            if (array_search('create', $urlSegments)  && $permission->create == 1) {
                return $next($request);
            }

            if (array_search('delete', $urlSegments)  && $permission->delete == 1) {
                return $next($request);
            }

            if (!in_array('create', $urlSegments) && !in_array('edit', $urlSegments) && !in_array('delete', $urlSegments) && $permission->read == 1) {
                return $next($request);
            }

        }

        return abort(403);
    }
}
