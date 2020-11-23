@extends('main')
@section('content')

<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>{{ $title }} </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <a class="btn btn-round btn-primary btn-sm" href="manage_stream.php" title="Add">
                            Add stream
                        </a>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <form action="" method="post">
                    <button type="submit" name="mass_start" value="Mass start" class="btn btn-sm btn-success" onclick="return confirm('Mass start ?')"><i class="far fa-play-circle"></i> MASS START</button>
                    <button type="submit" name="mass_stop" value="Mass stop" class="btn btn-sm btn-danger" onclick="return confirm('Mass stop ?')"><i class="far fa-stop-circle"></i> MASS STOP</button>
                    <button type="submit" name="mass_delete" value="Mass delete" class="btn btn-sm btn-danger" onclick="return confirm('Mass delete ?')"><i class="far fa-times-circle"></i> MASS DELETE</button>
                    @if($cronStatus == 1)
                    <button style="float: right;" type="submit" name="stop_cron" value="Stop stream watcher" class="btn btn-sm btn-warning"><i class="fas fa-hand-paper"></i> Stop stream watcher</button>
                    @else
                    <button style="float: right;" type="submit" name="start_cron" value="Start stream watcher" class="btn btn-sm btn-success"><i class="fas fa-play"></i>| Start stream watcher</button>
                    @endif
                    @if(count($streams) > 0)
                    @if($message)
                    <div class="alert alert-{{ $message['type'] }}">
                        {{ $message['message'] }}
                    </div>
                    @endif
                    <div class="">
                        <table id="example" class="table table-striped responsive-utilities jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th>
                                        <input type="checkbox" id="check-all" class="flat">
                                    </th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Input Codecs</th>
                                    <th>Output Encoders</th>
                                    <th class=" no-link last"><span class="nobr">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($streams as $key => $stream)
                                <tr>
                                    <td class="center"><input type="checkbox" class="tableflat check" value="{{ $stream->id }}" name="mselect[]"></td>
                                    <td style="font-family: system-ui; font-weight: bold;">
                                        {{ strtoupper($stream->name) }}
                                    </td>
                                    <td class="center"><span class="label label-{{ $stream->status_label['label'] }}"><i class="{{ $stream->status_label['icon'] }}"></i> {{ $stream->status_label["text"] }}</span>
                                        @if($stream->checker == 0)
                                        <span class="label label-info"><i class="fas fa-check-circle"></i> Primary URL</span>
                                        @endif
                                        @if($stream->checker == 2)
                                        <span class="label label-info"><i class="fas fa-exclamation-circle"></i> >Backup URL 1</span>
                                        @endif
                                        @if($stream->checker == 3)
                                        <span class="label label-info"><i class="fas fa-exclamation-circle"></i> Backup URL 2</span>
                                        @endif
                                    </td>
                                    <td class="center"><a class="label label-default">{{ $stream->category ? $stream->category->name : '' }} </a></td>
                                    <td style="font-family: monospace;" class="center">
                                        <i class="fas fa-long-arrow-alt-down"></i>
                                        <a class="label label-default">
                                            <i class="fas fa-video"></i>
                                            @if($stream->video_codec_name)
                                            {{ strtoupper($stream->video_codec_name) }}
                                            @else
                                            'N/A'
                                            @endif
                                            |
                                            <i class="fas fa-volume-up"></i>
                                            @if($stream->audio_codec_name)
                                            {{ strtoupper($stream->audio_codec_name) }}
                                            @else
                                            'N/A'
                                            @endif
                                        </a>
                                    </td>
                                    <td class="center" style="font-family: monospace;">
                                        <i class="fas fa-long-arrow-alt-up"></i>
                                        <a class="label label-default">
                                            <i class="fas fa-video"></i>
                                            @if(($stream->transcode)->video_codec)
                                            {{ strtoupper(($stream->transcode)->video_codec) }}
                                            @else
                                            COPY
                                            @endif
                                            |
                                            <i class="fas fa-volume-up"></i>
                                            @if(($stream->transcode)->audio_codec)
                                            {{ strtoupper(($stream->transcode)->audio_codec) }}
                                            @else
                                            COPY
                                            @endif
                                        </a>
                                    </td>
                                    <td class="center">
                                        <a class="btn-success btn-sm" title="START STREAM" href="streams.php?start={{ $stream->id }}"><i class="fas fa-play"></i></a>
                                        <a class="btn-danger btn-sm" title="STOP STREAM" href="streams.php?stop={{ $stream->id }}"><i class="fas fa-stop"></i></a>
                                        <a class="btn-warning btn-sm" title="RESTART STREAM" href="streams.php?restart={{ $stream->id }}"><i class="fas fa-redo-alt"></i></a>
                                        <a class="btn-info btn-sm" href="manage_stream.php?id={{ $stream->id }}" title="Edit"><i class="far fa-edit"></i></a>
                                        <a class="pull-right btn-danger btn-sm" href="streams.php?delete={{ $stream->id }}" title="Delete" onclick="return confirm('Delete {{ $stream->name }} ?')"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                    @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-info">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            No streams found
                        </div>
                        @endif
                </form>
            </div>
        </div>
    </div>
    @endsection
    @section('js')
    <!-- Datatables -->
    <script src="js/datatables/js/jquery.dataTables.js"></script>
    <script src="js/datatables/tools/js/dataTables.tableTools.js"></script>
    <script>
        $(document).ready(function() {
            $('input.tableflat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
        var asInitVals = new Array();
        $(document).ready(function() {
            var oTable = $('#example').dataTable({
                "oLanguage": {
                    "sSearch": "Search all columns:"
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': [0]
                    } //disables sorting for column one
                ],
                'iDisplayLength': 50,
                "sPaginationType": "full_numbers"
            });
            $("tfoot input").keyup(function() {
                /* Filter on the column based on the index of this element's parent <th> */
                oTable.fnFilter(this.value, $("tfoot th").index($(this).parent()));
            });
            $("tfoot input").each(function(i) {
                asInitVals[i] = this.value;
            });
            $("tfoot input").focus(function() {
                if (this.className == "search_init") {
                    this.className = "";
                    this.value = "";
                }
            });
            $("tfoot input").blur(function(i) {
                if (this.value == "") {
                    this.className = "search_init";
                    this.value = asInitVals[$("tfoot input").index(this)];
                }
            });
        });
        $('table input').on('ifChecked', function() {
            check_state = '';
            $(this).parent().parent().parent().addClass('selected');
            countChecked();
        });
        $('table input').on('ifUnchecked', function() {
            check_state = '';
            $(this).parent().parent().parent().removeClass('selected');
            countChecked();
        });
        var check_state = '';
        $('.bulk_action input').on('ifChecked', function() {
            check_state = '';
            $(this).parent().parent().parent().addClass('selected');
            countChecked();
        });
        $('.bulk_action input').on('ifUnchecked', function() {
            check_state = '';
            $(this).parent().parent().parent().removeClass('selected');
            countChecked();
        });
        $('.bulk_action input#check-all').on('ifChecked', function() {
            check_state = 'check_all';
            countChecked();
        });
        $('.bulk_action input#check-all').on('ifUnchecked', function() {
            check_state = 'uncheck_all';
            countChecked();
        });
        function countChecked() {
            if (check_state == 'check_all') {
                $(".bulk_action input[name='mselect[]']").iCheck('check');
            }
            if (check_state == 'uncheck_all') {
                $(".bulk_action input[name='mselect[]']").iCheck('uncheck');
            }
            var n = $(".bulk_action input[name='mselect[]']:checked").length;
            if (n > 0) {
                $('.column-title').hide();
                $('.bulk-actions').show();
                $('.action-cnt').html(n + ' Records Selected');
            } else {
                $('.column-title').show();
                $('.bulk-actions').hide();
            }
        }
    </script>
    @endsection