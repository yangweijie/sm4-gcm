@echo off
REM SM4-GCM Comparison Test Runner
REM This script runs the comparison tests using the specified PHP version

REM Set the path to the PHP executable
REM Modify this path to use the specific PHP version mentioned in the story
set PHP_EXECUTABLE=php

REM Check if the specific PHP version exists
if exist "D:\Program Files\PhpWebStudy-Data\app\php84.exe" (
    set PHP_EXECUTABLE="D:\Program Files\PhpWebStudy-Data\app\php84.exe"
    echo Using specific PHP version: D:\Program Files\PhpWebStudy-Data\app\php84.exe
) else (
    echo Using system default PHP version
    echo To use the specific PHP version, please ensure it is installed at:
    echo D:\Program Files\PhpWebStudy-Data\app\php84.exe
)

REM Run the comparison tests
%PHP_EXECUTABLE% tests/Comparison/run_comparison_tests.php

REM Pause to allow user to see the results
pause