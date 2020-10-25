<?php


class lesscreator_service
{
    const ExecModeManual     = '1';
    const ExecModeTime       = '2';
    const ExecModeLoop       = '3';
    const ExecModeForever    = '4';

    const ParaModeDebug      = '1';
    const ParaModeServer     = '2';
    const ParaModeDataSingle = '3';
    const ParaModeDataServer = '4';
    const ParaModeDataShard  = '5';

    public static function debugPrint($d)
    {
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }
    
    public static function listAll()
    {
        return array(
            'pagelet'       => 'Pagelet',
            'data'          => 'Database',
            'dataflow'      => 'Dataflow',
        );
    }

    public static function listExecMode()
    {
        return array(
            self::ExecModeManual => 'Execute by manual',
            self::ExecModeTime => 'Execute on a regular time',
            self::ExecModeLoop => 'Execute by loop, after a certain time interval',
            self::ExecModeForever => 'Execute forever, never expires',
        );
    }

    public static function listParaMode()
    {
        return array(
            //ParaModeDebug => 'Develop or Debug, starts a single process',
            self::ParaModeServer => 'Bound to server. Each physical server starts a process',
            self::ParaModeDataSingle => 'Bound to database. Each database starts a process',
            self::ParaModeDataServer => 'Bound to database. Each physical server starts a process',
            self::ParaModeDataShard => 'Bound to database. Each shard node starts a process',
        );
    }
}
