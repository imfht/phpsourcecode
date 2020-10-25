module.exports = {
    root: true,
    parser: 'babel-eslint',
    parserOptions: {
        sourceType: 'module',
    },
    env: {
        browser: true,
    },
    extends: 'airbnb-base',
    // required to lint *.vue files
    plugins: [
        'html',
    ],
    // check if imports actually resolve
    settings: {
        'import/resolver': {
            webpack: {
                config: 'build/webpack.base.conf.js',
            },
        },
    },
    rules: {
        'arrow-parens': [2, 'as-needed', {
            requireForBlockBody: false,
        }],
        'eol-last': 0,
        'guard-for-in': 0,
        indent: ['error', 4, {
            SwitchCase: 1,
        }],
        'import/extensions': ['error', 'always', {
            js: 'never',
        }],
        'import/no-extraneous-dependencies': ['error', {
            optionalDependencies: ['test/unit/index.js'],
        }],
        'no-console': process.env.NODE_ENV === 'production' ? 2 : 0,
        'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
        'no-param-reassign': ['error', {
            props: false,
        }],
        'no-plusplus': ['error', {
            allowForLoopAfterthoughts: true,
        }],
        'no-restricted-syntax': ['error', 'WithStatement', "BinaryExpression[operator='in']"],
    },
};