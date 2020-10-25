<?php require_once HMBKP_PLUGIN_PATH . 'classes/class-requirements.php'; ?>

<h2><?php _e( 'BackUpWordPress支持', 'backupwordpress' ); ?></h2>

<p class="howto"><?php printf( __( 'BackUpWordPress 使用 %s 提供支持。除了允许你发送和接收消息并发送以下服务器信息连同你的请求：', 'backupwordpress' ), '<a target="blank" href="https://www.intercom.io">Intercom</a>' ); ?></p>

<div class="server-info">

<?php foreach ( HMBKP_Requirements::get_requirement_groups() as $group ) : ?>

	<table class="fixed widefat">

		<thead>
			<tr>
				<th scope="col" colspan="2"><?php echo ucwords( $group ); ?></th>
			</tr>
		</thead>

		<tbody>

		<?php foreach ( HMBKP_Requirements::get_requirements( $group ) as $requirement ) : ?>

			<?php if ( ( is_string( $requirement->raw_result() ) && strlen( $requirement->result() ) < 20 ) || is_bool( $requirement->raw_result() ) ) { ?>

			<tr>

				<td><?php echo esc_html( $requirement->name() ); ?></td>

				<td>
					<code><?php echo esc_html( $requirement->result() ); ?></code>
				</td>

			</tr>

			<?php } else { ?>

			<tr>

				<td colspan="2">
					<?php echo esc_html( $requirement->name() ); ?>
					<pre><?php echo esc_html( $requirement->result() ); ?></pre>
				</td>

			</tr>

			<?php } ?>

		<?php endforeach; ?>

		</tbody>

	</table>

<?php endforeach; ?>

</div>

<p class="howto"><?php _e( '您可以禁用支持，在BackUpWordPress设置里', 'backupwordpress' ); ?></p>

<a href="#" class="button-secondary hmbkp-colorbox-close"><?php _e( '不，谢谢！', 'backupwordpress' ); ?></a>
<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'hmbkp_request_enable_support' ), admin_url( 'admin-post.php' ) ), 'hmbkp_enable_support', 'hmbkp_enable_support_nonce' ) ); ?>" class="button-primary right"><?php _e( '是的我想支持', 'backupwordpress' ); ?></a>