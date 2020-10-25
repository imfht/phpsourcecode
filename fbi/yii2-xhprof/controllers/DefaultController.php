<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace fbi\xhprof\controllers;

use fbi\xhprof\lib\Graphic;
use fbi\xhprof\lib\Helper;
use fbi\xhprof\lib\Html;
use fbi\xhprof\lib\XHProfRuns_Default;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Debugger controller
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout = 'main';

    public function actionIndex()
    {
		$params = array(
			'run' => array(Helper::XHPROF_STRING_PARAM, ''),
			'wts' => array(Helper::XHPROF_STRING_PARAM, ''),
			'symbol' => array(Helper::XHPROF_STRING_PARAM, ''),
			'sort' => array(Helper::XHPROF_STRING_PARAM, 'wt'), // wall time
			'run1' => array(Helper::XHPROF_STRING_PARAM, ''),
			'run2' => array(Helper::XHPROF_STRING_PARAM, ''),
			'source' => array(Helper::XHPROF_STRING_PARAM, 'xhprof'),
			'all' => array(Helper::XHPROF_UINT_PARAM, 0),
		);
		Helper::xhprof_param_init($params);
		foreach ($params as $k => $v) {
			$params[$k] = Helper::$params[$k];
			// unset key from params that are using default values. So URLs aren't
			// ridiculously long.
			if ($params[$k] == $v[1]) {
				unset($params[$k]);
			}
		}

		$xhprof_runs_impl = new XHProfRuns_Default($this->module->dir);

		$html=Html::displayXHProfReport($xhprof_runs_impl, $params,
			Helper::$params['source'], Helper::$params['run'], Helper::$params['wts'],
			Helper::$params['symbol'], Helper::$params['sort'], Helper::$params['run1'],
			Helper::$params['run2']);
		return $this->render('index',['html'=>$html]);
    }

	public function actionGraphic(){
		ini_set('max_execution_time', 100);
		$params = array(// run id param
			'run' => array(Helper::XHPROF_STRING_PARAM, ''),
			// source/namespace/type of run
			'source' => array(Helper::XHPROF_STRING_PARAM, 'xhprof'),
			// the focus function, if it is set, only directly
			// parents/children functions of it will be shown.
			'func' => array(Helper::XHPROF_STRING_PARAM, ''),
			// image type, can be 'jpg', 'gif', 'ps', 'png'
			'type' => array(Helper::XHPROF_STRING_PARAM, 'png'),
			// only functions whose exclusive time over the total time
			// is larger than this threshold will be shown.
			// default is 0.01.
			'threshold' => array(Helper::XHPROF_FLOAT_PARAM, 0.01),
			// whether to show critical_path
			'critical' => array(Helper::XHPROF_BOOL_PARAM, true),
			// first run in diff mode.
			'run1' => array(Helper::XHPROF_STRING_PARAM, ''),
			// second run in diff mode.
			'run2' => array(Helper::XHPROF_STRING_PARAM, '')
		);

		// pull values of these params, and create named globals for each param
		Helper::xhprof_param_init($params);
		// if invalid value specified for threshold, then use the default
		if (Helper::$params['threshold'] < 0 || Helper::$params['threshold'] > 1) {
			Helper::$params['threshold'] = $params['threshold'][1];
		}

		// if invalid value specified for type, use the default
		if (!array_key_exists(Helper::$params['type'], Graphic::$xhprof_legal_image_types)) {
			Helper::$params['type'] = $params['type'][1]; // default image type.
		}

		$xhprof_runs_impl = new XHProfRuns_Default($this->module->dir);

		if (!empty(Helper::$params['run'])) {
			// single run call graph image generation
			$html=Graphic::xhprof_render_image($xhprof_runs_impl,
				Helper::$params['run'],Helper::$params['type'],Helper::$params['threshold'],
				Helper::$params['func'],Helper::$params['source'],Helper::$params['critical']
			);
		} else {
			// diff report call graph image generation
			$html=Graphic::xhprof_render_diff_image($xhprof_runs_impl,
				Helper::$params['run1'],Helper::$params['run2'],Helper::$params['type'],
				Helper::$params['threshold'],Helper::$params['source']
			);
		}
		return $this->render('index',['html'=>$html]);
	}

	public function actionTypehead(){
		$xhprof_runs_impl = new XHProfRuns_Default($this->module->dir);
		$params = array(
			'q'          => array(Helper::XHPROF_STRING_PARAM, ''),
			'run'        => array(Helper::XHPROF_STRING_PARAM, ''),
			'run1'       => array(Helper::XHPROF_STRING_PARAM, ''),
			'run2'       => array(Helper::XHPROF_STRING_PARAM, ''),
			'source'     => array(Helper::XHPROF_STRING_PARAM, 'xhprof'),
		);

// pull values of these params, and create named globals for each param
		Helper::xhprof_param_init($params);

		if (!empty(Helper::$params['run'])) {

			// single run mode
			$raw_data = $xhprof_runs_impl->get_run(Helper::$params['run'], Helper::$params['source'], $desc_unused);
			$functions = Helper::xhprof_get_matching_functions(Helper::$params['q'], $raw_data);

		} else if (!empty(Helper::$params['run1']) && !empty(Helper::$params['run2'])) {

			// diff mode
			$raw_data = $xhprof_runs_impl->get_run(Helper::$params['run1'], Helper::$params['source'], $desc_unused);
			$functions1 = Helper::xhprof_get_matching_functions(Helper::$params['q'], $raw_data);

			$raw_data = $xhprof_runs_impl->get_run(Helper::$params['run2'], Helper::$params['source'], $desc_unused);
			$functions2 = Helper::xhprof_get_matching_functions(Helper::$params['q'], $raw_data);


			$functions = array_unique(array_merge($functions1, $functions2));
			asort($functions);
		} else {
			Helper::xhprof_error("no valid runs specified to typeahead endpoint");
			$functions = array();
		}

// If exact match is present move it to the front
		if (in_array(Helper::$params['q'], $functions)) {
			$old_functions = $functions;

			$functions = array(Helper::$params['q']);
			foreach ($old_functions as $f) {
				// exact match case has already been added to the front
				if ($f != Helper::$params['q']) {
					$functions[] = $f;
				}
			}
		}

		foreach ($functions as $f) {
			echo $f."\n";
		}
		return '';
	}

	public function actionDelall(){
		$xhprof_runs_impl = new XHProfRuns_Default($this->module->dir);
		$xhprof_runs_impl->deleteAll();
		$this->redirect(Url::to(['index']));
	}

	public function actionDel(){
		$xhprof_runs_impl = new XHProfRuns_Default($this->module->dir);
		$run = Yii::$app->request->get('run');
		$source=Yii::$app->request->get('source');
		$xhprof_runs_impl->delete($run,$source);
		$this->redirect(Url::to(['index']));
	}
}
