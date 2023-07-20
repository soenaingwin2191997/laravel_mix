<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Episode;
use App\Models\Frontend;
use App\Models\Item;
use App\Models\Language;
use App\Models\LiveTelevision;
use App\Models\Slider;
use App\Models\Advertise;
use App\Models\SubCategory;
use App\Models\VideoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller {

    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum')->only('watchVideo');
    // }
    public function logo() {
        $notify[] = 'Logo Information';
        $logo     = getFilePath('logoIcon') . '/logo.png';
        $favicon  = getFilePath('logoIcon') . '/favicon.png';

        return response()->json([
            'remark'  => 'logo_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'logo'    => $logo,
                'favicon' => $favicon,
            ],
        ]);
    }

    public function welcomeInfo() {
        $notify[] = 'Welcome Info';
        $welcome  = Frontend::where('data_keys', 'app_welcome.content')->first();
        $path     = 'assets/images/frontend/app_welcome';

        return response()->json([
            'remark'  => 'welcome_info',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'welcome' => $welcome->data_values,
                'path'    => $path,
            ],
        ]);

    }

    public function sliders() {
        $sliders  = Slider::with('item', 'item.category', 'item.sub_category')->get();
        $notify[] = 'All Sliders';
        $path     = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_sliders',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'sliders'        => $sliders,
                'landscape_path' => $path,
            ],
        ]);
    }

    public function liveTelevision() {
        $notify[]    = 'Live Television';
        $televisions = LiveTelevision::where('status', 1)->paginate(getPaginate());
        $imagePath   = getFilePath('television');

        return response()->json([
            'remark'  => 'live_television',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'televisions' => $televisions,
                'image_path'  => $imagePath,
            ],
        ]);
    }

    public function featured() {
        $notify[]     = 'Featured';
        $featured     = Item::active()->hasVideo()->where('featured', 1)->orderBy('id', 'desc')->get(['title', 'image', 'id', 'version', 'item_type', 'category_id', 'sub_category_id', 'view', 'ratings']);
        $imagePath    = getFilePath('item_landscape');
        $portraitPath = getFilePath('item_portrait');

        return response()->json([
            'remark'  => 'featured',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'featured'       => $featured,
                'landscape_path' => $imagePath,
                'portrait_path'  => $portraitPath,
            ],
        ]);

    }

    public function recentlyAdded() {
        $notify[]      = 'Recently Added';
        $recentlyAdded = Item::hasVideo()->orderBy('id', 'desc')->where('item_type', 1)->limit(18)->get(['title', 'image', 'id', 'version', 'item_type', 'ratings', 'view']);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'recently_added',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'recent'         => $recentlyAdded,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function latestSeries() {
        $notify[]      = 'Latest Series';
        $latestSeries  = Item::hasVideo()->orderBy('id', 'desc')->where('item_type', 2)->get(['title', 'image', 'id', 'version', 'item_type', 'ratings', 'view']);
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'latest-series',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'latest'         => $latestSeries,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function single() {
        $notify[] = 'Single Item';

        $single = Item::hasVideo()->orderBy('id', 'desc')->where('single', 1)->with('category')->get(['image', 'title', 'ratings', 'preview_text', 'view', 'id', 'version', 'item_type']);

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'single',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'single'         => $single,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function trailer() {
        $notify[] = 'Trailer';

        $trailer = Item::hasVideo()->where('item_type', 3)->orderBy('id', 'desc')->limit(12)->get(['image', 'title', 'ratings', 'id', 'view']);

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'trailer',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'trailer'        => $trailer,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function freeZone() {
        $notify[]      = 'Free Zone';
        $freeZone      = Item::hasVideo()->free()->orderBy('id', 'desc')->limit(12)->select(['image', 'title', 'id', 'view', 'ratings'])->paginate(getPaginate());
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'free_zone',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'free_zone'      => $freeZone,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function categories() {
        $notify[]   = 'All Categories';
        $categories = Category::where('status', 1)->paginate(getPaginate());

        return response()->json([
            'remark'  => 'all-categories',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'categories' => $categories,
            ],
        ]);
    }

    public function subcategories(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $notify[]      = 'SubCategories';
        $subcategories = SubCategory::where('category_id', $request->category_id)->where('status', 1)->paginate(getPaginate());

        return response()->json([
            'remark'  => 'sub-categories',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'subcategories' => $subcategories,
            ],
        ]);

    }

    public function search(Request $request) {
        $notify[] = 'Search';
        $search   = $request->search;

        $items = Item::search($search)->where('status', 1)->where(function ($query) {
            $query->orWhereHas('video')->orWhereHas('episodes', function ($video) {
                $video->where('status', 1)->whereHas('video');
            });
        });

        if ($request->category_id) {
            $items = $items->where('category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $items = $items->where('sub_category_id', $request->subcategory_id);
        }

        $items = $items->orderBy('id', 'desc')->paginate(getPaginate());

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'search',
            'status'  => 'success',
            'message' => ['success'=> $notify],
            'data'    => [
                'items'          => $items,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function watchVideo(Request $request) {
        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        $relatedItems = Item::hasVideo()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get(['image', 'id', 'version', 'item_type']);

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        if ($item->item_type == 2) {
            $episodes = Episode::hasVideo()->where('item_id', $request->item_id)->get();
            
            $this->storeHistory(0,$episodes->id);
            $this->storeVideoReport(0,$episodes->id);
            
            $notify[] = 'Episode Video';
            return response()->json([
                'remark'  => 'episode_video',
                'status'  => 'success',
                'message' => ['success' => $notify],
                'data'    => [
                    'item'           => $item,
                    'episodes'       => $episodes,
                    'related_items'  => $relatedItems,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'episode_path'   => $episodePath,
                ],
            ]);
        }

        if ($item->version == 1) {
            return response()->json([
                'remark'  => 'unauthorized',
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
            ]);
        }
        
        $this->storeHistory($item->id,0);
        $this->storeVideoReport($item->id,0);
        
        $notify[] = 'Item Video';

        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'item'           => $item,
                'related_items'  => $relatedItems,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'episode_path'   => $episodePath,
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

            if ($episode->version == 1) {
                return response()->json([
                    'remark'  => 'unauthorized',
                    'status'  => 'error',
                    'message' => ['error' => 'Unauthorized user'],
                ]);
            }

            $videoFile = getVideoFile($episode->video);
            $subtitles    = $episode->video->subtitles()->get();
            $adsTime      = $episode->video->getAds();
            $subtitlePath = getFilePath('subtitle');

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

        if ($item->version == 1) {
            return response()->json([
                'remark'  => 'unauthorized',
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
            ]);
        }

        $videoFile    = getVideoFile($item->video);
        $subtitles    = $item->video->subtitles()->get();
        $adsTime      = $item->video->getAds();
        $subtitlePath = getFilePath('subtitle');
        $notify[]     = 'Item Video';
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
    
    public function policyPages() {
        $notify[]    = 'Policy Page';
        $policyPages = Frontend::where('data_keys', 'policy_pages.element')->get();

        return response()->json([
            'remark'  => 'policy',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'policy_pages' => $policyPages,
            ],
        ]);
    }

    public function movies() {
        $notify[]      = 'All Movies';
        $movies        = Item::hasVideo()->where('item_type', 1)->orderBy('id', 'desc')->select(['title', 'image', 'id', 'version', 'item_type'])->paginate(getPaginate());
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_movies',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'movies'         => $movies,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function episodes() {
        $notify[]      = 'All Episodes';
        $episodes      = Item::hasVideo()->where('item_type', 2)->orderBy('id', 'desc')->select(['title', 'image', 'id', 'version', 'item_type'])->paginate(getPaginate());
        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');

        return response()->json([
            'remark'  => 'all_episodes',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'episodes'       => $episodes,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
            ],
        ]);
    }

    public function watchTelevision($id) {
        $tv = LiveTelevision::where('id', $id)->where('status', 1)->first();

        if (!$tv) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Television not found'],
            ]);
        }

        $notify[]  = $tv->title;
        $relatedTv = LiveTelevision::where('id', '!=', $id)->where('status', 1)->limit(8)->orderBy('id', 'desc')->get();
        $imagePath = getFilePath('television');
        return response()->json([
            'remark'  => 'tv_details',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'tv'         => $tv,
                'related_tv' => $relatedTv,
                'image_path' => $imagePath,
            ],
        ]);
    }
    
    public function language($code) {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            $code = 'en';
        }
        $languageData = json_decode(file_get_contents(resource_path('lang/' . $code . '.json')));
        $languages    = Language::get();
        $notify[]     = 'Language Data';
        return response()->json([
            'remark'  => 'language_data',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'language_data' => $languageData,
                'languages'     => $languages,
            ],
        ]);
    }
    public function popUpAds() {
        $advertise = Advertise::where('device', 2)->where('ads_show', 1)->where('ads_type', 'banner')->inRandomOrder()->first();
        $imagePath = getFilePath('ads');
        return response()->json([
            'remark' => 'pop_up_ad',
            'status' => 'success',
            'data'   => [
                'advertise' => $advertise,
                'imagePath' => $imagePath,
            ],
        ]);
    }
}
