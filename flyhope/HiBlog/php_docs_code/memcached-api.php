<?php

/**
 * Memcached class.
 */

class Memcached {
	/**
	 * Libmemcached behavior options.
	 */

	const OPT_HASH = 0;
	
	const OPT_HASH_DEFAULT = 0;

	const HASH_MD5 = 0;

	const HASH_CRC = 0;
	
	const HASH_FNV1_64 = 0;

	const HASH_FNV1A_64 = 0;

	const HASH_FNV1_32 = 0;

	const HASH_FNV1A_32 = 0;

	const HASH_HSIEH = 0;

	const HASH_MURMUR = 0;

	const OPT_DISTRIBUTION = 0;

	const DISTRIBUTION_MODULA = 0;

	const DISTRIBUTION_CONSISTENT = 0;

	const LIBKETAMA_COMPATIBLE = 0;

	const OPT_BUFFER_REQUESTS = 0;

	const OPT_BINARY_PROTOCOL = 0;

	const OPT_NO_BLOCK = 0;

	const OPT_TCP_NODELAY = 0;

	const OPT_SOCKET_SEND_SIZE = 0;

	const OPT_SOCKET_RECV_SIZE = 0;

	const OPT_CONNECT_TIMEOUT = 0;

	const OPT_RETRY_TIMEOUT = 0;

	const OPT_SND_TIMEOUT = 0;

	const OPT_RCV_TIMEOUT = 0;

	const OPT_POLL_TIMEOUT = 0;

	const OPT_SERVER_FAILURE_LIMIT = 0;

	const OPT_CACHE_LOOKUPS = 0;

	const OPT_AUTO_EJECT_HOSTS = 0;

	const OPT_NUMBER_OF_REPLICAS = 0;

	const OPT_NOREPLY = 0;

	const OPT_VERIFY_KEY = 0;
	
	const OPT_RANDOMIZE_REPLICA_READS = 0;


	/**
	 * Class parameters
	 */
	const HAVE_JSON = 0;

	const HAVE_IGBINARY = 0;

	/**
	 * Class options.
	 */
	const OPT_COMPRESSION = 0;

	const OPT_COMPRESSION_TYPE = 0;

	const OPT_PREFIX_KEY = 0;

	/**
	 * Serializer constants
	 */
	const SERIALIZER_PHP = 0;

	const SERIALIZER_IGBINARY = 0;

	const SERIALIZER_JSON = 0;

	const SERIALIZER_JSON_ARRAY = 0;

	/**
	 * Compression types
	 */
	const COMPRESSION_TYPE_FASTLZ = 0;

	const COMPRESSION_TYPE_ZLIB = 0;

	/**
	 * Flags
	 */
	const GET_PRESERVE_ORDER = 0;

	/**
	 * Return values
	 */
	const GET_ERROR_RETURN_VALUE = 0;

	const RES_PAYLOAD_FAILURE = 0;

	const RES_SUCCESS = 0;

	const RES_FAILURE = 0;

	const RES_HOST_LOOKUP_FAILURE = 0;

	const RES_UNKNOWN_READ_FAILURE = 0;

	const RES_PROTOCOL_ERROR = 0;

	const RES_CLIENT_ERROR = 0;

	const RES_SERVER_ERROR = 0;

	const RES_WRITE_FAILURE = 0;

	const RES_DATA_EXISTS = 0;

	const RES_NOTSTORED = 0;

	const RES_NOTFOUND = 0;

	const RES_PARTIAL_READ = 0;

	const RES_SOME_ERRORS = 0;

	const RES_NO_SERVERS = 0;

	const RES_END = 0;

	const RES_ERRNO = 0;

	const RES_BUFFERED = 0;

	const RES_TIMEOUT = 0;

	const RES_BAD_KEY_PROVIDED = 0;

	const RES_STORED = 0;

	const RES_DELETED = 0;

	const RES_STAT = 0;

	const RES_ITEM = 0;

	const RES_NOT_SUPPORTED = 0;

	const RES_FETCH_NOTFINISHED = 0;

	const RES_SERVER_MARKED_DEAD = 0;

	const RES_UNKNOWN_STAT_KEY = 0;

	const RES_INVALID_HOST_PROTOCOL = 0;

	const RES_MEMORY_ALLOCATION_FAILURE = 0;

	const RES_CONNECTION_SOCKET_CREATE_FAILURE = 0;


	public function __construct( $persistent_id = '', $on_new_object_cb = null ) {}
	
	public function get( $key, $cache_cb = null, &$cas_token = null ) {}

	public function getByKey( $server_key, $key, $cache_cb = null, &$cas_token = null ) {}

	public function getMulti( array $keys, &$cas_tokens = null, $flags = 0 ) {}

	public function getMultiByKey( $server_key, array $keys, &$cas_tokens = null, $flags = 0 ) {}

	public function getDelayed( array $keys, $with_cas = null, $value_cb = null ) {}

	public function getDelayedByKey( $server_key, array $keys, $with_cas = null, $value_cb = null ) {}

	public function fetch( ) {}
	
	public function fetchAll( ) {}

	public function set( $key, $value, $expiration = 0 ) {}

    public function touch( $key, $expiration = 0 ) {}

    public function touchbyKey( $key, $expiration = 0 ) {}

	public function setByKey( $server_key, $key, $value, $expiration = 0 ) {}

	public function setMulti( array $items, $expiration = 0 ) {}

	public function setMultiByKey( $server_key, array $items, $expiration = 0 ) {}

	public function cas( $token, $key, $value, $expiration = 0 ) {}

	public function casByKey( $token, $server_key, $key, $value, $expiration = 0 ) {}

	public function add( $key, $value, $expiration = 0 ) {}

	public function addByKey( $server_key, $key, $value, $expiration = 0 ) {}

	public function append( $key, $value ) {}

	public function appendByKey( $server_key, $key, $value ) {}

	public function prepend( $key, $value ) {}

	public function prependByKey( $server_key, $key, $value ) {}

	public function replace( $key, $value, $expiration = 0 ) {}

	public function replaceByKey( $server_key, $key, $value, $expiration = 0 ) {}

	public function delete( $key, $time = 0 ) {}

	public function deleteByKey( $server_key, $key, $time = 0 ) {}

	public function deleteMulti( array $keys, $expiration = 0 ) {}

	public function deleteMultiByKey( $server_key, array $keys, $expiration = 0 ) {}

	public function increment( $key, $offset = 1) {}

	public function decrement( $key, $offset = 1) {}

	public function getOption( $option ) {}
	
	public function setOption( $option, $value ) {}

	public function setOptions( array $options ) {}

	public function addServer( $host, $port,  $weight = 0 ) {}

	public function addServers( array $servers ) {}

	public function getServerList( ) {}

	public function getServerByKey( $server_key ) {}

	public function flush( $delay = 0 ) {}

	public function getStats( ) {}
	
	public function getVersion( ) {}

	public function getResultCode( ) {}

	public function getResultMessage( ) {}

	public function isPersistent( ) {}

	public function isPristine( ) {}

}

class MemcachedException extends Exception {

	function __construct( $errmsg = "", $errcode  = 0 ) {}

}
