#!/bin/bash

ffmpeg -i 1.mp3 -acodec libmp3lame 1.mp3 -y
ffmpeg -i 2.mp3 -acodec libmp3lame 2.mp3 -y

ffmpeg -i concat:'1.mp3|2.mp3' -c copy out.mp3