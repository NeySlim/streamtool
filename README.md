![Streamtool](https://github.com/NeySlim/streamtool/raw/master/app/www/img/streamtool.png "Streamtool")

A web software for managing and manipulating video streams.

## Features:
- Streaming and restreaming
- Manage users
- Manage stream categories
- Manage streams 
- Transcode streams with advanced configuration
- Manage transcode profiles
- NVENC full hardware transcoding (decoding/encoding) support for H264/HEVC
- VAAPI hardware transcoding
- Autorestart on stream failure
- Playlist generation
- Bulk import
- User Agent manager
- IP filter manager
- Resources monitor
- Patch nvidia encoder limit for consumer cards on demand


## Installation
 **SUPPORTED DISTRIBUTION : Ubuntu 20.04 64 BIT**
  As administrator execute:
```
wget https://bitbucket.org/le_lio/streamtool/raw/master/streamtool.tar.gz && tar xvzf streamtool.tar.gz && bash st-install.sh
```
  Visit : http://streamtool-adress:9001/ login with 
 Default Username Password: admin


## How does it work ?
- Default login: admin / admin
  - Setup settings in left panel
  - Add a category to allow user and stream creation
  - Add a stream or import a playlist
  - Add a user
- not recommanded to change hls output directory
- Not using transcoding will only remux stream to simple hls output.



