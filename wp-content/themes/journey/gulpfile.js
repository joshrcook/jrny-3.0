const { series, parallel, src, dest, watch } = require("gulp");
const clean = require("gulp-clean");
const sass = require("gulp-sass");
const rename = require("gulp-rename");
const postcss = require("gulp-postcss");
const del = require('del');

const paths = {
	sass: {
		src: "src/scss/*.scss",
		dest: "assets/css",
	},
	postcss: {
		src: {
			static: ["src/postcss/tailwind.postcss"],
			watch: ['src/postcss/styles.postcss'],
		},
		dest: "assets/css",
	},
};

function cleanDirs() {
	return del([
		'assets/**/*'
	]);
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

const compilePostcss = (srcPath = [...paths.postcss.src.static, ...paths.postcss.src.watch]) => () => {
	return src(srcPath)
		.pipe(postcss())
		.pipe(
			rename({
				extname: ".css",
			})
		)
		.pipe(dest(paths.postcss.dest));
}

function watchPostcss() {
	return watch(paths.postcss.src.watch, compilePostcss(paths.postcss.src.watch));
}

exports.watch = parallel(watchSass, watchPostcss);
exports.default = series(cleanDirs, parallel(compileSass, compilePostcss()));
