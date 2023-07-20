<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\VideoUploader;
use App\Models\Episode;
use App\Models\GeneralSetting;
use App\Models\Item;
use App\Models\Subtitle;
use App\Models\Video;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EpisodeController extends Controller {
    public function episodes($id) {
        $item = Item::findOrFail($id);
        if ($item->item_type != 2) {
            $notify[] = ['error', 'Something Wrong'];
            return redirect()->route('admin.dashboard')->withNotify($notify);
        }
        $pageTitle = "All Episode of : " . $item->title;
        $episodes  = Episode::with('video')->where('item_id', $item->id)->paginate(getPaginate());
        return view('admin.item.episode.index', compact('pageTitle', 'item', 'episodes'));
    }

    public function addEpisode(Request $request, $id) {
        $request->validate([
            'title'   => 'required',
            'image'   => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'version' => 'required|in:0,1',
        ]);

        $item  = Item::findOrFail($id);
        $image = $this->uploadImage($request);

        $episode          = new Episode();
        $episode->item_id = $item->id;
        $episode->title   = $request->title;
        $episode->image   = $image;
        $episode->version = $request->version;
        $episode->save();

        $notify[] = ['success', 'Episode added successfully'];
        return to_route('admin.item.episode.addVideo', $episode->id)->withNotify($notify);
    }

    public function updateEpisode(Request $request, $id) {
        $request->validate([
            'title'   => 'required',
            'image'   => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'version' => 'required|in:0,1',
        ]);

        $episode = Episode::findOrFail($id);

        $item = $episode->item;
        if (!$item) {
            $notify[] = ['error', 'Item not found'];
            return back()->withNotify($notify);
        }

        $image = $this->uploadImage($request, $episode->image);

        $episode->title   = $request->title;
        $episode->image   = $image;
        $episode->version = $request->version;
        $episode->status  = $request->status ? 1 : 0;
        $episode->save();

        $notify[] = ['success', 'Episode updated successfully'];
        return back()->withNotify($notify);
    }

    private function uploadImage($request, $image = null) {
        if ($request->hasFile('image')) {
            $maxSize = $request->image->getSize() / 3145728;
            if ($maxSize > 3) {
                $notify[0] = ['error', 'Image size could not be greater than 3mb'];
                return back()->withInput($request->all())->withNotify($notify);
            }
            try {
                $date = date('Y') . '/' . date('m') . '/' . date('d');
                $image ? fileManager()->removeFile(getFilePath('episode') . $image) : '';
                $image = $date . '/' . fileUploader($request->image, getFilePath('episode') . $date);
            } catch (\Exception$e) {
                $notify[0] = ['error', 'Image could not be uploaded'];
                return back()->withInput($request->all())->withNotify($notify);
            }
        }

        return $image;
    }

    public function addEpisodeVideo($id) {
        $episode   = Episode::findOrFail($id);
        $pageTitle = "Add Video To : " . $episode->title;
        $video     = $episode->video;
        $prevUrl   = route('admin.item.episodes', $episode->item_id);
        $item      = $episode->item;
        return view('admin.item.video.upload', compact('pageTitle', 'episode', 'video', 'prevUrl', 'item'));
    }

    public function storeEpisodeVideo(Request $request, $id) {
        ini_set('memory_limit', '-1');
        if ($request->video_type == 1) {
            $validation_rule['video'] = ['required_without:link', new FileTypeValidate(['mp4', 'mkv', '3gp'])];
        }
        $validation_rule['video_type'] = 'required|integer';
        $validation_rule['link']       = 'required_without:video';

        $validator = Validator::make($request->all(), $validation_rule);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $episode = Episode::findOrFail($id);
        $video   = $episode->video;

        if ($video) {
            return response()->json(['errors' => 'Already video exist']);
        }

        if ($request->hasFile('video')) {
            $file      = $request->file('video');
            $videoSize = $file->getSize();

            if ($videoSize > 4194304000) {
                return response()->json(['errors' => 'File size must be lower then 4 gb']);
            }

            $videoUploader       = new VideoUploader();
            $videoUploader->file = $file;
            $videoUploader->upload();

            $error = $videoUploader->error;
            if ($error) {
                return response()->json(['errors' => 'Could not upload the Video']);
            }

            $server = $videoUploader->uploadedServer;
            $video  = $videoUploader->fileName;
        } else {
            $video  = $request->link;
            $server = 2;
        }

        $videoObj             = new Video();
        $videoObj->episode_id = $episode->id;
        $videoObj->video_type = $request->video_type;
        $videoObj->content    = $video;
        $videoObj->server     = $server;
        $videoObj->save();

        return response()->json('success');
    }

    public function updateEpisodeVideo($id) {
        $episode = Episode::findOrFail($id);
        $video   = $episode->video;
        if (!$video) {
            $notify[] = ['error', 'Video not found'];
            return back()->withNotify($notify);
        }
        $general   = gs();
        $pageTitle = "Update video of: " . $episode->title;
        $image     = getImage(getFilePath('episode') . $episode->image);
        $videoFile = getVideoFile($video);
        $prevUrl   = route('admin.item.episodes', $episode->item_id);
        return view('admin.item.video.update', compact('pageTitle', 'video', 'image', 'videoFile', 'prevUrl'));
    }

    public function editEpisodeVideo(Request $request, $id) {
        ini_set('memory_limit', '-1');
        if ($request->video_type == 1) {
            $validation_rule['video'] = ['required_without:link', new FileTypeValidate(['mp4', 'mkv', '3gp'])];
        }
        $validation_rule['video_type'] = 'required';
        $validation_rule['link']       = 'required_without:video';
        $validator                     = Validator::make($request->all(), $validation_rule);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $episode = Episode::findOrFail($id);
        $video   = $episode->video;
        if (!$video) {
            return response()->json(['errors' => 'Video not found']);
        }

        $videoUploader            = new VideoUploader();
        $videoUploader->oldServer = $video->server;
        $videoUploader->oldFile   = $video->content;

        if ($request->hasFile('video')) {

            $file      = $request->file('video');
            $videoSize = $file->getSize();

            if ($videoSize > 4194304000) {
                return response()->json(['errors' => 'File size must be lower then 4 gb']);
            }

            $videoUploader->file = $file;
            $videoUploader->upload();

            $error = $videoUploader->error;
            if ($error) {
                return response()->json(['errors' => 'Could not upload the Video']);
            }

            $content = $videoUploader->fileName;
            $server  = $videoUploader->uploadedServer;

        } else {
            $videoUploader->removeOldFile();

            $content = $request->link;
            $server  = 2;
        }

        $video->video_type = $request->video_type;
        $video->content    = $content;
        $video->server     = $server;
        $video->save();

        return response()->json('success');
    }

    public function subtitles($id, $videoId = 0) {
        $item = Episode::with('video')->findOrFail($id);
        if ($videoId == 0) {
            $videoId = $item->video->id;
        }
        $subtitles = Subtitle::where('episode_id', $id)->where('video_id', $videoId)->paginate(getPaginate());
        $pageTitle = 'Subtitles for - ' . $item->title;
        return view('admin.item.video.subtitles', compact('pageTitle', 'item', 'subtitles', 'videoId'));
    }

    public function subtitleStore(Request $request, $itemId, $videoId, $id = 0) {

        $validate = $id ? 'nullable' : 'required';
        $request->validate([
            'language' => 'required|string|max:40',
            'code'     => 'required|string|max:40',
            'file'     => [$validate, new FileTypeValidate(['vtt'])],
        ]);

        if ($id) {
            $subtitle     = Subtitle::findOrFail($id);
            $oldFile      = $subtitle->file;
            $notification = 'Subtitle updated successfully';
        } else {
            $subtitle     = new Subtitle();
            $notification = 'Subtitle created successfully';
            $oldFile      = null;
        }

        $subtitle->item_id  = $itemId;
        $subtitle->video_id = $videoId;
        $subtitle->language = $request->language;
        $subtitle->code     = $request->code;
        if ($request->file) {
            $subtitle->file = fileUploader($request->file, getFilePath('subtitle'), null, $oldFile);
        }
        $subtitle->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
