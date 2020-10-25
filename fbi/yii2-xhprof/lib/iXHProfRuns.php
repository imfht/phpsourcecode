<?php
/**
 * Created by PhpStorm.
 * User: Simonx
 * Date: 2015/4/20
 * Time: 18:17
 */

namespace fbi\xhprof\lib;

//
//  Copyright (c) 2009 Facebook
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

//
// This file defines the interface iXHProfRuns and also provides a default
// implementation of the interface (class XHProfRuns).
//

/**
 * iXHProfRuns interface for getting/saving a XHProf run.
 *
 * Clients can either use the default implementation,
 * namely XHProfRuns_Default, of this interface or define
 * their own implementation.
 *
 * @author Kannan
 */
interface iXHProfRuns {

	/**
	 * Returns XHProf data given a run id ($run) of a given
	 * type ($type).
	 *
	 * Also, a brief description of the run is returned via the
	 * $run_desc out parameter.
	 */
	public function get_run($run_id, $type, &$run_desc);

	/**
	 * Save XHProf data for a profiler run of specified type
	 * ($type).
	 *
	 * The caller may optionally pass in run_id (which they
	 * promise to be unique). If a run_id is not passed in,
	 * the implementation of this method must generated a
	 * unique run id for this saved XHProf run.
	 *
	 * Returns the run id for the saved XHProf run.
	 *
	 */
	public function save_run($xhprof_data, $type, $run_id = null);
}
