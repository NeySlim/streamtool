<?php
if (isset($_SERVER['SERVER_ADDR'])) {
    if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
        die('access is not permitted');
    }
}
include('config.php');

while (TRUE) {
    $setting = Setting::first();
    !$setting->enableCheck  ? exit(0) : '';
    foreach (Stream::where('pid', '!=', 0)->where('running', '=', 1)->where('checkable', '=', 1)->get() as $stream) {
        if (checkPid($stream->pid)) {
            $stream->checker = 0;
            $checkstreamurl = shell_exec('/usr/bin/timeout 3s ' . $setting->ffprobe_path . ' -analyzeduration 1000000 -probesize 9000000 -i "/' . $setting->hlsfolder . '/' . $stream->id . '"_.m3u8" -v  quiet -print_format json -show_streams 2>&1');
            $streaminfo = json_decode($checkstreamurl, true);
            if (count($streaminfo) > 0) {
                $video = "";
                $audio = "";
                $duration = 0;
                if (is_array($streaminfo)) {
                    foreach ($streaminfo['streams'] as $info) {
                        if ($video == '') {
                            $video = ($info['codec_type'] == 'video' ? $info['codec_name'] : '');
                        }
                        if ($audio == '') {
                            $audio = ($info['codec_type'] == 'audio' ? $info['codec_name'] : '');
                        }
                        if ($duration == '') {
                            $duration = ($info['index'] == '0' ? $info['start_time'] : 0 );
                        }
                    }
                    $stream->video_codec_name = $video;
                    $stream->audio_codec_name = $audio;
                    $stream->duration = round($duration, 0);
                }
            }
            $stream->save();
        }
    }
}
