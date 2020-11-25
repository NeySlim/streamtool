<?php
include('config.php');

$db = $capsule;
if (isset($_GET['install'])) {

    $arraynamesexist = [];
    $tables = $capsule::select('SHOW TABLES');
    foreach ($tables as $key => $val) {

        $tableName = (array)$val;
        $tableName = array_shift($tableName);

        array_push($arraynamesexist, $tableName);
    }

    if ($_GET['install'] == 'fresh') {
        $db::schema()->dropIfExists('admins');
        $db::schema()->dropIfExists('categories');
        $db::schema()->dropIfExists('category_user');
        $db::schema()->dropIfExists('settings');
        $db::schema()->dropIfExists('streams');
        $db::schema()->dropIfExists('users');
        $db::schema()->dropIfExists('transcodes');
        $db::schema()->dropIfExists('activity');
        $db::schema()->dropIfExists('blocked_ips');
        $db::schema()->dropIfExists('blocked_user_agents');
        $arraynamesexist = [];
    }

    if (!in_array('admins', $arraynamesexist)) {

        $db->schema()->create('admins', function ($table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $admin = new Admin;
        $admin->username = 'admin';
        $admin->password = md5('admin');
        $admin->save();

        echo "created admin table <br>" . PHP_EOL;
        echo "admin created: username: admin  and password: admin <br>" . PHP_EOL;
    }


    if (!in_array('categories', $arraynamesexist)) {

        $db->schema()->create('categories', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        echo "created categories table <br>" . PHP_EOL;


        $category1 = new Category();
        $category1->name = 'Default';
        $category1->save();
        echo "created default stream categorybr>" . PHP_EOL;
    }
 

    if (!in_array('category_user', $arraynamesexist)) {

        $db->schema()->create('category_user', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->timestamps();
        });

        echo "created category_user table <br>" . PHP_EOL;
    }


    if (!in_array('settings', $arraynamesexist)) {

        $db->schema()->create('settings', function ($table) {
            $table->increments('id');
            $table->string('ffmpeg_path')->default('/opt/streamtool/app/bin/ffmpeg-streamtool');
            $table->string('ffprobe_path')->default('/opt/streamtool/app/bin/ffprobe-streamtool');
            $table->string('ffmpeg_nvcc_path')->default('/opt/streamtool/app/bin/ffmpeg-streamtool');
            $table->string('webport')->default('8000');
            $table->string('webip');
            $table->string('server_name')->default('Streamtool');
            $table->string('logourl')->default('http://example.com/logo/');
            $table->string('hlsfolder')->default('/hls');
            $table->string('user_agent')->default('Streamtool/ST-20.11');
            $table->tinyInteger('enableCheck')->default(1);
            $table->timestamps();
        });

        echo "created settings table <br>" . PHP_EOL;
    }


    if (!in_array('streams', $arraynamesexist)) {

        $db->schema()->create('streams', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('streamurl')->default('http://');
            $table->string('streamurl2')->default('');
            $table->string('streamurl3')->default('');
            $table->tinyInteger('running')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('cat_id');
            $table->integer('trans_id');
            $table->integer('pid')->default('0');
            $table->tinyInteger('restream')->default(0);
            $table->string('video_codec_name')->default('N/A');
            $table->string('audio_codec_name')->default('N/A');
            $table->BigInteger('duration')->default(0);
            $table->tinyInteger('bitstreamfilter')->default(0);
            $table->tinyInteger('checker')->default(0);
            $table->tinyInteger('checkable')->default(0);
            $table->string('logo')->default('');
            $table->string('tvid')->default('');
            $table->timestamps();
        });
        echo "created streams table <br>" . PHP_EOL;
    }

    if (!in_array('users', $arraynamesexist)) {

        $db->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->tinyInteger('active')->default('1');
            $table->string('lastconnected_ip')->default('');
            $table->date('exp_date')->default('0000-00-00 00:00:00');
            $table->integer('last_stream')->default('0');
            $table->string('useragent')->default('');
            $table->integer('max_connections')->default('1');
            $table->timestamps();
        });

        echo "created users table <br>" . PHP_EOL;

    }


    if (!in_array('transcodes', $arraynamesexist)) {

        $db->schema()->create('transcodes', function ($table) {
            $table->increments('id');
            $table->string('name')->unique();

            $table->BigInteger('probesize')->default('10000000');
            $table->BigInteger('analyzeduration')->default('10000000');
            $table->string('video_codec')->default('copy');
            $table->string('audio_codec')->default('copy');
            $table->string('profile')->default('');
            $table->string('preset_values')->default('');
            $table->string('scale')->default('');
            $table->string('aspect_ratio')->default('');
            $table->BigInteger('video_bitrate')->default('0');
            $table->integer('audio_channel')->default('0');
            $table->BigInteger('audio_bitrate')->default('0');
            $table->integer('fps')->default('0');
            $table->integer('minrate')->default('0');
            $table->integer('maxrate')->default('0');
            $table->integer('bufsize')->default('0');
            $table->integer('audio_sampling_rate')->default('0');
            $table->integer('crf')->default('0');
            $table->integer('threads')->default('0');
            $table->tinyInteger('deinterlance')->default(0);
            $table->timestamps();
        });

        echo "created transcodes table <br>" . PHP_EOL;
        
        $profile1 = new Transcode();
        $profile1->name = 'Default: HLS remux';
        $profile1->probesize = 10000000;
        $profile1->analyzeduration = 10000000;
        $profile1->video_codec = 'copy';
        $profile1->audio_codec = 'copy';
        $profile1->save();
        echo "created default hls copy profile<br>" . PHP_EOL;
        
        $profile2 = new Transcode();
        $profile2->name = 'AAC: Video Copy, AAC Audio';
        $profile2->probesize = 10000000;
        $profile2->analyzeduration = 10000000;
        $profile2->video_codec = 'copy';
        $profile2->audio_codec = 'aac';
        $profile2->save();
        echo "created AAC transcode profile<br>" . PHP_EOL;

        $profile3 = new Transcode();
        $profile3->name = 'NVENC H264 - AAC - 4Mb/s';
        $profile3->probesize = 10000000;
        $profile3->analyzeduration = 10000000;
        $profile3->video_codec = 'h264_nvenc';
        $profile3->audio_codec = 'aac';
        $profile3->profile = 'high -level 4.2';
        $profile3->preset_values = 'llhq';
        $profile3->video_bitrate = '4000';
        $profile3->save();
        echo "created h264_nvenc profile<br>" . PHP_EOL;

        $profile4 = new Transcode();
        $profile4->name = 'CPU H264 - AAC - 4Mb/s';
        $profile4->probesize = 10000000;
        $profile4->analyzeduration = 10000000;
        $profile4->video_codec = 'h264';
        $profile4->audio_codec = 'copy';
        $profile4->profile = 'main -level 4.0';
        $profile4->preset_values = 'veryfast';
        $profile4->video_bitrate = '4000';
        $profile4->save();
        echo "created H264 CPU profile<br>" . PHP_EOL;

        $profile5 = new Transcode();
        $profile5->name = 'NVENC HEVC - AAC - 2.8Mb/s';
        $profile5->probesize = 10000000;
        $profile5->analyzeduration = 10000000;
        $profile5->video_codec = 'h264_nvenc';
        $profile5->audio_codec = 'aac';
        $profile5->profile = 'high -level 4.1';
        $profile5->preset_values = 'llhq';
        $profile5->video_bitrate = '2800';
        $profile5->save();
        echo "created hevc_nvenc profile<br>" . PHP_EOL;
    }


    if (!in_array('activity', $arraynamesexist)) {

        $db->schema()->create('activity', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('stream_id');
            $table->text('user_agent');
            $table->string('user_ip');
            $table->integer('pid')->default('0');
            $table->integer('bandwidth')->default('0');
            $table->dateTime('date_start');
            $table->dateTime('date_end')->default('0000-00-00');
            $table->index('user_id');
            $table->index('stream_id');
            $table->index('date_end');
            $table->index('pid');
            $table->timestamps();
        });

        echo "created activity table <br>" . PHP_EOL;
    }


    if (!in_array('blocked_ips', $arraynamesexist)) {

        $db->schema()->create('blocked_ips', function ($table) {
            $table->increments('id');
            $table->string('ip')->unique();
            $table->text('description');
            $table->timestamps();
        });

        echo "created blocked_ips table <br>" . PHP_EOL;
    }

    if (!in_array('blocked_user_agents', $arraynamesexist)) {

        $db->schema()->create('blocked_user_agents', function ($table) {
            $table->increments('id');
            $table->string('name', 255)->unique();
            $table->text('description');
            $table->timestamps();
        });

        echo "created blocked_user_agents table <br>" . PHP_EOL;
    }


}

if (isset($_GET['update'])) {

    $db->schema()->table('streams', function ($table) use ($db) {
        $db->schema()->hasColumn('streams', 'duration') ? '' : $table->BigInteger('duration')->default(0);
        
    });
    $db->schema()->table('settings', function ($table) use ($db) {
        $db->schema()->hasColumn('settings', 'server_name') ? '' : $table->string('server_name')->default('Streamtool');
    });
    echo "update <br>" . PHP_EOL;
}
