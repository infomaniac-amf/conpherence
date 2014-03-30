var gulp = require('gulp'),
    notify = require( 'gulp-notify'),
    size = require('gulp-size'),
    browserify  = require('gulp-browserify');

gulp.task('default', function() {
    gulp.watch(['js/app.js', 'js/*.js', 'js/**/*.js'], function(e) {
        gulp.src('js/app.js')
            .pipe(browserify({
                insertGlobals: true
            }))
            .pipe(gulp.dest('./public/js'))
            .pipe(notify('Done!'))
            .pipe(size());
    });
});