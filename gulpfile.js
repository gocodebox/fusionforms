var gulp = require('gulp'),

	autoprefixer = require('gulp-autoprefixer'),
	fs = require('fs'),
	include = require('gulp-include'),
	jshint = require('gulp-jshint'),
	minifycss = require('gulp-minify-css'),
	notify = require('gulp-notify'),
	rename = require('gulp-rename'),
	sass = require('gulp-ruby-sass'),
	stylish = require('jshint-stylish'),
	svgmin = require('gulp-svgmin'),
	svgSprite = require('gulp-svg-sprites'),
	uglify = require('gulp-uglify'),
	watch = require('gulp-watch'),

	// get json from package!
	pkg = require('./package.json'),

	// default bump update
	bump_type = 'patch';





// start a browser-sync server
gulp.task('browser-sync', function() {
	browserSync({
		// debugInfo: false,
		// online: true,
		// open: 'tunnel',
		proxy: 'local.wordpress.dev',
		// tunnel: 'http://local.wordpress.dev/'
	});
});

// JAVACRIPT LINTER
gulp.task('lint-js', function () {
	return gulp.src([
			'_private/**/*.js',
			'!_private/**/lib/*.js',
		])
		.pipe(jshint('.jshintrc'))
		.pipe(jshint.reporter(stylish))
		.pipe(jshint.reporter('fail'))
		.on('error',notify.onError({
			message: '<%= error.message %>',
			sound: 'Frog',
			title: 'JS Hint Error'
		}));
});

// JAVACRIPT BUILDER
gulp.task('build-js',['lint-js'], function(){
	return gulp.src([
			'_private/**/*.js',
			'!_private/**/_*.js'
		])
		.pipe(include())
		.pipe(gulp.dest('assets'))
		.pipe(rename({ suffix: '.min' }))
		.pipe(uglify())
		.pipe(gulp.dest('assets'))
		.on('error', notify.onError({
			message: '<%= error.message %>',
			sound: 'Frog',
			title: 'JS Build Error'
		}));
});

// RUN JS TASKS
gulp.task('js',['lint-js','build-js'],function(){
	return gulp.src('_private/**/*.js')
		.pipe(notify({
			message: 'No Errors!',
			onLast: true,
			title: 'Javascript Tasks Complete'
		}));
});




// BUILD SASS
gulp.task('sass',function() {
	return gulp.src([
			'_private/**/*.scss',
			'!_private/**/_*.scss'
		])
		.pipe(sass({
			cacheLocation: '_private/.sass-cache',
			/**
			 * sourcemaps working wrong this is a hack until this issue is resolved
			 * https://github.com/sindresorhus/gulp-ruby-sass/issues/130
			 */
			'sourcemap=none': true,
			style: 'expanded'
		}))
		.on('error', notify.onError({
			message: '<%= error.message %>',
			sound: 'Frog',
			title: 'SASS Compilation Error'
		}))
		.pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
		.pipe(rename(function(path){
			path.dirname = path.dirname.replace('/scss','/css');
		}))
		.pipe(gulp.dest('assets'))
		.pipe(rename({suffix: '.min'}))
		.pipe(minifycss())
		.pipe(gulp.dest('assets'))
		.on('error', notify.onError({
			message: '<%= error.message %>',
			sound: 'Frog',
			title: 'SASS Build Error'
		}));
});

// generate an SVG sprite
// gulp.task('svg', function () {
// 	return gulp.src(svg_glob)
// 		.pipe(svgSprite({
// 			// cssFile: '_private/public/scss/_svg.scss',
// 			mode: 'symbols',
// 			preview: false,
// 			svg: {
// 				symbols: 'img/icons.svg',
// 			},
// 			svgId: 'pp-icon-%f'
// 		}))
// 		.pipe(gulp.dest('assets/public/'))
// 		.pipe(rename({suffix: '.min'}))
// 		.pipe(svgmin([{
//             cleanupIDs: false
//         }]))
// 		.pipe(gulp.dest('assets/public/'));
// });


// build all
gulp.task('rebuild',['sass','js','svg'],function(){});

// watcher
gulp.task('watch',function(){
	gulp.watch(['_private/**/*.scss'],['sass']);
	gulp.watch(['_private/**/*.js'],['js']);
	// gulp.watch(svg_glob,['svg']);
});