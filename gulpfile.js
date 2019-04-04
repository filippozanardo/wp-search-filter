// plugins for development
var gulp = require('gulp'),
		rimraf = require('rimraf'),
		sass = require('gulp-sass'),
		gulpSequence = require('gulp-sequence'),
		inlineimage = require('gulp-inline-image'),
		prefix = require('gulp-autoprefixer'),
		plumber = require('gulp-plumber'),
		dirSync = require('gulp-directory-sync'),
		browserSync = require('browser-sync').create(),
		reload = browserSync.reload,
		concat = require('gulp-concat'),
		cssfont64 = require('gulp-cssfont64'),
		sourcemaps = require('gulp-sourcemaps');

// plugins for build
var purify = require('gulp-purifycss'),
		uglify = require('gulp-uglify'),
		imagemin = require('gulp-imagemin'),
		pngquant = require('imagemin-pngquant'),
		csso = require('gulp-csso');

//plugins for testing
var html5Lint = require('gulp-html5-lint');
var reporter = require('postcss-reporter');
var stylelint = require('stylelint');
//var postcss_scss = require("postcss-scss");

var assetsDir = 'assets/';
var outputDir = 'dist/';
var cssDir = 'css/';
var jsDir = 'js/';
var imgDir = 'img/';
var fontDir = 'fonts/';
var buildDir = 'build/';

//----------------------------------------------------Compiling
// gulp.task('pug', function () {
// 	gulp.src([assetsDir + 'pug/*.pug', '!' + assetsDir + 'pug/_*.pug'])
// 		.pipe(plumber())
// 		.pipe(pug({pretty: true}))
// 		.pipe(gulp.dest(outputDir))
// 		.pipe(browserSync.stream({once: true}));
// });

gulp.task('sass', function () {
	gulp.src([assetsDir + 'sass/**/*.scss', '!' + assetsDir + 'sass/**/_*.scss'])
		.pipe(plumber())
		.pipe(sourcemaps.init())
		.pipe(sass(
			{
        includePaths: [
          './node_modules/bootstrap/scss',
        ]
      }
		))
		.pipe(inlineimage())
		.pipe(prefix('last 3 versions'))
		// .pipe(postcss([assets({
		// 	basePath:outputDir,
		// 	loadPaths: ['i/']
		// })]))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(cssDir))
		.pipe(browserSync.stream({match: "**/*.css"}));
});

gulp.task('jsConcat', function () {
	return gulp.src(assetsDir + 'js/ext/**/*.js')
		.pipe(concat('ext.js', {newLine: ';'}))
		.pipe(gulp.dest(jsDir))
		.pipe(browserSync.stream({once: true}));
});

gulp.task('fontsConvert', function () {
	return gulp.src([assetsDir + 'fonts/*.woff', assetsDir + 'fonts/*.woff2'])
		.pipe(cssfont64())
		.pipe(gulp.dest(fontDir))
		.pipe(browserSync.stream({once: true}));
});

//----------------------------------------------------Compiling###

//-------------------------------------------------Synchronization
gulp.task('imageSync', function () {
	return gulp.src('')
		.pipe(plumber())
		.pipe(dirSync(assetsDir + 'img/', imgDir, {printSummary: true}))
		.pipe(browserSync.stream({once: true}));
});

gulp.task('fontsSync', function () {
	return gulp.src('')
		.pipe(plumber())
		.pipe(dirSync(assetsDir + 'fonts/', fontDir, {printSummary: true}))
		.pipe(browserSync.stream({once: true}));
});

gulp.task('jsSync', function () {
	return gulp.src(assetsDir + 'js/*.js')
		.pipe(plumber())
		.pipe(gulp.dest(jsDir))
		.pipe(browserSync.stream({once: true}));
});
//-------------------------------------------------Synchronization###


//watching files and run tasks
gulp.task('watch', function () {
	//gulp.watch(assetsDir + 'pug/**/*.pug', ['pug']);
	gulp.watch(assetsDir + 'sass/**/*.scss', ['sass']);
	gulp.watch(assetsDir + 'js/**/*.js', ['jsSync']);
	gulp.watch(assetsDir + 'js/ext/**/*.js', ['jsConcat']);
	gulp.watch(assetsDir + 'img/**/*', ['imageSync']);
	gulp.watch(assetsDir + 'fonts/**/*', ['fontsSync', 'fontsConvert']);
});

var projectURL = "192.168.1.64/gemreport";
//livereload and open project in browser
var plugins = {
	browserSync: {
		options: {
			//port: 1337,
			// server: {
			// 	baseDir: "./"
			// }
			proxy: projectURL,
		}
	}
}

gulp.task('browser-sync', function() {
	return browserSync.init(plugins.browserSync.options);
});

gulp.task('bs-reload', function (cb) {
	browserSync.reload();
});


//---------------------------------building final project folder
//clean build folder
gulp.task('cleanBuildDir', function (cb) {
	rimraf(buildDir, cb);
});


//minify images
gulp.task('imgBuild', function () {
	return gulp.src([outputDir + 'i/**/*', '!' + outputDir + 'i/sprite/**/*'])
			.pipe(imagemin({
				progressive: true,
				svgoPlugins: [{removeViewBox: false}],
				use: [pngquant()]
			}))
			.pipe(gulp.dest(buildDir + 'i/'))
});

//copy sprite.svg
gulp.task('copySprite', function () {
	return gulp.src(outputDir + 'i/sprite/sprite.svg')
	.pipe(plumber())
	.pipe(gulp.dest(buildDir + 'i/sprite/'))
});

//copy fonts
gulp.task('fontsBuild', function () {
	return gulp.src(outputDir + 'fonts/**/*')
		.pipe(gulp.dest(buildDir + 'fonts/'))
});

//copy html
gulp.task('htmlBuild', function () {
	return gulp.src(outputDir + '**/*.html')
		.pipe(gulp.dest(buildDir))
});

//copy and minify js
gulp.task('jsBuild', function () {
	return gulp.src(outputDir + 'js/**/*')
		.pipe(uglify())
		.pipe(gulp.dest(buildDir + 'js/'))
});

//copy, minify css
gulp.task('cssBuild', function () {
	return gulp.src(outputDir + 'styles/**/*')
		.pipe(csso())
		.pipe(gulp.dest(buildDir + 'styles/'))
});


//// --------------------------------------------If you need iconfont
// var iconfont = require('gulp-iconfont'),
// 	iconfontCss = require('gulp-iconfont-css'),
// 	fontName = 'iconfont';
// gulp.task('iconfont', function () {
// 	gulp.src([assetsDir + 'i/icons/*.svg'])
// 		.pipe(iconfontCss({
// 			path: 'assets/sass/templates/_icons_template.scss',
// 			fontName: fontName,
// 			targetPath: '../../sass/_icons.scss',
// 			fontPath: '../fonts/icons/',
// 			svg: true
// 		}))
// 		.pipe(iconfont({
// 			fontName: fontName,
// 			svg: true,
// 			formats: ['svg','eot','woff','ttf']
// 		}))
// 		.pipe(gulp.dest('assets/fonts/icons'));
// });

// --------------------------------------------If you need svg sprite
var svgSprite = require('gulp-svg-sprite'),
		svgmin = require('gulp-svgmin'),
		cheerio = require('gulp-cheerio'),
		replace = require('gulp-replace');

gulp.task('svgSpriteBuild', function () {
	return gulp.src(assetsDir + 'i/icons/*.svg')
	// minify svg
	.pipe(svgmin({
		js2svg: {
			pretty: true
		}
	}))
	// remove all fill and style declarations in out shapes
	.pipe(cheerio({
		run: function ($) {
			$('[fill]').removeAttr('fill');
			$('[stroke]').removeAttr('stroke');
			$('[style]').removeAttr('style');
		},
		parserOptions: {xmlMode: true}
	}))
	// cheerio plugin create unnecessary string '&gt;', so replace it.
	.pipe(replace('&gt;', '>'))
	// build svg sprite
	.pipe(svgSprite({
		mode: {
			symbol: {
				sprite: "../sprite.svg",
				render: {
					scss: {
						dest:'../../../sass/_sprite.scss',
						template: assetsDir + "sass/templates/_sprite_template.scss"
					}
				},
				example: true
			}
		}
	}))
	.pipe(gulp.dest(assetsDir + 'i/sprite/'));
});

//testing your build files
gulp.task('validation', function () {
	return gulp.src(buildDir + '**/*.html')
		.pipe(html5Lint());
});

gulp.task('cssLint', function () {
	return gulp.src([assetsDir + 'sass/**/*.scss', '!' + assetsDir + 'sass/templates/*.scss'])
		.pipe(postcss(
			[
				stylelint(),
				reporter({ clearMessages: true })
			],
			{
				syntax: postcss_scss
			}
		));
});


//gulp.task('default', ['pug', 'sass', 'imageSync', 'fontsSync', 'fontsConvert', 'jsConcat', 'jsSync', 'watch', 'browser-sync']);

gulp.task('default', [ 'sass', 'imageSync', 'fontsSync', 'fontsConvert', 'jsConcat', 'jsSync', 'watch', 'browser-sync']);

gulp.task('build', ['cleanBuildDir'], function () {
	gulp.start('imgBuild', 'fontsBuild', 'htmlBuild', 'jsBuild', 'cssBuild', 'copySprite');
});
