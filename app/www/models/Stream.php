<?php
class Stream extends Streamtool {

    public function category()
    {
        return $this->hasOne('Category', 'id', 'cat_id');
    }

    public function transcode()
    {
        return $this->hasOne('Transcode', 'id', 'trans_id');
    }

    public function getStatusLabelAttribute()
    {
        $return = [];
        $return['label'] = 'danger';
        $return['text'] = 'STOPPED';
        $return['icon'] = 'fas fa-stop-circle';

        if ($this->status == '1') {
            $return['label'] = 'success';
            $return['text'] = 'RUNNING';
            $return['icon'] = 'fas fa-play-circle';
        } else if ($this->status == '2') {
            $return['label'] = 'danger';
            $return['text'] = 'ERROR';
            $return['icon'] = 'fas fa-exclamation-circle';
        }

        return $return;
    }
}