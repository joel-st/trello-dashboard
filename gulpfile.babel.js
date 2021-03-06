import gulp from 'gulp';

const config = {
    name: 'TVP Trello Dashbaord',
    key: 'tvp-trello-dashboard',
    assetsDir: 'assets/',
    gulpDir: './.build/gulp/',
    assetsBuild: '.build/assets/',
    errorLog: function (error) {
        console.log('\x1b[31m%s\x1b[0m', error);
        if(this.emit) {
            this.emit('end');
        }
    },
};

import { task as taskStyles } from './.build/gulp/task-styles';
import { task as taskScripts } from './.build/gulp/task-scripts';

export const styles = () => taskStyles(config);
export const scripts = () => taskScripts(config);
export const watch = () => {
    const settings = { usePolling: true, interval: 100 };

    gulp.watch(config.assetsBuild + 'styles/**/*.scss', settings, gulp.series(styles));
    gulp.watch(config.assetsBuild + 'scripts/**/*.{scss,css,js}', settings, gulp.series(scripts));
};

export const taskDefault = gulp.series(gulp.parallel(styles, scripts), watch);
export default taskDefault;