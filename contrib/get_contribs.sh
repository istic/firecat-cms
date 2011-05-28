#!/bin/sh

# Given the relevant version numbers, downloads and unpacks the contrib 
# libraries in an upgradeable manner.

# NOTE: Requires curl! (not in default installation)

# Config:
IDIORM_VERSION=1.1.1
PARIS_VERSION=1.1.1
SMARTY_VERSION=3.0.6

# Script:
IDIORM_FILENAME=idiorm-v$IDIORM_VERSION.tar.gz
PARIS_FILENAME=paris-v$PARIS_VERSION.tar.gz
SMARTY_FILENAME=Smarty-$SMARTY_VERSION.tar.gz

curl -L -o $IDIORM_FILENAME https://github.com/j4mie/idiorm/tarball/v$IDIORM_VERSION && rm -r ./idiorm
curl -L -o $PARIS_FILENAME https://github.com/j4mie/paris/tarball/v$PARIS_VERSION && rm -r ./paris
curl -L -o $SMARTY_FILENAME http://www.smarty.net/files/Smarty-$SMARTY_VERSION.tar.gz && rm -r ./smarty

tar zxvf $IDIORM_FILENAME && rm $IDIORM_FILENAME
tar zxvf $PARIS_FILENAME && rm $PARIS_FILENAME
tar zxvf $SMARTY_FILENAME && rm $SMARTY_FILENAME

mv j4mie-idiorm-* idiorm
mv j4mie-paris-* paris
mv Smarty-$SMARTY_VERSION smarty
