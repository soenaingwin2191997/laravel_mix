<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {

    protected $casts = ['seconds' => 'object', 'ads_time' => 'object', 'subtitles' => 'object'];

    public function episode() {
        return $this->belongsTo(Episode::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function subtitles() {
        return $this->hasMany(Subtitle::class, 'video_id');
    }

    public function getAds() {
        $adsTime = [];
        if ($this->seconds) {
            $duration = $this->seconds;
            $videoAds = VideoAdvertise::get();
            for ($i = 0; $i < count($duration); $i++) {
                $videoAd = $videoAds->shuffle()->first();
                if ($videoAd) {
                    if (@$videoAd->server == 1) {
                        $general = gs();
                        $video   = $general->ftp->domain . '/' . $videoAd->content->video;
                    } elseif (@$videoAd->server == 3) {
                        $general = gs();
                        $video   = @$general->wasabi->endpoint . '/' . @$general->wasabi->bucket . '/' . @$videoAd->content->video;
                    } else {
                        $video = getImage(getFilePath('ads') . '/' . @$videoAd->content->video);
                    }
                    $adsTime[$duration[$i]] = @$videoAd->content->link ? @$videoAd->content->link : $video;
                }
            }
        }
        return $adsTime;
    }
}
