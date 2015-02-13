var gulp     = require('gulp'),
    watch    = require('gulp-watch'),
    shell    = require('gulp-shell'),
    codecept = require('gulp-codeception'),
    notify   = require('gulp-notify');

var srcFilePattern         = './src/**/*.php',
    unitTestPattern        = './tests/unit/**/*.php',
    testSupportFilePattern = './tests/_support/**/*.php';


gulp.task('clear', shell.task(
    [
        'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo',
        'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo',
        'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo', 'echo'
    ]
));


/**
 * Codeception tasks
 */
gulp.task('cc:unit', function () {
    var options = {
        flags    : '--no-colors',
        testSuite: 'unit',
        debug    : false,
        notify   : true
    };
    gulp.src(unitTestPattern)
        .pipe(codecept(false, options))
        .on('error', notify.onError({
            title  : "Unit Tests failed!",
            message: "Errors during runtime <%= error.message %>",
            icon   : './node_modules/gulp-codeception/assets/test-fail.jpg'
        }))
        .pipe(notify({
            title  : 'Success!',
            icon   : './node_modules/gulp-codeception/assets/test-pass.jpg',
            message: 'Everything was successful!'
        }))
});
gulp.task('watch:cc:unit', ['cc:unit'], function () {
    gulp.watch([srcFilePattern, unitTestPattern, testSupportFilePattern], ['clear', 'cc:unit']);
});