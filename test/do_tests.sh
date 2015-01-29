#! /bin/sh
#DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DIR=`pwd`/`dirname $0`
cd $DIR
echo `pwd`
phpunit --bootstrap ../autoload.php src
