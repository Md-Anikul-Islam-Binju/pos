<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckModelStatusMiddleware
{
    public function handle(Request $request, Closure $next, $modelClass, $idParam)
    {
        // Get the model ID from the route parameter
        $modelId = $request->route($idParam);

        // Resolve the model class and find the instance by ID
        $model = app($modelClass)::find($modelId);

        // Check if the model exists
        if (!$model) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        // Ensure the retrieved result is a single model instance, not a collection
        if (is_iterable($model)) {
            return redirect()->back()->with('error', 'Unexpected collection instead of a model instance.');
        }

        // If the status is approved or rejected, prevent any updates or actions except status updates
        if (in_array($model->status, ['approved', 'rejected'])) {
            if ($request->isMethod('patch') || $request->isMethod('put')) {
                // Check if the request is trying to update fields other than the status
                $updatedFields = array_keys($request->all());
                if (array_diff($updatedFields, ['status', '_token', '_method'])) {
                    return redirect()->back()->with('error', 'Only status can be updated.');
                }
            }

            // Prevent delete, restore, or force delete for approved/rejected assets
            if ($request->isMethod('delete') || $request->route()->getActionMethod() === 'restore' || $request->route()->getActionMethod() === 'force_delete') {
                return redirect()->back()->with('error', 'Cannot delete or restore an approved/rejected item.');
            }
        }

        // Allow all actions if the status is pending
        return $next($request);
    }
}
