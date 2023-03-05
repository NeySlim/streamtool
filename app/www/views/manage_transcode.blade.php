@extends('main')
@section('content')
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Manage transcode profiles</h2>

                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    @if(count($categories) > 0)

                    @if($message)
                    <div class="alert alert-{{ $message['type'] }}">
                        {{ $message['message'] }}
                    </div>
                    @endif
                    <br>
                    <form id="demo-form2" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" role="form" action="" method="post">



                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Profile name <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" name="profilename" class="form-control col-md-7 col-xs-12" placeholder="new_profile" value="{{  isset($_POST['name']) ?  $_POST['name'] : $transcode->name }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Analyzeduration <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="probesize" class="form-control col-md-7 col-xs-12" placeholder="10000000" value="{{  isset($_POST['probesize']) ?  $_POST['probesize'] : ( $transcode->probesize ? $transcode->probesize : "10000000" ) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Probesize <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="analyzeduration" class="form-control col-md-7 col-xs-12" placeholder="10000000" value="{{  isset($_POST['analyzeduration']) ?  $_POST['analyzeduration'] : ($transcode->analyzeduration ? $transcode->analyzeduration : "10000000") }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Video Encoder</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="video_codec" id="video_codec" class="form-control">
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio codec</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="audio_codec" class="form-control" id="audio_codec">
                                    <option value="" {{ isset($_POST['audio_codec']) ?  $_POST['audio_codec']  == '' : ( $transcode->audio_codec  == '' ? "selected" : "") }}>Disable</option>
                                    <option value="aac" {{ isset($_POST['audio_codec']) ?  $_POST['audio_codec']  == 'aac' :( $transcode->audio_codec  == 'aac' ? "selected" : "") }}>AAC</option>
                                    <option value="libfdk_aac" {{ isset($_POST['audio_codec']) ?  $_POST['audio_codec']  == 'libfdk_aac' :( $transcode->audio_codec  == 'libfdk_aac' ? "selected" : "" )}}>FDK AAC</option>
                                    <option value="copy" {{ isset($_POST['audio_codec']) ?  $_POST['audio_codec']  == 'copy' :( $transcode->audio_codec  == 'copy' ? "selected" : "" )}}>Copy</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Profile</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="profile" id="profile_values" class="form-control">
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Preset</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="preset_values" class="form-control" id="preset_values">
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Scalling</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="scalling" class="form-control">
                                    <option value="" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '' : ($transcode->scale  == '' ? "selected" : "") }}>Disable</option>
                                    <option value="1920:1080" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '1920:1080' :( $transcode->scale  == '1920:1080' ? "selected" : "") }}>1920x1080</option>
                                    <option value="1680:1056" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '1680:1056' :( $transcode->scale  == '1680:1056' ? "selected" : "") }}>1680x1056</option>
                                    <option value="1280:720" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '1280:720' :( $transcode->scale  == '1280:720' ? "selected" : "") }}>1280x720</option>
                                    <option value="1024:576" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '1024:576' :( $transcode->scale  == '1024:576' ? "selected" : "") }}>1024x576</option>
                                    <option value="960:540" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '960:540' :( $transcode->scale  == '960:540' ? "selected" : "") }}>960x540</option>
                                    <option value="850:480" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '850:480' :( $transcode->scale  == '850:480' ? "selected" : "") }}>850x480</option>
                                    <option value="720:576" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '720:576' :( $transcode->scale  == '720:576' ? "selected" : "") }}>720x576</option>
                                    <option value="720:540" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '720:540' :( $transcode->scale  == '720:540' ? "selected" : "") }}>720x540</option>
                                    <option value="720:80" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '720:80' : ($transcode->scale  == '720:80' ? "selected" : "" )}}>720x480</option>
                                    <option value="720:404" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '720:404' :( $transcode->scale  == '720:404' ? "selected" : "" )}}>720x404</option>
                                    <option value="704:576" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '704:576' :($transcode->scale  == '704:576' ? "selected" : "" )}}>704x576</option>
                                    <option value="640:480" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '640:480' : ($transcode->scale  == '640:480' ? "selected" : "" )}}>640x480</option>
                                    <option value="640:360" {{ isset($_POST['scalling']) ?  $_POST['scalling']  == '640:360' : ($transcode->scale  == '640:360' ? "selected" : "" )}}>640x360</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Aspect ratio</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="aspect_ratio" class="form-control">
                                    <option value="" {{ isset($_POST['aspect_ratio']) ?  $_POST['aspect_ratio']  == '' :( $transcode->aspect_ratio  == '' ? "selected" : "") }}>Disable</option>
                                    <option value="16:9" {{ isset($_POST['aspect_ratio']) ?  $_POST['aspect_ratio']  == '16:9' : ($transcode->aspect_ratio  == '16:9' ? "selected" : "") }}>16:9</option>
                                    <option value="4:3" {{ isset($_POST['aspect_ratio']) ?  $_POST['aspect_ratio']  == '4:3' : ($transcode->aspect_ratio  == '4:3' ? "selected" : "") }}>4:3</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Video bitrate <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="video_bitrate" class="form-control col-md-7 col-xs-12" placeholder="1500" required value="{{  isset($_POST['video_bitrate']) ?  $_POST['video_bitrate'] : ( empty($transcode->video_bitrate) ? 0 : $transcode->video_bitrate  ) }}">
                                <span class="help-inline">Kilobytes, 0 is disabled</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio channel</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="audio_channel" class="form-control">
                                    <option value="0" {{ isset($_POST['audio_channel']) ?  $_POST['audio_channel']  == '0' : ($transcode->audio_channel  == '0' ? "selected" : "") }}>Disable</option>
                                    <option value="1" {{ isset($_POST['audio_channel']) ?  $_POST['audio_channel']  == '1' : ($transcode->audio_channel  == '1' ? "selected" : "") }}>Mono</option>
                                    <option value="2" {{ isset($_POST['audio_channel']) ?  $_POST['audio_channel']  == '2' : ($transcode->audio_channel  == '2' ? "selected" : "") }}>Stereo</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio bitrate</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="audio_bitrate" class="form-control">
                                    <option value="0" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '0' : ($transcode->audio_bitrate  == '0' ? "selected" : "") }}>Disable</option>
                                    <option value="32" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '32' : ($transcode->audio_bitrate  == '32' ? "selected" : "") }}>32k</option>
                                    <option value="48" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '48' : ($transcode->audio_bitrate  == '48' ? "selected" : "") }}>48k</option>
                                    <option value="64" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '64' : ($transcode->audio_bitrate  == '64' ? "selected" : "") }}>64k</option>
                                    <option value="96" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '96' : ($transcode->audio_bitrate  == '96' ? "selected" : "") }}>96k</option>
                                    <option value="128" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '128' : ($transcode->audio_bitrate  == '128' ? "selected" : "") }}>128k</option>
                                    <option value="160" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '160' : ($transcode->audio_bitrate  == '160' ? "selected" : "") }}>160k</option>
                                    <option value="192" {{ isset($_POST['audio_bitrate']) ?  $_POST['audio_bitrate']  == '192' : ($transcode->audio_bitrate  == '192' ? "selected" : "") }}>192k</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">FPS <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="fps" class="form-control col-md-7 col-xs-12" placeholder="25" value="{{  isset($_POST['fps']) ?  $_POST['fps'] : ( empty($transcode->fps) ? 0 : $transcode->fps ) }}">
                                0 is disabled
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">minrate <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="minrate" class="form-control col-md-7 col-xs-12" placeholder="200" value="{{  isset($_POST['minrate']) ?  $_POST['minrate'] : ( empty($transcode->minrate) ? 0 : $transcode->minrate ) }}">
                                0 is disabled
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">maxrate <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="maxrate" class="form-control col-md-7 col-xs-12" placeholder="2000" value="{{  isset($_POST['maxrate']) ?  $_POST['maxrate'] : ( empty($transcode->maxrate) ? 0 : $transcode->maxrate ) }}">
                                0 is disabled
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">bufsize <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="bufsize" class="form-control col-md-7 col-xs-12" placeholder="1200" value="{{  isset($_POST['bufsize']) ?  $_POST['bufsize'] : ( empty($transcode->bufsize) ? 0 : $transcode->bufsize ) }}">
                                0 is disabled
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio sampling rate</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="audio_sampling_rate" class="form-control">
                                    <option value="0">Disable</option>
                                    <option value="32000" {{ isset($_POST['audio_sampling_rate']) ?  $_POST['audio_sampling_rate']  == '32000' : ($transcode->audio_sampling_rate  == '32000' ? "selected" : "") }}>32000</option>
                                    <option value="44100" {{ isset($_POST['audio_sampling_rate']) ?  $_POST['audio_sampling_rate']  == '44100' : ($transcode->audio_sampling_rate  == '44100' ? "selected" : "") }}>44100</option>
                                    <option value="48000" {{ isset($_POST['audio_sampling_rate']) ?  $_POST['audio_sampling_rate']  == '48000' : ($transcode->audio_sampling_rate  == '48000' ? "selected" : "") }}>48000 </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">crf <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="crf" class="form-control col-md-7 col-xs-12" placeholder="23" value="{{  isset($_POST['crf']) ?  $_POST['crf'] : ( empty($transcode->crf) ? 0 : $transcode->crf ) }}">
                                0 is disabled
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">threads <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" name="threads" class="form-control col-md-7 col-xs-12" placeholder="0" value="{{  isset($_POST['threads']) ?  $_POST['threads'] : ( empty($transcode->threads) ? 0 : empty($transcode->threads ))  }}">
                                0 is disabled
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Deinterlance</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <p style="padding: 5px;"><span><input type="checkbox" class="flat" name="deinterlance" id="" value="1" {{ $transcode->deinterlance ? "checked" : ""}}></span></p>

                            </div>
                        </div>


                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" name="submit" class="btn btn-success btn-sm">Submit</button>
                            </div>
                        </div>

                    </form>
                    @else
                    <div class="alert alert-error">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Error!</strong> You need to create an category!
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script type="text/javascript" src="js/moment.min2.js"></script>
    <script type="text/javascript" src="js/datepicker/daterangepicker.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#expdate').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_4"
            }, function(start, end, label) {});
            $(".select2_multiple").select2({
                placeholder: "Select categories",
                allowClear: true
            });
        });
    </script>
    <script type="text/javascript">
        function updatePresetValuesSelect() {
            var codec_id = document.getElementById("video_codec").value;

            var preset_values_select = document.getElementById("preset_values");
            preset_values_select.innerHTML = null;

            var presetValues = codecs[codec_id].presetValues;
            var selectedIndex = 0;
            for (var presetValue in presetValues) {
                var option = document.createElement("option");
                option.text = presetValues[presetValue].label;
                option.value = presetValues[presetValue].id;
                preset_values_select.appendChild(option);

                if (presetValues[presetValue].id == preset_default_value) {
                    preset_values_select.selectedIndex = selectedIndex;
                }
                selectedIndex++;
            }
        }

        function updateProfileValuesSelect() {
            var codec_id = document.getElementById("video_codec").value;

            var profile_values_select = document.getElementById("profile_values");
            profile_values_select.innerHTML = null;

            var profileValues = codecs[codec_id].profileValues;
            var selectedIndex = 0;
            for (var profileValue in profileValues) {
                var option = document.createElement("option");
                option.text = profileValues[profileValue].label;
                option.value = profileValues[profileValue].id;
                profile_values_select.appendChild(option);

                if (profileValues[profileValue].id == profile_default_value) {
                    profile_values_select.selectedIndex = selectedIndex;
                }
                selectedIndex++;
            }
        }

        function updateVideoCodecSelect() {
            var selectedIndex = 0;
            var codecs_select = document.getElementById("video_codec");
            for (var codec in codecs) {
                var option = document.createElement("option");
                option.text = codecs[codec].label;
                option.value = codec;
                codecs_select.appendChild(option);

                if (codec == codec_default_value) {
                    codecs_select.selectedIndex = selectedIndex;
                }
                selectedIndex++;
            }
        }
        var presetValues1 = [{
            id: "",
            label: "Disable"
        }];
        var profileValues1 = [{
            id: "",
            label: "Disable"
        }];


        var presetValues2 = [{
                id: "ultrafast",
                label: "Ultrafast"
            },
            {
                id: "superfast",
                label: "Superfast"
            },
            {
                id: "veryfast",
                label: "Veryfast"
            },
            {
                id: "faster",
                label: "Faster"
            },
            {
                id: "fast",
                label: "Fast"
            },
            {
                id: "medium",
                label: "Medium"
            },
            {
                id: "slow",
                label: "Slow"
            },
            {
                id: "slower",
                label: "Slower"
            },
            {
                id: "veryslow",
                label: "Veryslow"
            }
        ];

        var profileValues2 = [{
                id: "baseline -level 3.0",
                label: "baseline -level 3.0"
            },
            {
                id: "main -level 4.0",
                label: "main -level 4.0"
            },
            {
                id: "high -level 4.2",
                label: "high -level 4.2"
            }
        ];

        var presetValues3 = [{
                id: "default",
                label: "default"
            },
            {
                id: "slow",
                label: "slow"
            },
            {
                id: "medium",
                label: "medium"
            },
            {
                id: "fast",
                label: "fast"
            },
            {
                id: "hp",
                label: "hp"
            },
            {
                id: "hq",
                label: "hq"
            },
            {
                id: "ll",
                label: "ll"
            },
            {
                id: "llhq",
                label: "llhq"
            },
            {
                id: "llhp",
                label: "llhp"
            }
        ];
        var presetValues4 = [{
                id: "default",
                label: "default"
            },
            {
                id: "slow",
                label: "slow"
            },
            {
                id: "medium",
                label: "medium"
            },
            {
                id: "fast",
                label: "fast"
            },
            {
                id: "hp",
                label: "hp"
            },
            {
                id: "hq",
                label: "hq"
            },
            {
                id: "ll",
                label: "ll"
            },
            {
                id: "llhq",
                label: "llhq"
            },
            {
                id: "llhp",
                label: "llhp"
            }
        ];
		var presetValues5 = [{
                id: "",
                label: "-"
            }
        ];
		var presetValues6 = [{
                id: "",
                label: "-"
            }
        ];
        var profileValues3 = [{
                id: "baseline -level 3.0",
                label: "baseline -level 3.0"
            },
            {
                id: "main -level 4.0",
                label: "main -level 4.0"
            },
            {
                id: "high -level 4.2",
                label: "high -level 4.2"
            }
        ];
        var profileValues4 = [{
                id: "main -level 4.1",
                label: "main -level 4.1"
            },
            {
                id: "main10 -level auto",
                label: "main10 -level auto"
            },
            {
                id: "rext -level auto",
                label: "rext -level auto"
            }
        ];
		var profileValues5 = [{
                id: "constrained_baseline -level 3.0",
                label: "constrained_baseline -level 3.0"
            },
            {
                id: "main -level 4.0",
                label: "main -level 4.0"
            },
            {
                id: "high -level 4.2",
                label: "high -level 4.2"
            }
        ];
		var profileValues6 = [{
                id: "main -level 4.1",
                label: "main -level 4.1"
            },
            {
                id: "main10 -level 5",
                label: "main10 -level 5"
            },
            {
                id: "rext -level 5",
                label: "rext -level 5"
            }
        ];

        var codecs = {
            "": {
                label: "Disable",
                presetValues: presetValues1,
                profileValues: profileValues1
            },
            "copy": {
                label: "Copy",
                presetValues: presetValues1,
                profileValues: profileValues1
            },
            "h264": {
                label: "H264",
                presetValues: presetValues2,
                profileValues: profileValues2
            },
            "libx265": {
                label: "HEVC",
                presetValues: presetValues2,
                profileValues: profileValues2
            },
            "h264_nvenc": {
                label: "Nvidia H264 nvenc",
                presetValues: presetValues3,
                profileValues: profileValues3
            },
            "hevc_nvenc": {
                label: "Nvidia HEVC nvenc",
                presetValues: presetValues4,
                profileValues: profileValues4
            },
			"h264_vaapi": {
                label: "VAAPI H264",
                presetValues: presetValues5,
                profileValues: profileValues5
            },
			"hevc_vaapi": {
                label: "VAAPI HEVC",
                presetValues: presetValues6,
                profileValues: profileValues6
            },
        };
        var codec_default_value = "{{  isset($_POST['video_codec']) ?  $_POST['video_codec']  : ( empty($transcode->video_codec) ? 'copy' : $transcode->video_codec ) }}";
        var preset_default_value = "{{ isset($_POST['preset_values']) ?  $_POST['preset_values'] : ( empty($transcode->preset_values) ? 'veryfast' : $transcode->preset_values) }}"; 
        var profile_default_value = "{{ isset($_POST['profile']) ?  $_POST['profile'] : ( empty($transcode->profile) ? '' : $transcode->profile ) }}"; 
        $(document).ready(function() {

            $("#video_codec").on("change", function() {
                updatePresetValuesSelect();
                updateProfileValuesSelect();
            });



            updateVideoCodecSelect();
            updatePresetValuesSelect();
            updateProfileValuesSelect();
        });
    </script>
    @endsection