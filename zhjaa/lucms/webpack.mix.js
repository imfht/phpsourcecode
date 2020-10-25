let mix = require('laravel-mix')

mix.js('resources/assets/js/app.js', 'public/js').sass('resources/assets/sass/app.scss', 'public/css')

mix.scripts(['resources/assets/js/main.js'], 'public/js/jquery.js')

mix.webpackConfig({
    resolve: {
        alias: {
            'components': 'assets/js/components'
        },
        modules: [
            'node_modules',
            path.resolve(__dirname, 'resources')
        ]
    }
});
