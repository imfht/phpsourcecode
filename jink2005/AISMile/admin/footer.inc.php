<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

echo '			</div>
			</div>
			'.Hook::exec('displayBackOfficeFooter').'
			<div id="footer">
				<div style="float:left;margin-left:10px;padding-top:6px">
					<a href="http://www.milebiz.com/" target="_blank" style="font-weight:700;color:#666666">MileBiz&trade; '._MB_VERSION_.'</a><br />
					<span style="font-size:10px">'.translate('Load time:').' '.number_format(microtime(true) - $timerStart, 3, '.', '').'s</span>
				</div>
				<div style="float:right;height:40px;margin-right:10px;line-height:38px;vertical-align:middle">';
if (strtoupper(Context::getContext()->language->iso_code) == 'FR') echo '<span style="color: #812143; font-weight: bold;">Questions / Renseignements / Formations :</span> <strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h ';

echo '				| <a href="http://www.milebiz.com/contact_us/" target="_blank" class="footer_link">'.translate('Contact').'</a>
					| <a href="http://forge.milebiz.com" target="_blank" class="footer_link">'.translate('Bug Tracker').'</a>
					| <a href="http://bbs.milebiz.com" target="_blank" class="footer_link">'.translate('Forum').'</a>	
				</div>
			</div>
		</div>
	</div>';

// FrontController::disableParentCalls();
// $fc = new FrontController();
// $fc->displayFooter();

echo '
	</body>
</html>';

