#!/bin/bash
# SM4-GCM Comparison Test Runner
# This script runs the comparison tests using the specified PHP version

# Set the path to the PHP executable
# Modify this path to use the specific PHP version mentioned in the story
PHP_EXECUTABLE="php"

# Check if the specific PHP version exists
if [ -f "/d/Program Files/PhpWebStudy-Data/app/php84.exe" ]; then
    PHP_EXECUTABLE="/d/Program Files/PhpWebStudy-Data/app/php84.exe"
    echo "Using specific PHP version: /d/Program Files/PhpWebStudy-Data/app/php84.exe"
else
    echo "Using system default PHP version"
    echo "To use the specific PHP version, please ensure it is installed at:"
    echo "/d/Program Files/PhpWebStudy-Data/app/php84.exe"
fi

# Run the comparison tests
$PHP_EXECUTABLE tests/Comparison/run_comparison_tests.php