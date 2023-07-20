<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\Episode;
use App\Models\GeneralSetting;
use App\Models\History;
use App\Models\Item;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VideoReport;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller {

    public function userInfo() {
        $notify[] = 'User information';
        return response()->json([
            'remark'  => 'user_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'user' => auth()->user()->load('plan'),
            ],
        ]);
    }

    public function userDataSubmit(Request $request) {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            $notify[] = 'You\'ve already completed your profile';
            return response()->json([
                'remark'  => 'already_completed',
                'status'  => 'error',
                'message' => ['error' => 'You\'ve already completed your profile'],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = 1;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return response()->json([
            'remark'  => 'profile_completed',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function depositHistory(Request $request) {
        $deposits = auth()->user()->deposits()->selectRaw("*, DATE_FORMAT(created_at, '%Y-%m-%d %h:%m') as date");
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway', 'subscription.plan'])->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Deposit data';
        return response()->json([
            'remark'  => 'deposits',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'deposits' => $deposits,
            ],
        ]);
    }

    public function submitProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->save();

        $notify[] = 'Profile updated successfully';
        return response()->json([
            'remark'  => 'profile_updated',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function submitPassword(Request $request) {
        $passwordValidation = Password::min(6);
        $general            = GeneralSetting::first();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return response()->json([
                'remark'  => 'password_changed',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => 'The password doesn\'t match!'],
            ]);
        }
    }

    public function plans() {
        $notify[]  = 'Plan';
        $plans     = Plan::where('status', 1)->paginate(getPaginate());
        $imagePath = getFilePath('plan');

        return response()->json([
            'remark'  => 'plan',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'plans'      => $plans,
                'image_path' => $imagePath,
            ],
        ]);
    }

    public function subscribePlan(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        $plan = Plan::where('status', 1)->find($request->id);

        if (!$plan) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Plan not found'],
            ]);
        }

        if ($user->exp > now()) {
            return response()->json([
                'remark'  => 'already_subscribe',
                'status'  => 'error',
                'message' => ['error' => 'You already subscribed to a plan'],
            ]);
        }

        $pendingPayment = $user->deposits()->where('status', 2)->count();
        if ($pendingPayment > 0) {
            return response()->json([
                'remark'  => 'pending_payment',
                'status'  => 'error',
                'message' => ['error' => 'Already 1 payment in pending. Please wait'],
            ]);
        }

        $subscription               = new Subscription();
        $subscription->user_id      = $user->id;
        $subscription->plan_id      = $plan->id;
        $subscription->expired_date = now()->addDays($plan->duration);
        $subscription->save();

        $notify[] = 'Plan Purchase';

        return response()->json([
            'remark'  => 'subscribe_payment',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'subscription_id' => $subscription->id,
                'redirect_url'    => route('user.deposit.index'),
            ],
        ]);
    }

    //Wishlist
    public function wishlists() {
        $notify[]  = 'Wishlist';
        $wishlists = Wishlist::with('item.category', 'episode')->where('user_id', auth()->id())->paginate(getPaginate());

        return response()->json([
            'remark'  => 'wishlist',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'wishlists' => $wishlists,
            ],
        ]);
    }

    public function addWishList(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $wishlist = new Wishlist();

        if ($request->item_id) {
            $item = Item::find($request->item_id);
            if (!$item) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Item not found'],
                ]);
            }
            $exits             = Wishlist::where('item_id', $item->id)->where('user_id', auth()->id())->first();
            $wishlist->item_id = $item->id;

        } elseif ($request->episode_id) {
            $episode = Episode::find($request->episode_id);
            if (!$episode) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Episode not found'],
                ]);
            }
            $exits                = Wishlist::where('episode_id', $episode->id)->where('user_id', auth()->id())->first();
            $wishlist->episode_id = $episode->id;
        }

        if (!$exits) {
            $wishlist->user_id = auth()->id();
            $wishlist->save();

            $notify[] = 'Video added to wishlist successfully';
            return response()->json([
                'remark'  => 'added_successfully',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        }

        $notify[] = 'Already in wishlist';
        return response()->json([
            'remark'  => 'already_exits',
            'status'  => 'error',
            'message' => ['error' => 'Already in wishlist'],
        ]);
    }

    public function checkWishlist(Request $request) {

        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $wishlist = 0;
        if ($request->item_id) {
            $item = Item::find($request->item_id);
            if (!$item) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Item not found'],
                ]);
            }
            $wishlist = Wishlist::where('item_id', $item->id)->where('user_id', auth()->id())->count();
        } else if ($request->episode_id) {
            $episode = Episode::find($request->episode_id);
            if (!$episode) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Episode not found'],
                ]);
            }
            $wishlist = Wishlist::where('episode_id', $episode->id)->where('user_id', auth()->id())->count();
        }
        if ($wishlist) {
            $notify[] = 'Already in wishlist';
            return response()->json([
                'remark'  => 'true',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Data not found';
            return response()->json([
                'remark'  => 'false',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function removeWishlist(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        if ($request->item_id) {
            $wishlist = Wishlist::where('item_id', $request->item_id)->where('user_id', auth()->id());
        }

        if ($request->episode_id) {
            $wishlist = Wishlist::where('episode_id', $request->episode_id)->where('user_id', auth()->id());
        }

        $wishlist = $wishlist->first();

        if ($wishlist) {
            $wishlist->delete();
            $notify[] = 'Video removed from wishlist successfully';
            return response()->json([
                'remark'  => 'remove_successfully',
                'status'  => 'success',
                'message' => ['success' => 'Video removed from wishlist successfully'],
            ]);
        }

        $notify[] = 'Something wrong';
        return response()->json([
            'remark'  => 'something_wrong',
            'status'  => 'error',
            'message' => ['success' => $notify],
        ]);
    }

    public function history() {
        $notify[]  = 'History';
        $histories = History::with('item', 'episode')->where('user_id', auth()->id())->paginate(getPaginate());

        return response()->json([
            'remark'  => 'history',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'histories' => $histories,
            ],
        ]);
    }

    public function watchVideo(Request $request) {

        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        $relatedItems = Item::hasVideo()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get(['image', 'id', 'version', 'item_type']);

        if ($item->item_type == 2) {
            $episodes = Episode::hasVideo()->where('item_id', $request->item_id)->get();

            $notify[] = 'Episode Video';
            return response()->json([
                'remark'  => 'episode_video',
                'status'  => 'success',
                'message' => ['success' => $notify],
                'data'    => [
                    'item'          => $item,
                    'episodes'      => $episodes,
                    'related_items' => $relatedItems,
                ],
            ]);
        }

        if ($item->version == 1 && auth()->user()->exp < now()) {
            return response()->json([
                'remark'  => 'purchase_plan',
                'status'  => 'error',
                'message' => ['error' => 'Purchase a plan for watch paid video'],
            ]);
        }

        $notify[] = 'Item Video';
        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'item'          => $item,
                'related_items' => $relatedItems,
            ],
        ]);
    }

    public function playVideo(Request $request) {

        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        if ($item->item_type == 2 && !$request->episode_id) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Episode id field is required'],
            ]);
        }

        if ($item->item_type == 2) {
            $episode = Episode::hasVideo()->where('item_id', $request->item_id)->find($request->episode_id);

            if (!$episode) {
                return response()->json([
                    'remark'  => 'no_episode',
                    'status'  => 'error',
                    'message' => ['error' => 'No episode found'],
                ]);
            }

            if ($episode->version == 1 && auth()->user()->exp < now()) {
                return response()->json([
                    'remark'  => 'purchase_plan',
                    'status'  => 'error',
                    'message' => ['error' => 'Purchase a plan for watch paid video'],
                ]);
            }

            $videoFile = getVideoFile($episode->video);

            $subtitles    = $episode->video->subtitles()->get();
            $adsTime      = $episode->video->getAds();
            $subtitlePath = getFilePath('subtitle');

            $this->storeHistory(0, $episode->id);
            $this->storeVideoReport(0, $episode->id);

            $notify[] = 'Episode Video';
            return response()->json([
                'remark'  => 'episode_video',
                'status'  => 'success',
                'message' => ['success' => $notify],
                'data'    => [
                    'video'        => $videoFile,
                    'subtitles'    => $subtitles,
                    'adsTime'      => $adsTime,
                    'subtitlePath' => $subtitlePath,
                ],
            ]);
        }

        if ($item->version == 1 && auth()->user()->exp < now()) {
            return response()->json([
                'remark'  => 'purchase_plan',
                'status'  => 'error',
                'message' => ['error' => 'Purchase a plan for watch paid video'],
            ]);
        }

        $videoFile    = getVideoFile($item->video);
        $subtitles    = $item->video->subtitles()->get();
        $adsTime      = $item->video->getAds();
        $subtitlePath = getFilePath('subtitle');

        $this->storeHistory($item->id, 0);
        $this->storeVideoReport($item->id, 0);

        $notify[] = 'Item Video';
        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'video'        => $videoFile,
                'subtitles'    => $subtitles,
                'adsTime'      => $adsTime,
                'subtitlePath' => $subtitlePath,
            ],
        ]);

    }

    protected function storeHistory($itemId = null, $episodeId = null) {
        if (auth()->check()) {
            if ($itemId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();

                if (!$history || ($history && $history->item_id != $itemId)) {
                    $history          = new History();
                    $history->user_id = auth()->id();
                    $history->item_id = $itemId;
                    $history->save();
                }
            }
            if ($episodeId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->episode_id != $episodeId)) {
                    $history             = new History();
                    $history->user_id    = auth()->id();
                    $history->episode_id = $episodeId;
                    $history->save();
                }
            }
        }
    }

    protected function storeVideoReport($itemId = null, $episodeId = null) {
        $deviceId = md5($_SERVER['HTTP_USER_AGENT']);

        if ($itemId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('item_id', $itemId)->exists();
        }

        if ($episodeId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('episode_id', $episodeId)->exists();
        }
        if (!$report) {
            $videoReport             = new VideoReport();
            $videoReport->device_id  = $deviceId;
            $videoReport->item_id    = $itemId ?? 0;
            $videoReport->episode_id = $episodeId ?? 0;
            $videoReport->save();
        }
    }

    public function getDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();
        if ($deviceToken) {
            $notify[] = 'Already exists';
            return response()->json([
                'remark'  => 'get_device_token',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->id();
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = 1;
        $deviceToken->save();

        $notify[] = 'Token save successfully';
        return response()->json([
            'remark'  => 'get_device_token',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function purchasePlan(Request $request) {
        $validator = Validator::make($request->all(), [
            'username'    => 'required',
            'token'       => 'required',
            'plan_id'     => 'required|integer',
            'amount'      => 'required',
            'method_code' => 'required|in:-1,-2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $plan = Plan::active()->where('id', $request->plan_id)->first();
        if (!$plan) {
            $notify[] = ['error', 'Plan not found'];
            return back()->withNotify($notify);
        }
        $subscription               = new Subscription();
        $subscription->user_id      = auth()->id();
        $subscription->plan_id      = $plan->id;
        $subscription->expired_date = now()->addDays($plan->duration);
        $subscription->save();

        $general = gs();

        $detail = [
            'username' => $request->username,
            'plan'     => $plan->name,
            'amount'   => $request->amount,
        ];

        $data                  = new Deposit();
        $data->user_id         = auth()->id();
        $data->subscription_id = $subscription->id;
        $data->method_code     = $request->method_code;
        $data->method_currency = strtoupper($general->cur_text);
        $data->amount          = $request->amount;
        $data->charge          = 0;
        $data->rate            = gs()->cur_text;
        $data->final_amo       = $request->amount;
        $data->btc_amo         = 0;
        $data->btc_wallet      = "";
        $data->detail          = $detail;
        $data->trx             = getTrx();
        $data->status          = Status::PAYMENT_PENDING;
        $data->save();

        $user          = User::find($data->user_id);
        $user->plan_id = $subscription->plan_id;
        $user->exp     = $subscription->expired_date;
        $user->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = $user->username . ' subscribed to ' . $plan->name;
        $adminNotification->click_url = urlPath('admin.deposit.pending');
        $adminNotification->save();

        notify($user, 'SUBSCRIBE_PLAN', [
            'plan'     => $plan->name,
            'price'    => showAmount($plan->pricing),
            'duration' => $plan->duration,
        ]);

        $notify[] = 'You have deposit request has been taken';
        return response()->json([
            'remark'  => 'payment_pending',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);

    }

    public function status() {
        $user         = auth()->user();
        $user->status = !$user->status;
        $user->save();
        $status   = $user->status == 1 ? 'active' : 'delete';
        $notify[] = 'Your account is ' . $status;
        return response()->json([
            'remark'  => 'account_status',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'status' => $status,
            ],
        ]);

    }
}
