<?php
if ( defined( 'THINK_VERSION' ) ) {
    \think\Route::get( 'doc$', function () {
        return redirect( '/doc/document?name=explain' );
    } );
    \think\Route::get( 'doc/assets', "\\OkCoder\\ApiDoc\\DocController@assets", [ 'deny_ext' => 'php|.htacess' ] );
    \think\Route::get( 'doc/module', "\\OkCoder\\ApiDoc\\Controller@module" );
    \think\Route::get( 'doc/action', "\\OkCoder\\ApiDoc\\Controller@action" );
    \think\Route::get( 'doc/document', "\\OkCoder\\ApiDoc\\\Controller@document" );
    \think\Route::any( 'doc/login$', "\\OkCoder\\ApiDoc\\Controller@login" );
    \think\Route::any( 'doc/format_params', "\\OkCoder\\ApiDoc\\Controller@format_params" );
} else {
    Route::any( 'doc$', function () {
        return redirect( '/doc/document?name=explain' );
    } );
    Route::get( 'doc/assets', "\\OkCoder\\ApiDoc\\Controller@assets", [ 'deny_ext' => 'php|.htacess' ] );
    Route::get( 'doc/module', "\\OkCoder\\ApiDoc\\Controller@module" );
    Route::get( 'doc/action', "\\OkCoder\\ApiDoc\\Controller@action" );
    Route::get( 'doc/document', "\\OkCoder\\ApiDoc\\Controller@document" );
    Route::any( 'doc/login$', "\\OkCoder\\ApiDoc\\Controller@login" );
    Route::any( 'doc/format_params', "\\OkCoder\\ApiDoc\\Controller@format_params" );

}

# 当前URL
if ( !function_exists( "get_url" ) ) {
    function get_url()
    {
        $sys_protocal = isset( $_SERVER[ 'SERVER_PORT' ] ) && $_SERVER[ 'SERVER_PORT' ] == '443' ? 'https://' : 'http://';
        $php_self     = $_SERVER[ 'PHP_SELF' ] ? $_SERVER[ 'PHP_SELF' ] : $_SERVER[ 'SCRIPT_NAME' ];
        $path_info    = isset( $_SERVER[ 'PATH_INFO' ] ) ? $_SERVER[ 'PATH_INFO' ] : '';
        $relate_url   = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : $php_self . ( isset( $_SERVER[ 'QUERY_STRING' ] ) ? '?' . $_SERVER[ 'QUERY_STRING' ] : $path_info );
        return $sys_protocal . ( isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '' ) . $relate_url;
    }
}

# 驼峰转下划线
if ( !function_exists( "hump_to_line" ) ) {
    function hump_to_line( $str )
    {
        $str = preg_replace_callback( '/([A-Z]{1})/', function ( $matches ) {
            return '_' . strtolower( $matches[ 0 ] );
        }, $str );
        return $str;
    }
}

# curl $data有值是是post
if ( !function_exists( "http_curl" ) ) {

    /**
     * curl模拟请求方法
     * @param $url
     * @param $cookie
     * @param array $data
     * @param $method
     * @param array $headers
     * @return mixed
     */
    function http_curl( $url, $cookie, $data = array(), $method = 'GET', $headers = array() )
    {
        $curl = curl_init();
        if ( $data && count( $data ) && $method == "GET" ) {
            $data = array_filter( $data );
            $url  .= "?" . http_build_query( $data );
            $url  = str_replace( array( '%5B0%5D' ), array( '[]' ), $url );
        }
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
        if ( $headers ) {
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        }
        $method = strtoupper( $method );
        switch ( $method ) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
                break;
            case 'PUT':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
                break;
            case 'DELETE':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                break;
        }
        if ( !empty( $cookie ) ) {
            curl_setopt( $curl, CURLOPT_COOKIE, $cookie );
        }
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        $output = curl_exec( $curl );
        curl_close( $curl );
        return json_decode( $output, true );
    }
}

