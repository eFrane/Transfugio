var gulp     = require('gulp');
var notify   = require('gulp-notify');
var codecept = require('gulp-codeception');
var run      = require('gulp-run');

gulp.task('test', function () {
    gulp.src('codeception.yml')
        .pipe(codecept('./vendor/bin/codecept', { notify: true}))
        .on('error', notify.onError({
            'title': 'Crap!',
            'message': 'A test failed!'
        }))
        .pipe(notify({
            'title': 'Success!',
            'message': 'All tests passed!'
        }));
});

gulp.task('watch', function() {
    gulp.watch(['tests/**/*.php', 'src/**/*.php'], ['test']);
});
