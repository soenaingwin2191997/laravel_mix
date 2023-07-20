<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function home() {
        $pageTitle      = 'Dashboard';
        $plans          = Plan::active()->get();
        $user           = auth()->user();
        $pendingDeposit = $user->deposits->where('status', 2)->count();
        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'plans', 'user'));
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'Payment History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway', 'subscription.plan'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function watchHistory() {
        $pageTitle = 'Watch History';
        $histories = History::where('user_id', auth()->id());
        $total     = $histories->count();
        if (request()->lastId) {
            $histories = $histories->where('id', '<', request()->lastId);
        }
        $histories = $histories->with('item', 'episode.item')->orderBy('id', 'desc')->take(20)->get();
        $lastId    = @$histories->last()->id;

        if (request()->lastId) {
            if ($histories->count()) {
                $data = view($this->activeTemplate . 'user.watch.fetch_history', compact('histories'))->render();
                return response()->json([
                    'data'   => $data,
                    'lastId' => $lastId,
                ]);
            }
            return response()->json([
                'error' => 'History not more yet',
            ]);
        }
        return view($this->activeTemplate . 'user.watch.history', compact('pageTitle', 'histories', 'lastId', 'total'));
    }

    public function removeHistory(Request $request, $id) {
        History::where('id', $id)->where('user_id', auth()->id())->delete();
        $notify[] = ['success', 'Item removed from history list.'];
        return back()->withNotify($notify);
    }

    public function attachmentDownload($fileHash) {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData() {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request) {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function subscribePlan(Request $request, $id) {
        $plan = Plan::where('status', 1)->find($id);
        if (!$plan) {
            $notify[] = ['error', 'Plan not found'];
            return back()->withNotify($notify);
        }
        $user           = auth()->user();
        $pendingPayment = $user->deposits()->where('status', 2)->count();
        if ($pendingPayment > 0) {
            $notify[] = ['error', 'Already 1 payment in pending. Please Wait'];
            return back()->withNotify($notify);
        }
        $subscription               = new Subscription();
        $subscription->user_id      = $user->id;
        $subscription->plan_id      = $plan->id;
        $subscription->expired_date = now()->addDays($plan->duration);
        $subscription->save();
        session()->put('subscription_id', $subscription->id);
        return redirect()->route('user.deposit.index');
    }
}
