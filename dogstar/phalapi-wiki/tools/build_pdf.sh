#!/bin/bash
#
# ./build_pdf.sh
#
# @author dogstar 20180331
#

## Usage

## Env
#WIKI_PATH=$1
WIKI_PATH="./v2.0"
BASE_PATH=$(cd `dirname $0`; pwd)
BASE_PATH="./tools"
PHP_PATH="/usr/bin/php"

if [ ! -d $WIKI_PATH ]
then
    echo "Error: can not open $WIKI_PATH !"
    echo ""
    exit 2
fi

ALL_IN_ONE="/tmp/PhalApi-2x-开发文档.md"
echo "" > $ALL_IN_ONE

while read line
do
    if [ ! -f $WIKI_PATH/$line ]; then
        echo "no such file: $line"
        continue;
    fi

    echo "add $line ..."

    # echo "#"${line/.md/} >> $ALL_IN_ONE

    cat $WIKI_PATH/$line >> $ALL_IN_ONE
done < $BASE_PATH/pdf_config.txt

$PHP_PATH $BASE_PATH/parse_markdown_file.php $ALL_IN_ONE $BASE_PATH/../html/

ALL_IN_ONE_HTML=$BASE_PATH/../html/PhalApi-2x-release.html

cat $BASE_PATH/html_header.html > $ALL_IN_ONE_HTML
cat $BASE_PATH/../html/PhalApi-2x-开发文档.html >> $ALL_IN_ONE_HTML
cat $BASE_PATH/html_footer.html >> $ALL_IN_ONE_HTML

cd $BASE_PATH/..

git commit -a -m "build pdf"
git push

echo "done!"

