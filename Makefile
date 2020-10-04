analyse::
	@bin/phpstan analyse --autoload-file Build/BuildEssentials/PhpUnit/UnitTestBootstrap.php --level 8 Tests/Unit
	@bin/phpstan analyse --level 8 Classes

test::
	@bin/phpunit -c phpunit.xml \
		--enforce-time-limit \
		--coverage-html Build/Reports/coverage \
		Tests

test-isolated::
	@bin/phpunit -c phpunit.xml \
		--enforce-time-limit \
		--group isolated \
		Tests
