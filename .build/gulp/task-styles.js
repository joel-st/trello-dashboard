import { src, dest } from 'gulp';

import cleanCSS from 'gulp-clean-css';
import sass from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import rename from 'gulp-rename';
import sourcemaps from 'gulp-sourcemaps';

export const task = config => {
    return (
        src(config.assetsBuild + 'styles/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(
            sass({
                includePaths: ['./node_modules/'],
            }).on('error', sass.logError)
        )
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer())
        .pipe(dest(config.assetsDir + 'styles/'))
        .pipe(sourcemaps.write('.'))
        .on('error', config.errorLog)
        // minify
        .pipe(cleanCSS())
        .pipe(
            rename({
                suffix: '.min',
            })
        )
        .on('error', config.errorLog)
        .pipe(dest(config.assetsDir + 'styles/'))
    );
};