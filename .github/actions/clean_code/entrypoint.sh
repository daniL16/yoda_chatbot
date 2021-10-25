#!/bin/sh
set -e
/php-cs-fixer fix api/src/ --verbose --rules=@Symfony,-no_unused_imports
/php-cs-fixer fix api/tests/ --verbose --rules=@Symfony,-no_unused_imports
