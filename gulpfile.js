/*РУКОВОДСТВО ПО GULP

>> НАСТРОЙКА НОВОГО ПРОЕКТА

1. Открыть проект в терминале
2. Выполнить команду ( npm i ) - установит модули
3. В файле gulpfile.js сделать "БАЗОВУЮ НАСТРОЙКУ"
- Шаг 1. Выбрать препроцессор scss || less
- Шаг 2. Назвать проект, как в hg
- Шаг 3. Выполнить команду ( gulp custom-gulpfile ) - создаст файл с ИНДИВИДУАЛЬНЫМИ НАСТРОЙКАМИ
- Шаг 4. Выполнить команду ( gulp new-project ) - удалит папку со стилями, т.е Если выбрали SASS, то удалит папку LESS и наоборот
4. В файле custom-gulpfile.js сделать "ИНДИВИДУАЛЬНУЮ НАСТРОЙКУ"
5. Выполнить команду ( gulp ) - запустит проект
6. Открыть проект - localhost:3000 (ввести в браузере)


>> ЗАПУСК ПРОЕКТА (раннее созданного)

1. Открыть проект в терминале
2. Выполнить команду ( gulp custom-gulpfile ) - создаст custom-gulpfile.js
3. Выполнить команду ( gulp ) - запустит проект
4. Открыть проект - localhost:3000 (ввести в браузере)
*/

'use strict';

// БАЗОВАЯ НАСТРОЙКА ПРОЕКТА
// 1. scss || less
var preprocessor = 'scss';

// 2. Название проекта как в hg
var nameProject = 'blackbox-ufo';

// 3. Создать custom-gulpfile.js
// gulp custom-gulpfile

// 4. Выполнить команду, если НОВЫЙ ПРОЕКТ
// gulp new-project


// ------------------------- //
// Экспорт модулей
const fs = require('fs');

let gulp = require('gulp'),
	browserSync = require('browser-sync'),
	watch = require('gulp-watch'),
	sass = require('gulp-sass')(require('sass')),
	less = require('gulp-less'),
	autoprefixer = require('gulp-autoprefixer'),
	rename = require('gulp-rename'),
	concat = require('gulp-concat'),
	gulpif = require('gulp-if'),
	del = require('del'),
	plumber = require('gulp-plumber'),
	touch = require('gulp-touch-cmd'),
	notify = require("gulp-notify"),
	csso = require('gulp-csso'),
	babel = require('gulp-babel'),
	uglify = require('gulp-uglify'),
	ftp = require('vinyl-ftp'),
	zip = require('gulp-zip');



let customFilePath = "./custom-gulpfile.js";	// путь к файлу индивидуальной настройки
let styleFiles = ['styles', 'tinymce'];			// файлы стилей для препроцессора для отдельной сборки
let scriptFiles = ['cart', 'product-show'];		// файлы js для отдельной сборки

if (fs.existsSync(customFilePath)) {
	var customGulpfile = require(customFilePath);
} else {
	customGulpfile = {
		watchFiles: ["views/**/**/**/*.+(html|htm)"], // ФАЙЛЫ ЗА КОТОРЫМИ НУЖНО СЛЕДИТЬ (для автоматической перезагрузки страницы)
		styleHotReload: false, 	// АВТООБНОВЛЕНИЕ СТИЛЕЙ
		tasks: function () { return false; }
	}
}

// Удаляет ненужную папку стилей
gulp.task('new-project', function (done) {
	let del_preprocessor = "";
	if (preprocessor == 'scss') {
		del_preprocessor = 'less';
	}
	else {
		del_preprocessor = 'scss';
	};

	del.sync(['resources/' + del_preprocessor]);

	done();
});

// Перезагрузка страницы после изменения исходных файлов
gulp.task('browser-sync-start', function (done) {

	browserSync.init({
		proxy: "http://" + nameProject + ".web",
		notify: true,
		open: false
	});

	done();
});

gulp.task('browser-sync-reload', function () {
	browserSync.reload();
});

// слежение за файлами
gulp.task('watch-files', function () {

	if (customGulpfile.styleHotReload) {
		gulp.watch(customGulpfile.watchFiles, gulp.parallel('browser-sync-reload'));
	}

	gulp.watch('resources/' + preprocessor + '/**/**/*.' + preprocessor, gulp.parallel('style'));
	gulp.watch('resources/js/*.js', gulp.parallel('scripts'));
});


// ТАБЛИЦЫ СТИЛЕЙ SASS/LESS
gulp.task('style', function () {
	styleFiles.forEach(function (styleFile, n, styleFiles) {
		gulp.src(['resources/' + preprocessor + "/" + styleFile + "." + preprocessor])
			.pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
			.pipe(gulpif(preprocessor == 'scss', sass(), less()))
			.pipe(rename({ dirname: '' }))
			.pipe(concat(styleFile + '.css'))
			.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: true
			}))
			.pipe(csso())
			.pipe(gulp.dest('resources/dist'))
			.pipe(browserSync.reload({ stream: true }))
			.pipe(touch())
	});
	gulp.watch('resources/' + preprocessor + '/**/**/*.' + preprocessor, gulp.parallel(['style']));
});


// SCRIPTS
gulp.task('scripts', done => {

	let pathJsFiles = 'resources/js/';
	let ignoreFiles = [];

	scriptFiles.forEach(script => {
		let ignoreFile = '!' + pathJsFiles + script + '.js';
		ignoreFiles.push(ignoreFile);
	})

	gulp.src(['resources/js/*.js', ...ignoreFiles])
		.pipe(concat('common.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('resources/dist'))
		.pipe(browserSync.reload({ stream: true }))



	scriptFiles.forEach(function (scriptFile, n, scriptFiles) {
		gulp.src([pathJsFiles + scriptFile + ".js"])
			.pipe(concat(scriptFile + '.min.js'))
			.pipe(uglify())
			.pipe(gulp.dest('resources/dist'))
			.pipe(browserSync.reload({ stream: true }))
	});

	done();
})

// создание custom gulpfiles
var templateCastomGulpfile =
	`module.exports = {

	// ФАЙЛЫ ЗА КОТОРЫМИ НУЖНО СЛЕДИТЬ (для автоматической перезагрузки страницы)
	watchFiles: ["views/**/**/**/*.+(html|htm)"],
	
	// АВТООБНОВЛЕНИЕ СТИЛЕЙ
	styleHotReload: false,
	
	tasks: function(){
		//var gulp = require('gulp');
		//gulp.task('custom-task',function(){});
		//gulp.start('custom-task');
		return false;
	}
};`;

gulp.task('custom-gulpfile', function (done) {
	fs.writeFile(customFilePath, templateCastomGulpfile, function (err) {
		if (err) {
			return console.log(err);
		}
		console.log("Файл " + customFilePath + " успешно создан!");
	});
	done();
});


// Дефолтный таск
gulp.task('default', gulp.parallel('browser-sync-start', 'style', 'scripts', 'watch-files'), function () {
	return true;
});




// DEPLOY

// BUILD
gulp.task('build', () => {

	const ignoredFiles = [
		'.DS_Store',
		'ftp.example',
		'ftp.js',
		'humans.txt',
		'.git',
		'config/**',
		'dist/**',
		'logs/**',
		'node_modules/**',
		'resources/scss/**',
		'resources/js/**',
		'resources/fonts/**',
		'temp/**',
		'uploaded/**',
		'.gitignore',
		'composer.json',
		'composer.lock',
		'custom-gulpfile.js',
		'gulpfile.js',
		'package-lock.json',
		'package.json',
		'ftp.example'
	]

	return gulp.src(['.*', '*', '*/**'], { base: './', ignore: ignoredFiles })
		.pipe(zip('build.zip'))
		.pipe(gulp.dest('./dist'))
		.on('end', () => console.log('👌 Проект собран'));
});


// FTP
gulp.task('ftp', () => {

	let ftpConfig = require('./ftp.js')

	function getConn() {
		console.log("🌍 Подключение к серверу");
		return ftp.create({ ...ftpConfig });
	}

	const conn = getConn()

	return gulp.src(['./dist/build.zip'], { buffer: false, dot: true })
		.pipe(conn.dest('./www/bb65.ru/'))
		.on('end', () => console.log('👌 Файлы загружены на сервер'));
});

// RUN DEPLOY
gulp.task('deploy', gulp.series('build', 'ftp'), function () {
	return true;
})


