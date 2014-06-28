#!/bin/bash

BASEPATH=$(cd $(dirname $0) && pwd)

${BASEPATH}/vendor/bin/phpunit --colors --bootstrap ${BASEPATH}/application/boot.php ${BASEPATH}/application/test
