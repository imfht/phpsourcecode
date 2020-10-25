<?php

// Calculated filesize
$cached = $schedule->is_site_size_cached();

if ( 'database' === $schedule->get_type() ) {
	$cached = true;
}

$filesize = $cached ? '(<code title="' . __( '备份将被压缩，应小于这个。', 'backupwordpress' ) . '">' . esc_attr( $schedule->get_formatted_site_size() ) . '</code>)' : '(<code class="calculating" title="' . __( 'this shouldn\'t take long&hellip;', 'backupwordpress' ) . '">' . __( 'calculating the size of your backup&hellip;', 'backupwordpress' ) . '</code>)';

if ( isset( $_GET['hmbkp_add_schedule'] ) ) {
	$filesize = '';
}

// Backup Type
$type = strtolower( hmbkp_human_get_type( $schedule->get_type() ) );

// Backup Time
$day = date_i18n( 'l', $schedule->get_next_occurrence( false ) );

$next_backup = 'title="' . esc_attr( sprintf( __( '下一次备份将与 %1$s at %2$s %3$s', 'backupwordpress' ), date_i18n( get_option( 'date_format' ), $schedule->get_next_occurrence( false ) ), date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) ), date_i18n( 'T', $schedule->get_next_occurrence( false ) ) ) ) . '"';

// Backup Re-occurrence
switch ( $schedule->get_reoccurrence() ) :

	case 'hmbkp_hourly' :

		$reoccurrence = date_i18n( 'i', $schedule->get_next_occurrence( false ) ) === '00' ? '<span ' . $next_backup . '>' . __( '每小时整', 'backupwordpress' ) . '</span>' : sprintf( __( 'hourly at %s minutes past the hour', 'backupwordpress' ), '<span ' . $next_backup . '>' . intval( date_i18n( 'i', $schedule->get_next_occurrence( false ) ) ) ) . '</span>';

	break;

	case 'hmbkp_daily' :

		$reoccurrence = sprintf( __( '每天在 %s', 'backupwordpress' ), '<span ' . $next_backup . '>' . esc_html( date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) ) ) . '</span>' );

	break;

	case 'hmbkp_twicedaily' :

		$times[] = date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) );
		$times[] = date_i18n( get_option( 'time_format' ), strtotime( '+ 12 hours', $schedule->get_next_occurrence( false ) ) );

		sort( $times );

		$reoccurrence = sprintf( __( '每12小时 %1$s &amp; %2$s', 'backupwordpress' ), '<span ' . $next_backup . '>' . esc_html( reset( $times ) ) . '</span>', '<span>' . esc_html( end( $times ) ) ) . '</span>';

	break;

	case 'hmbkp_weekly' :

		$reoccurrence = sprintf( __( '每周在 %1$s at %2$s', 'backupwordpress' ), '<span ' . $next_backup . '>' .esc_html( $day ) . '</span>', '<span>' . esc_html( date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) ) ) . '</span>' );

	break;

	case 'hmbkp_fortnightly' :

		$reoccurrence = sprintf( __( '双周在 %1$s at %2$s', 'backupwordpress' ), '<span ' . $next_backup . '>' . $day . '</span>', '<span>' . esc_html( date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) ) ) . '</span>' );

	break;

	case 'hmbkp_monthly' :

		$reoccurrence = sprintf( __( '在 %1$s 每个月 %2$s', 'backupwordpress' ), '<span ' . $next_backup . '>' . esc_html( date_i18n( 'jS', $schedule->get_next_occurrence( false ) ) ) . '</span>', '<span>' . esc_html( date_i18n( get_option( 'time_format' ), $schedule->get_next_occurrence( false ) ) ) . '</span>' );

	break;

	case 'manually' :

		$reoccurrence = __( '手动', 'backupwordpress' );

	break;

	default :

		$schedule->set_reoccurrence( 'manually' );

endswitch;

$server = '<span title="' . esc_attr( hmbkp_path() ) . '">' . __( '此服务器', 'backupwordpress' ) . '</span>';
$server = '<code>' . esc_attr( str_replace( $schedule->get_home_path(), '', hmbkp_path() ) ) . '</code>';

// Backup to keep
switch ( $schedule->get_max_backups() ) :

	case 1 :

		$backup_to_keep = sprintf( __( '存储最近的备份 %s', 'backupwordpress' ), $server );

	break;

	case 0 :

		$backup_to_keep = sprintf( __( '不存储任何备份服务器上', 'backupwordpress' ), hmbkp_path() );

	break;

	default :

		$backup_to_keep = sprintf( __( '存储最近的 %1$s 个备份在 %2$s', 'backupwordpress' ), esc_html( $schedule->get_max_backups() ), $server );

endswitch;

$email_msg = $services = '';

foreach ( HMBKP_Services::get_services( $schedule ) as $file => $service ) {

	if ( 'Email' === $service->name ) {
		$email_msg = wp_kses_post( $service->display() );

	} elseif ( $service->is_service_active() ) {
		$services[] = esc_html( $service->display() );

	}

}

if ( ! empty( $services ) && count( $services ) > 1 ) {

	$services[count( $services ) -2] .= ' & ' . $services[count( $services ) -1];

	array_pop( $services );

} ?>

<div class="hmbkp-schedule-sentence<?php if ( $schedule->get_status() ) { ?> hmbkp-running<?php } ?>">

	<?php $sentence = sprintf( _x( '备份我的 %1$s %2$s %3$s, %4$s.', '1: 备份类型 2: 备份文件的总大小 3: 时刻表 4: 存储备份的数目', 'backupwordpress' ), '<span>' . $type . '</span>', $filesize, $reoccurrence, $backup_to_keep );

	if ( $email_msg ) {
		$sentence .= sprintf( __( '%s. ', 'backupwordpress' ), $email_msg );
	}

	if ( $services ) {
		$sentence .= sprintf( __( '发送一个已备份的文件到 %s.', 'backupwordpress' ), implode( ', ', array_filter( $services ) ) );
	}

	echo $sentence; ?>

	<?php if ( HMBKP_Schedules::get_instance()->get_schedule( $schedule->get_id() ) ) {
		hmbkp_schedule_status( $schedule );
	} ?>

	<?php require( HMBKP_PLUGIN_PATH . 'admin/schedule-settings.php' ); ?>

</div>