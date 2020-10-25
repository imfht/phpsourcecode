find ./ -name ".DS_Store" -depth -exec rm {} \;
#defaults write com.apple.desktopservices DSDontWriteNetworkStores true

rm -rf ./var/cache/*
rm -rf ./var/log/*
rm -rf ./var/tmp/*
rm -rf ./var/pid/*

