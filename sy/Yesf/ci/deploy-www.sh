#!/bin/bash

main() {
	# Only push
	if [[ "$TRAVIS_EVENT_TYPE" != "push" ]];then
		echo -e "Not push, skip deploy www\n"
		return 0
	fi
	# Only master
	if [[ "$TRAVIS_BRANCH" != "master" ]];then
		echo -e "Not master, skip deploy www\n"
		return 0
	fi
	# Only first build job
	if ! [[ "$TRAVIS_JOB_NUMBER" =~ \.1$ ]];then
		echo -e "Not first build job, skip deploy www\n"
		return 0
	fi

	gitee_repo="sy/Yesf"
	gitee_branch="osc-pages"
	github_repo="sylingd/Yesf"
	github_branch="gh-pages"

	# Install node
	node_ver="10.15.3"
	curl -o ~/.nvm/nvm.sh https://raw.githubusercontent.com/creationix/nvm/v0.34.0/nvm.sh
	source ~/.nvm/nvm.sh
	nvm install $node_ver
	nvm use $node_ver
	nvm alias default $node_ver
	npm install -g yarn
	node --version
	yarn -v
	
	cd $TRAVIS_BUILD_DIR
	mkdir -p build/www

	# Build
	cd $TRAVIS_BUILD_DIR/docs
	yarn
	yarn build
	mv $TRAVIS_BUILD_DIR/docs/.vuepress/dist/* $TRAVIS_BUILD_DIR/build/www

	# Copy all files
	cp $TRAVIS_BUILD_DIR/ci/www/* $TRAVIS_BUILD_DIR/build/www/

	# Upload
	cd $TRAVIS_BUILD_DIR/build/www/
	git init
	git config user.name "Deployment Bot"
	git config user.email "deploy@travis-ci.org"
	git add .
	git commit -m "Automatic deployment"
	git push --force --quiet "https://${GITHUB_TOKEN}@github.com/${github_repo}.git" master:$github_branch
	git push --force --quiet "https://sy:${GITEE_TOKEN}@gitee.com/${gitee_repo}.git" master:$gitee_branch
}

main