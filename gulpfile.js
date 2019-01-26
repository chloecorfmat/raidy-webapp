const gulp = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const rename = require('gulp-rename');
const autoprefixer = require('gulp-autoprefixer');
const sassLint = require('gulp-sass-lint');
const esLint = require('gulp-eslint');
const cleanCSS = require('gulp-clean-css');
const babel = require('gulp-babel');
const merge = require('merge-stream');
const uglify = require('gulp-uglify-es').default;


// Linter.
gulp.task('lint', function() {
    gulp.src('./web/assets/js/*.js')
        //.pipe(esLint())
        .pipe(esLint.format());
        //.pipe(esLint.failAfterError());

      gulp.src('./web/assets/js/editor/*.js')
       // .pipe(esLint())
        .pipe(esLint.format());
      //.pipe(esLint.failAfterError());

    gulp.src(['./web/assets/scss/**/*.scss', '!./web/assets/scss/lib/*.scss'])
        .pipe(sassLint())
        .pipe(sassLint.format())
        .pipe(sassLint.failOnError());
    return null;
});

gulp.task('styles', function() {
    // place code for your default task here
    return gulp.src('./web/assets/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false,
        }))
        .pipe(rename('../dist/css/styles.css'))
        .pipe(gulp.dest('./web/assets/css'))
        // Comment the line below to have unminify files.
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(rename('css/styles.min.css'))
        .pipe(gulp.dest('./web/assets/dist'));
});

gulp.task('scripts', function() {
    var core = gulp.src(['./web/assets/js/*.js', './web/assets/js/editor/*.js', './web/assets/js/race/*.js'])
        .pipe(concat('../dist/js/scripts.js'))
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(gulp.dest('./web/assets/js'))
        // Comment the line below to have unminify files.
        .pipe(uglify())
        .pipe(rename('js/scripts.min.js'))
        .pipe(gulp.dest('./web/assets/dist'));

    var lib = gulp.src(['./web/assets/js/lib/*.js'])
        .pipe(concat('../dist/js/lib.js'))
        .pipe(gulp.dest('./web/assets/js'))
        // Comment the line below to have unminify files.
        .pipe(uglify())
        .pipe(rename('js/lib.min.js'))
        .pipe(gulp.dest('./web/assets/dist'));

    return lib;
});

gulp.task('default', ['lint', 'styles', 'scripts'], function() {
    // place code for your default task here
    return null;
});

gulp.task('watch', function () {
    gulp.watch(['./web/assets/scss/**/*.scss', './web/assets/js/**/*.js'], ['lint']);
    gulp.watch('./web/assets/scss/**/*.scss', ['styles']);
    gulp.watch('./web/assets/js/**/*.js', ['scripts']);
});
