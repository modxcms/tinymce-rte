const gulp = require('gulp'),
    composer = require('gulp-uglify/composer'),
    format = require('date-format'),
    header = require('gulp-header'),
    rename = require('gulp-rename'),
    replace = require('gulp-replace'),
    uglifyjs = require('uglify-js'),
    uglify = composer(uglifyjs, console),
    clean = require('gulp-clean'),
    unzip = require('gulp-unzip'),
    download = require('gulp-download2'),
    pkg = require('./_build/config.json');

const banner = '/*!\n' +
    ' * <%= pkg.name %> - <%= pkg.description %>\n' +
    ' * Version: <%= pkg.version %>\n' +
    ' * Build date: ' + format("yyyy-MM-dd", new Date()) + '\n' +
    ' */';
const year = new Date().getFullYear();
const tinyMCEVersion = '5.9.2';

const copyTinyMCE = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/tinymce.min.js',
        'src/tinymce/js/tinymce/license.txt'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/'))
};
const copyIcons = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/icons/**/*.min.js'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/icons/'))
};
const copyPlugins = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/plugins/**/*.min.js'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/plugins/'))
};
const copySkins = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/skins/**/*.min.css',
        'src/tinymce/js/tinymce/skins/**/*.woff'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/skins/'))
};
const copyThemes = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/themes/**/*.min.js'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/icons/'))
};
const copyIconsDebug = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/icons/**/*.js',
        '!src/tinymce/js/tinymce/icons/**/*.min.js'
    ])
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/icons/'))
};
const copyPluginsDebug = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/plugins/**/*.js',
        '!src/tinymce/js/tinymce/plugins/**/*.min.js'
    ])
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/plugins/'))
};
const copyThemesDebug = function () {
    return gulp.src([
        'src/tinymce/js/tinymce/themes/**/*.js',
        '!src/tinymce/js/tinymce/themes/**/*.min.js'
    ])
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/icons/'))
};
const copyModxPlugins = function () {
    return gulp.src([
        'src/modx/plugins/**/plugin.js',
    ])
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/plugins/'))
};
const copyModxSkin = function () {
    return gulp.src([
        'src/modx/skins/**/*.min.css',
        'src/modx/skins/**/*.woff'
    ])
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/skins/'))
};
gulp.task('copy-tinymce', gulp.series(copyTinyMCE, copyIcons, copyPlugins, copySkins, copyThemes));
gulp.task('copy-tinymce-debug', gulp.series(copyTinyMCE, copyIconsDebug, copyPluginsDebug, copySkins, copyThemesDebug));

const scriptsMgr = function () {
    return gulp.src([
        'src/modx/mgr/*.js',
    ])
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/mgr/'))
};
const scriptsPlugins = function () {
    return gulp.src([
        'src/modx/plugins/**/*.js',
        'src/modx/plugins/**/*.original.js'
    ])
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/plugins/'))
};
gulp.task('scripts', gulp.series(scriptsMgr, scriptsPlugins));

gulp.task('clean', function () {
    return gulp.src('src/tinymce', {read: false})
        .pipe(clean());
});

const downloadTinyMCE = function () {
    return download('https://download.tiny.cloud/tinymce/community/tinymce_' + tinyMCEVersion + '_dev.zip')
        .pipe(rename('tinymce_dev.zip'))
        .pipe(gulp.dest('src/'));
};
const downloadLanguages = function () {
    return download('https://www.tiny.cloud/tinymce-services-azure/1/i18n/download?langs=ar,be,bg_BG,cs,da,de,el,es,et,fa,fi,fr_FR,he_IL,id,it,ja,nl,pl,pt_BR,ro,ru,sk,sv_SE,th_TH,uk,zh_CN')
        .pipe(rename('tinymce_languages.zip'))
        .pipe(gulp.dest('src/'));
};
gulp.task('download', gulp.series(downloadTinyMCE, downloadLanguages));

const unzipTinyMCE = function () {
    return gulp.src('src/tinymce_dev.zip')
        .pipe(unzip())
        .pipe(gulp.dest('src/'));
};
const unzipLanguages = function () {
    return gulp.src('src/tinymce_languages.zip')
        .pipe(unzip())
        .pipe(gulp.dest('assets/components/tinymcerte/js/vendor/tinymce/'));
};
gulp.task('unzip', gulp.series(unzipTinyMCE, unzipLanguages));

const bumpVersion = function () {
    return gulp.src([
        'core/components/tinymcerte/src/TinyMCERTE.php'
    ], {base: './'})
        .pipe(replace(/version = '\d+\.\d+\.\d+[-a-z0-9]*'/ig, 'version = \'' + pkg.version + '\''))
        .pipe(gulp.dest('.'));
};
gulp.task('bump', gulp.series(bumpVersion));

// Combined Tasks
gulp.task('prepare', gulp.series('copy-tinymce', 'scripts', copyModxSkin));
gulp.task('prepare-debug', gulp.series('copy-tinymce-debug', 'scripts', copyModxPlugins, copyModxSkin));
gulp.task('update', gulp.series('clean', 'download', 'unzip'));
gulp.task('default', gulp.series('bump'));
