const { series, parallel, src, dest, watch } = require("gulp");
const clean = require("gulp-clean");
const sass = require("gulp-sass");
const rename = require("gulp-rename");
const postcss = require("gulp-postcss");

const paths = {
	sass: {
		src: "src/scss/*.scss",
		dest: "assets/css",
	},
	postcss: {
		src: "src/postcss/*.postcss",
		dest: "assets/css",
	},
};

function cleanDirs() {
	return src("assets").pipe(clean({ allowEmpty: true }));
}

function compileSass() {
	return src(paths.sass.src)
		.pipe(sass())
		.pipe(
			rename({
				extname: ".css",
			})
		)
		.pipe(dest(paths.sass.dest));
}

function watchSass() {
	return watch(paths.sass.src, compileSass);
}

function compilePostcss() {
	return src(paths.postcss.src)
		.pipe(postcss())
		.pipe(
			rename({
				extname: ".css",
			})
		)
		.pipe(dest(paths.postcss.dest));
}

function watchPostcss() {
	return watch(paths.postcss.src, compilePostcss);
}

exports.watch = parallel(watchSass, watchPostcss);
exports.default = series(cleanDirs, parallel(compileSass, compilePostcss));
