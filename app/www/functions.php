<?php

function redirect($url, $time)
{
    echo "<script>
                window.setTimeout(function(){
                    window.location.href = '" . $url . "';
                }, " . $time . ");
            </script>";
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("location: index.php");
}

function logincheck()
{
    if (!isset($_SESSION['user_id'])) {
        header("location: index.php");
    }
}

function lists($list, $column)
{
    $columns = [];
    foreach ($list->toArray() as $key => $value) {
        array_push($columns, $value[$column]);
    }

    return $columns;
}

function barColor($pr)
{
    $color = "red";
    if ($pr < 75) {
        $color = "orange";
    }
    if ($pr < 50) {
        $color = "green";
    }
    if ($pr < 25) {
        $color = "green";
    }
    return $color;
}


function checkPid($pid)
{
    exec("ps $pid", $output, $result);
    return count($output) >= 2 ? true : false;
}

function csv_to_array($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if (!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}
function patchnv()
{
    copy('https://raw.githubusercontent.com/keylase/nvidia-patch/master/patch.sh', '/tmp/patch.sh');
    $patchresult = shell_exec('/usr/bin/chmod +x /tmp/patch.sh && /usr/bin/sudo /tmp/patch.sh');
    return $patchresult;
}

function stop_stream($id)
{
    $stream = Stream::find($id);
    $setting = Setting::first();

    if (checkPid($stream->pid)) {
        shell_exec("kill -9 " . $stream->pid);
        shell_exec("/bin/rm -r /opt/streamtool/app/www/" . $setting->hlsfolder . "/" . $stream->id . "*");
    }
    $stream->pid = "0";
    $stream->running = 0;
    $stream->status = 0;
    $stream->duration = 0;

    $stream->save();
}


function getTranscode($id, $streamnumber = null)
{
    $stream = Stream::find($id);
    $setting = Setting::first();
    $trans = $stream->transcode;
    $ffmpeg = $setting->ffmpeg_path;
    $url = $stream->streamurl;
    if ($streamnumber == 2) {
        $url = $stream->streamurl2;
    }
    if ($streamnumber == 3) {
        $url = $stream->streamurl3;
    }
    $endofffmpeg = "";
    $endofffmpeg .= $stream->bitstreamfilter ? ' -bsf h264_mp4toannexb' : '';
    $endofffmpeg .= ' -hls_flags delete_segments -hls_time 4 -hls_list_size 8 -hls_allow_cache 1 -hls_delete_threshold 10 -hls_segment_type mpegts';
    $endofffmpeg .= ' -hls_segment_filename /opt/streamtool/app/www/' . $setting->hlsfolder . '/' . $stream->id . '_%03d.ts  /opt/streamtool/app/www/' . $setting->hlsfolder . '/' . $stream->id . '_.m3u8 ';
    if ($trans) {
        $ffmpeg .= ' -y -thread_queue_size 512 -loglevel error -fflags nobuffer -flags low_delay -fflags +genpts -strict experimental -reconnect 1 -reconnect_streamed 1 -reconnect_delay_max 2 -err_detect ignore_err';
        $ffmpeg .= ' -progress /opt/streamtool/app/www/' . $setting->hlsfolder . '/' . $stream->id . '_.stats';
        $ffmpeg .= ' -probesize ' . ($trans->probesize ? $trans->probesize : '15000000');
        $ffmpeg .= ' -analyzeduration ' . ($trans->analyzeduration ? $trans->analyzeduration : '12000000');
        $ffmpeg .= ' -user_agent "' . ($setting->user_agent ? $setting->user_agent : 'Streamtool') . '"';
        $nvencpos = 0;
        if (strpos($trans->video_codec, 'nvenc')) {
            $ffmpeg .= ' -hwaccel cuvid';
            if (strpos($stream->video_codec_name, '264') !== false) {
                $ffmpeg .= ' -c:v h264_cuvid';
                $nvencpos = 1;
            }
            if (strpos($stream->video_codec_name, 'hevc') !== false) {
                $ffmpeg .= ' -c:v hevc_cuvid';
                $nvencpos = 1;
            }
        }
        $ffmpeg .= ' -i ' . '"' . "$url" . '"';
        $ffmpeg .= ' -strict -2 -dn ';
        $ffmpeg .= $trans->scale ? ' -vf scale=' . ($trans->scale ? $trans->scale : '') : '';
        $ffmpeg .= $trans->audio_codec ? ' -acodec ' . $trans->audio_codec : '';
        $ffmpeg .= $trans->video_codec ? ' -vcodec ' . $trans->video_codec : '';
        $ffmpeg .= $trans->profile ? ' -profile:v ' . $trans->profile : '';
        $ffmpeg .= $trans->preset ? ' -preset ' . $trans->preset_values : '';
        $ffmpeg .= $trans->video_bitrate ? ' -b:v ' . $trans->video_bitrate . 'k' : '';
        $ffmpeg .= $trans->audio_bitrate ? ' -b:a ' . $trans->audio_bitrate . 'k' : '';
        $ffmpeg .= $trans->fps ? ' -r ' . $trans->fps : '';
        $ffmpeg .= $trans->minrate ? ' -minrate ' . $trans->minrate . 'k' : '';
        $ffmpeg .= $trans->maxrate ? ' -maxrate ' . $trans->maxrate . 'k' : '';
        $ffmpeg .= $trans->bufsize ? ' -bufsize ' . $trans->bufsize . 'k' : '';
        $ffmpeg .= $trans->aspect_ratio ? ' -aspect ' . $trans->aspect_ratio : '';
        $ffmpeg .= $trans->audio_sampling_rate ? ' -ar ' . $trans->audio_sampling_rate : '';
        $ffmpeg .= $trans->crf ? ' -crf ' . $trans->crf : '';
        $ffmpeg .= $trans->audio_channel ? ' -ac ' . $trans->audio_channel : '';
        $ffmpeg .= $stream->bitstreamfilter ? ' -bsf h264_mp4toannexb' : '';
        $ffmpeg .= $trans->threads ? ' -threads ' . $trans->threads : '';
        $ffmpeg .= $trans->deinterlance ? ($nvencpos ? ' -vf yadif_cuda' : ' -vf yadif') : '';
        $ffmpeg .= $endofffmpeg;
        file_put_contents('/tmp/streamtool-ffmpeg_' . $id . '.log', $ffmpeg . PHP_EOL , FILE_APPEND);
        return $ffmpeg;
    }

    $ffmpeg .= ' -y -thread_queue_size 512 -loglevel error -fflags nobuffer -flags low_delay -fflags +genpts -strict experimental -reconnect 1 -reconnect_streamed 1  -reconnect_delay_max 2 -err_detect ignore_err';
    $ffmpeg .= ' -user_agent "' . ($setting->user_agent ? $setting->user_agent : 'Streamtool') . '"';
    $ffmpeg .= ' -i "' . $url . '"';
    $ffmpeg .= ' -c:v copy -c:a copy';
    $ffmpeg .= $endofffmpeg;
    return $ffmpeg;
}

function getTranscodedata($id)
{
    $stream = Stream::find($id);
    $trans = Transcode::find($id);
    $setting = Setting::first();
    $ffmpeg = $setting->ffmpeg_path;
    $ffmpeg .= ' -y -thread_queue_size 512 -loglevel error -fflags nobuffer -flags low_delay -fflags +genpts -strict experimental -reconnect 1 -reconnect_streamed 1 -reconnect_delay_max 2 -err_detect ignore_err';
    $ffmpeg .= ' -probesize ' . ($trans->probesize ? $trans->probesize : '15000000');
    $ffmpeg .= ' -analyzeduration ' . ($trans->analyzeduration ? $trans->analyzeduration : '12000000');
    $ffmpeg .= ' -user_agent "' . ($setting->user_agent ? $setting->user_agent : 'Streamtool') . '"';
    $nvencpos = 0;
    if (strpos($trans->video_codec, 'nvenc')) {
        $ffmpeg .= ' -hwaccel cuvid';
        if (strpos($stream->video_codec_name, '264') !== false) {
            $ffmpeg .= ' -c:v h264_cuvid';
            $nvencpos = 1;
        }
        if (strpos($stream->video_codec_name, 'hevc') !== false) {
            $ffmpeg .= ' -c:v hevc_cuvid';
            $nvencpos = 1;
        }
    }
    $ffmpeg .= ' -i ' . '"' . "[input]" . '"';
    $ffmpeg .= ' -strict -2 -dn ';
    $ffmpeg .= $trans->scale ? ' -vf scale=' . ($trans->scale ? $trans->scale : '') : '';
    $ffmpeg .= $trans->audio_codec ? ' -acodec ' . $trans->audio_codec : '';
    $ffmpeg .= $trans->video_codec ? ' -vcodec ' . $trans->video_codec : '';
    $ffmpeg .= $trans->profile ? ' -profile:v ' . $trans->profile : '';
    $ffmpeg .= $trans->preset ? ' -preset ' . $trans->preset_values : '';
    $ffmpeg .= $trans->video_bitrate ? ' -b:v ' . $trans->video_bitrate . 'k' : '';
    $ffmpeg .= $trans->audio_bitrate ? ' -b:a ' . $trans->audio_bitrate . 'k' : '';
    $ffmpeg .= $trans->fps ? ' -r ' . $trans->fps : '';
    $ffmpeg .= $trans->minrate ? ' -minrate ' . $trans->minrate . 'k' : '';
    $ffmpeg .= $trans->maxrate ? ' -maxrate ' . $trans->maxrate . 'k' : '';
    $ffmpeg .= $trans->bufsize ? ' -bufsize ' . $trans->bufsize . 'k' : '';
    $ffmpeg .= $trans->aspect_ratio ? ' -aspect ' . $trans->aspect_ratio : '';
    $ffmpeg .= $trans->audio_sampling_rate ? ' -ar ' . $trans->audio_sampling_rate : '';
    $ffmpeg .= $trans->crf ? ' -crf ' . $trans->crf : '';
    $ffmpeg .= $trans->audio_channel ? ' -ac ' . $trans->audio_channel : '';
    $ffmpeg .= $trans->threads ? ' -threads ' . $trans->threads : '';
    $ffmpeg .= $trans->deinterlance ? ($nvencpos ? ' -vf yadif_cuda' : ' -vf yadif') : '';
    $ffmpeg .= " [OUTPUT]";
    return $ffmpeg;
}


function start_stream($id)
{
    $stream = Stream::find($id);
    $setting = Setting::first();
    $stream->checkable = 0;
    if ($stream->restream) {
        $stream->checker = 0;
        $stream->pid = 0;
        $stream->running = 1;
        $stream->status = 1;
    } else {
        $stream->checker = 0;
        $checkstreamurl = shell_exec('' . $setting->ffprobe_path . ' -analyzeduration 1000000 -probesize 9000000 -i "' . $stream->streamurl . '" -v  quiet -print_format json -show_streams 2>&1');
        $streaminfo = json_decode($checkstreamurl, true);
        if ($streaminfo) {
//            $pid = shell_exec(getTranscode($stream->id));

	     $pid = exec(sprintf("%s > %s 2>&1 & echo $!", getTranscode($stream->id) , "/opt/streamtool/app/www/" . $setting->hlsfolder ."/" . $stream->id ."_.log"));

            $stream->pid = $pid;
            $stream->running = 1;
            $stream->status = 1;
            $video = "";
            $audio = "";
            if (is_array($streaminfo)) {
                foreach ($streaminfo['streams'] as $info) {
                    if ($video == '') {
                        $video = ($info['codec_type'] == 'video' ? $info['codec_name'] : '');
                    }
                    if ($audio == '') {
                        $audio = ($info['codec_type'] == 'audio' ? $info['codec_name'] : '');
                    }
                }
                $stream->video_codec_name = $video;
                $stream->audio_codec_name = $audio;
            }
        } else {
            $stream->running = 1;
            $stream->status = 2;
            if (checkPid($stream->pid)) {
                shell_exec("kill -9 " . $stream->pid);
                shell_exec("/bin/rm -r /opt/streamtool/app/www/" . $setting->hlsfolder . "/" . $stream->id . "*");
            }

            if ($stream->streamurl2) {
                $stream->checker = 2;

                $checkstreamurl = shell_exec('' . $setting->ffprobe_path . ' -analyzeduration 1000000 -probesize 9000000 -i "' . $stream->streamurl . '" -v  quiet -print_format json -show_streams 2>&1');
                $streaminfo = json_decode($checkstreamurl, true);

                if ($streaminfo) {
		     $pid = exec(sprintf("%s > %s 2>&1 & echo $!", getTranscode($stream->id, 2) , "/opt/streamtool/app/www/" . $setting->hlsfolder ."/" . $stream->id ."_.log"));
//                    $pid = shell_exec(getTranscode($stream->id, 2));
                    $stream->pid = $pid;
                    $stream->running = 1;
                    $stream->status = 1;
                    $video = "";
                    $audio = "";
                    if (is_array($streaminfo)) {
                        foreach ($streaminfo['streams'] as $info) {
                            if ($video == '') {
                                $video = ($info['codec_type'] == 'video' ? $info['codec_name'] : '');
                            }
                            if ($audio == '') {
                                $audio = ($info['codec_type'] == 'audio' ? $info['codec_name'] : '');
                            }
                        }
                        $stream->video_codec_name = $video;
                        $stream->audio_codec_name = $audio;
                    }
                } else {
                    $stream->running = 1;
                    $stream->status = 2;
                    if (checkPid($stream->pid)) {
                        shell_exec("kill -9 " . $stream->pid);
                        shell_exec("/bin/rm -r /opt/streamtool/app/www/" . $setting->hlsfolder . "/" . $stream->id . "*");
                    }
                    if ($stream->streamurl3) {
                        $stream->checker = 3;
                        $checkstreamurl = shell_exec('' . $setting->ffprobe_path . ' -analyzeduration 1000000 -probesize 9000000 -i "' . $stream->streamurl . '" -v  quiet -print_format json -show_streams 2>&1');
                        $streaminfo = json_decode($checkstreamurl, true);
                        if ($streaminfo) {
                //            $pid = shell_exec(getTranscode($stream->id, 3));
			     $pid = exec(sprintf("%s > %s 2>&1 & echo $!", getTranscode($stream->id, 3) , "/opt/streamtool/app/www/" . $setting->hlsfolder ."/" . $stream->id ."_.log"));

                            $stream->pid = $pid;
                            $stream->running = 1;
                            $stream->status = 1;

                            $video = "";
                            $audio = "";

                            if (is_array($streaminfo)) {
                                foreach ($streaminfo['streams'] as $info) {
                                    if ($video == '') {
                                        $video = ($info['codec_type'] == 'video' ? $info['codec_name'] : '');
                                    }
                                    if ($audio == '') {
                                        $audio = ($info['codec_type'] == 'audio' ? $info['codec_name'] : '');
                                    }
                                }
                                $stream->video_codec_name = $video;
                                $stream->audio_codec_name = $audio;
                            }
                        } else {
                            $stream->running = 1;
                            $stream->status = 2;
                            $stream->pid = 0;
                        }
                    }
                }
            }
        }
    }
    $stream->checkable = 1;
    $stream->save();
}


function generateNginxConfPort($port)
{
    ob_start();
    echo 'user  streamtool;
worker_processes  auto;
worker_rlimit_nofile 655350;

events {
    worker_connections  65535;
    use epoll;
        accept_mutex on;
        multi_accept on;
}

http {
        include                   mime.types;
        default_type              application/octet-stream;
        sendfile                  on;
        tcp_nopush                on;
        tcp_nodelay               on;
        reset_timedout_connection on;
        gzip                      off;
        fastcgi_read_timeout      200;
        access_log                off;
        keepalive_timeout         10;
        client_max_body_size      999m;
        send_timeout              120s;
        sendfile_max_chunk        512k;
        lingering_close           off;
	server {
		listen ' . $port . ';
		root /opt/streamtool/app/wws/;
		server_tokens off;
		chunked_transfer_encoding off;
        rewrite ^/live/(.*)/(.*)/(.*)$ /stream.php?username=$1&password=$2&stream=$3 break;
        rewrite ^/mpegts/(.*)/(.*)/(.*)$ /mpegts.php?username=$1&password=$2&stream=$3 break;
		location ~ \.php$ {
            try_files $uri =404;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_buffering on;
            fastcgi_buffers 96 32k;
            fastcgi_buffer_size 32k;
            fastcgi_max_temp_file_size 0;
            fastcgi_keep_conn on;
            fastcgi_param SCRIPT_FILENAME /opt/streamtool/app/wws/$fastcgi_script_name;
            fastcgi_param SCRIPT_NAME $fastcgi_script_name;
            fastcgi_pass unix:/opt/streamtool/app/php/var/run/stream.sock;
		}	
	}
	server {
		listen 9001;
		root /opt/streamtool/app/www/;
                index index.php index.html index.htm;
                server_tokens off;
                chunked_transfer_encoding off;
		location ~ \.php$ {
                        try_files $uri =404;
                        fastcgi_index index.php;
                        include fastcgi_params;
                        fastcgi_buffering on;
                        fastcgi_buffers 96 32k;
                        fastcgi_buffer_size 32k;
                        fastcgi_max_temp_file_size 0;
                        fastcgi_keep_conn on;
                        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
                        fastcgi_pass 127.0.0.1:9002;
		}
	}
}';
    $file = '/opt/streamtool/app/nginx/conf/nginx.conf';
    $current = ob_get_clean();
    file_put_contents($file, $current);
}
