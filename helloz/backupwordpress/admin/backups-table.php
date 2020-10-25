<table class="widefat">

	<thead>

		<tr>

			<th scope="col"><?php hmbkp_backups_number( $schedule ); ?></th>
			<th scope="col"><?php _e( '大小', 'backupwordpress' ); ?></th>
			<th scope="col"><?php _e( '类型', 'backupwordpress' ); ?></th>
			<th scope="col"><?php _e( '动作', 'backupwordpress' ); ?></th>

		</tr>

	</thead>

	<tbody>

		<?php if ( $schedule->get_backups() ) {

			$schedule->delete_old_backups();

			foreach ( $schedule->get_backups() as $file ) {

				if ( ! file_exists( $file ) ) {
					continue;
				}

				hmbkp_get_backup_row( $file, $schedule );

			}

		} else { ?>

			<tr>
				<td class="hmbkp-no-backups" colspan="4"><?php _e( '如果您已经备份，这里将出现您的最近备份。', 'backupwordpress' ); ?></td>
			</tr>

		<?php } ?>

	</tbody>

</table>