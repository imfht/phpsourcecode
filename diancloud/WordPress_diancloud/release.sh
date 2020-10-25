#/bin/bash

if [ -z $1 ]; then
	echo "./release.sh <tag>";
	exit;
fi

if [ $(git tag |grep $1 |wc -l) != 1 ]; then
	echo " TAG [$1] NOT FOUND";
	exit;
fi

RELEASE_NAME="wordpress-$1.zip"
DOCKER_FILE_PATH=/Volumes/Work/MacBook/WORK-2015/dockerfile/dockerfile/Docker/applications/wordpress/$1
RELEASE_PATH=/Volumes/Work/MacBook/WORK-2015/wordpress/release

mkdir -p $RELEASE_PATH

echo "$RELEASE_PATH/$RELEASE_NAME"
mkdir -p $DOCKER_FILE_PATH

if [ -f "$RELEASE_PATH/$RELEASE_NAME" ]; then 
	rm -f  "$RELEASE_PATH/$RELEASE_NAME"
fi

git archive --format=zip $1 > "$RELEASE_PATH/$RELEASE_NAME"

if [ -f "$DOCKER_FILE_PATH/$RELEASE_NAME" ]; then 
	mv "$DOCKER_FILE_PATH/$RELEASE_NAME" "$DOCKER_FILE_PATH/$RELEASE_NAME.$(date +%Y%m%d%H%M%S)"
fi

cp "$RELEASE_PATH/$RELEASE_NAME" "$DOCKER_FILE_PATH/$RELEASE_NAME"

