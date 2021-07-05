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


////////////////////////////////////////////
// Экспорт модулей
const fs = require('fs');

var gulp = require('gulp'),
	browserSync = require('browser-sync'),
	watch = require('gulp-watch'),
	sass = require('gulp-sass'),
	less = require('gulp-less'),
	autoprefixer = require('gulp-autoprefixer'),
	rename  = require('gulp-rename'),
	concat = require('gulp-concat'),
	gulpif = require('gulp-if'),
	del = require('del'),
	plumber = require('gulp-plumber'),
	touch = require('gulp-touch-cmd'),
	notify = require("gulp-notify"),
	csso = require('gulp-csso');



var customFilePath = "./custom-gulpfile.js";	// путь к файлу индивидуальной настройки
var styleFiles = ['styles','tinymce'];		// файлы стилей для препроцессора для отдельной сборки

if(fs.existsSync(customFilePath)){
	var customGulpfile = require(customFilePath);
}else{
	customGulpfile = {
		watchFiles: ["views/**/**/**/*.+(html|htm)"], // ФАЙЛЫ ЗА КОТОРЫМИ НУЖНО СЛЕДИТЬ (для автоматической перезагрузки страницы)
		styleHotReload: false, 	// АВТООБНОВЛЕНИЕ СТИЛЕЙ
		tasks: function(){return false;}
	}
}

// Удаляет ненужную папку стилей
gulp.task('new-project', function(done){
	let del_preprocessor = "";
	if(preprocessor == 'scss'){
		del_preprocessor = 'less';
	}
	else{
		del_preprocessor = 'scss';
	};

	del.sync(['resources/' + del_preprocessor]);

	done();
});

// Перезагрузка страницы после изменения исходных файлов
gulp.task('browser-sync-start', function(done){
	browserSync.init({
		proxy: "http://" + nameProject + ".web",
		//proxy: { target: nameProject + ".web"},
		notify: true,
		open: false
	});
	if(customGulpfile.styleHotReload){
		gulp.watch(customGulpfile.watchFiles, gulp.parallel(['browser-sync-reload']));
	}
	done();
	
});


gulp.task('browser-sync-reload', function(){
	browserSync.reload();
	gulp.watch(customGulpfile.watchFiles, gulp.parallel(['browser-sync-reload']));
});

// ТАБЛИЦЫ СТИЛЕЙ SASS/LESS
gulp.task('style', function(){
	styleFiles.forEach(function(styleFile, n, styleFiles){
		gulp.src(['resources/' + preprocessor + "/" + styleFile + "." + preprocessor])
			.pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
			.pipe(gulpif(preprocessor == 'scss', sass(), less()))
			.pipe(rename({dirname: ''}))
			.pipe(concat(styleFile + '.css'))
			.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: true
			}))
			.pipe(csso())
			.pipe(gulp.dest('resources/css'))
			.pipe(browserSync.reload({stream:true}))
			.pipe(touch())
	});
	gulp.watch('resources/' + preprocessor + '/**/**/*.' + preprocessor, gulp.parallel(['style']));
});

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

gulp.task('custom-gulpfile', function(done){
	fs.writeFile(customFilePath, templateCastomGulpfile, function(err) {
	    if(err) {
	        return console.log(err);
	    }
	    console.log("Файл " + customFilePath + " успешно создан!");
	});
	done();
});


// Дефолтный таск
gulp.task('default', gulp.parallel('browser-sync-start', 'style'), function(){
	return true;
}); 
