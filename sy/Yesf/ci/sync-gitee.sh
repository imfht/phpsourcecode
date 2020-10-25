#!/bin/bash

main() {
	# Only push
	if [[ "$TRAVIS_EVENT_TYPE" != "push" ]];then
		echo -e "Not push, skip sync gitee\n"
		return 0
	fi
	# Only master
	if [[ "$TRAVIS_BRANCH" != "master" ]];then
		echo -e "Not master, skip deploy www\n"
		return 0
	fi
	# Only first build job
	if ! [[ "$TRAVIS_JOB_NUMBER" =~ \.1$ ]];then
		echo -e "Not first build job, skip sync gitee\n"
		return 0
	fi

	gitee_repo="sy/Yesf"

	# Upload
	cd $TRAVIS_BUILD_DIR
	git config user.name "ShuangYa"
	git config user.email "$GIT_MAIL"
	git push --quiet "https://sy:${GITEE_TOKEN}@gitee.com/${gitee_repo}.git" master:master
}

main