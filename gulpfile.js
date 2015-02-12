var gulp  = require('gulp'),
    watch = require('gulp-watch'),
    shell = require('gulp-shell');


/**
 * Codeception tasks
 */
// Run unit tests
 gulp.task('cc:run:unit', shell.task(['codecept run unit']));

// Watch for changes and run unit tests
gulp.task('cc:watch:unit', function () {
    gulp.watch(['src/**/*.php', 'tests/**/*.php'], ['cc:run:unit']);
});