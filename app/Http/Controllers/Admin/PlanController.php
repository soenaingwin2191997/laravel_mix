<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller {
    public function index() {
        $pageTitle = "All Plans";
        $plans     = Plan::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function store(Request $request, $id = 0) {
        $limitValidate = gs()->device_limit ? 'required' : 'nullable';
        $request->validate([
            'name'         => 'required|unique:plans,name,' . $id,
            'price'        => 'required|numeric|gt:0',
            'duration'     => 'required|integer|gt:0',
            'icon'         => 'required|string',
            'device_limit' => "$limitValidate|integer|gte:0",
        ]);

        if ($id == 0) {
            $plan         = new Plan();
            $notification = 'Plan created successfully';
        } else {
            $plan         = Plan::findOrFail($id);
            $notification = 'Plan updated successfully';
        }

        $plan->name     = $request->name;
        $plan->pricing  = $request->price;
        $plan->duration = $request->duration;
        $plan->icon     = $request->icon;
        if (gs()->device_limit) {
            $plan->device_limit = $request->device_limit;
        }
        $plan->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Plan::changeStatus($id);
    }
}
