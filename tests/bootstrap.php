<?php

declare(strict_types = 1);

namespace Datalator;

\error_reporting(\E_ALL);
\date_default_timezone_set('Europe/Berlin');
\mb_internal_encoding('UTF-8');

\define('TESTS_DIR', \getcwd() . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR);
\define('TESTS_FIXTURE_DIR', \TESTS_DIR . 'fixtures' . \DIRECTORY_SEPARATOR);
