<?php
function swoole_version()
{
}

function swoole_cpu_num()
{
}

function swoole_last_error()
{
}

/**
 * @param $domain_name[required]
 * @param $timeout[optional]
 *
 * @return mixed
 */
function swoole_async_dns_lookup_coro($domain_name, $timeout = null)
{
}

/**
 * @param $settings[required]
 *
 * @return mixed
 */
function swoole_async_set($settings)
{
}

/**
 * @param $func[required]
 * @param $params[optional]
 *
 * @return mixed
 */
function swoole_coroutine_create($func, $params = null)
{
}

/**
 * @param $callback[required]
 *
 * @return mixed
 */
function swoole_coroutine_defer($callback)
{
}

/**
 * @param $read_array[required]
 * @param $write_array[required]
 * @param $error_array[required]
 * @param $timeout[optional]
 *
 * @return mixed
 */
function swoole_client_select($read_array, $write_array, $error_array, $timeout = null)
{
}

/**
 * @param $read_array[required]
 * @param $write_array[required]
 * @param $error_array[required]
 * @param $timeout[optional]
 *
 * @return mixed
 */
function swoole_select($read_array, $write_array, $error_array, $timeout = null)
{
}

/**
 * @param $process_name[required]
 *
 * @return mixed
 */
function swoole_set_process_name($process_name)
{
}

function swoole_get_local_ip()
{
}

function swoole_get_local_mac()
{
}

/**
 * @param $errno[required]
 * @param $error_type[optional]
 *
 * @return mixed
 */
function swoole_strerror($errno, $error_type = null)
{
}

function swoole_errno()
{
}

/**
 * @param $data[required]
 * @param $type[optional]
 *
 * @return mixed
 */
function swoole_hashcode($data, $type = null)
{
}

/**
 * @param $suffix[required]
 * @param $mime_type[required]
 *
 * @return mixed
 */
function swoole_mime_type_add($suffix, $mime_type)
{
}

/**
 * @param $suffix[required]
 * @param $mime_type[required]
 *
 * @return mixed
 */
function swoole_mime_type_set($suffix, $mime_type)
{
}

/**
 * @param $suffix[required]
 *
 * @return mixed
 */
function swoole_mime_type_delete($suffix)
{
}

/**
 * @param $filename[required]
 *
 * @return mixed
 */
function swoole_mime_type_get($filename)
{
}

/**
 * @param $filename[required]
 *
 * @return mixed
 */
function swoole_get_mime_type($filename)
{
}

/**
 * @param $filename[required]
 *
 * @return mixed
 */
function swoole_mime_type_exists($filename)
{
}

function swoole_mime_type_list()
{
}

function swoole_clear_dns_cache()
{
}

function swoole_internal_call_user_shutdown_begin()
{
}

/**
 * @param $func[required]
 *
 * @return mixed
 */
function go($func)
{
}

/**
 * @param $callback[required]
 *
 * @return mixed
 */
function defer($callback)
{
}

/**
 * @param $fd[required]
 * @param $read_callback[required]
 * @param $write_callback[optional]
 * @param $events[optional]
 *
 * @return mixed
 */
function swoole_event_add($fd, $read_callback, $write_callback = null, $events = null)
{
}

/**
 * @param $fd[required]
 *
 * @return mixed
 */
function swoole_event_del($fd)
{
}

/**
 * @param $fd[required]
 * @param $read_callback[optional]
 * @param $write_callback[optional]
 * @param $events[optional]
 *
 * @return mixed
 */
function swoole_event_set($fd, $read_callback = null, $write_callback = null, $events = null)
{
}

/**
 * @param $fd[required]
 * @param $events[optional]
 *
 * @return mixed
 */
function swoole_event_isset($fd, $events = null)
{
}

function swoole_event_dispatch()
{
}

/**
 * @param $callback[required]
 *
 * @return mixed
 */
function swoole_event_defer($callback)
{
}

/**
 * @param $callback[required]
 * @param $before[optional]
 *
 * @return mixed
 */
function swoole_event_cycle($callback, $before = null)
{
}

/**
 * @param $fd[required]
 * @param $data[required]
 *
 * @return mixed
 */
function swoole_event_write($fd, $data)
{
}

function swoole_event_wait()
{
}

function swoole_event_exit()
{
}

/**
 * @param $settings[required]
 *
 * @return mixed
 */
function swoole_timer_set($settings)
{
}

/**
 * @param $ms[required]
 * @param $callback[required]
 *
 * @return mixed
 */
function swoole_timer_after($ms, $callback)
{
}

/**
 * @param $ms[required]
 * @param $callback[required]
 *
 * @return mixed
 */
function swoole_timer_tick($ms, $callback)
{
}

/**
 * @param $timer_id[required]
 *
 * @return mixed
 */
function swoole_timer_exists($timer_id)
{
}

/**
 * @param $timer_id[required]
 *
 * @return mixed
 */
function swoole_timer_info($timer_id)
{
}

function swoole_timer_stats()
{
}

function swoole_timer_list()
{
}

/**
 * @param $timer_id[required]
 *
 * @return mixed
 */
function swoole_timer_clear($timer_id)
{
}

function swoole_timer_clear_all()
{
}
