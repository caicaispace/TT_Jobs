var gulp = require('gulp'),
	browserSync = require('browser-sync');

gulp.task('server', function() {
	browserSync.init({
		server: "./"
	});
	gulp.watch([
		'index.html',
		'apps/**/*.*',
		'css/*.css'
	]).on("change", browserSync.reload);
});

gulp.task('default', ['server']);