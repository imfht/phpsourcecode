/*******************************************************************************
 * Copyright 2019 pf-miles
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.  You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
 * License for the specific language governing permissions and limitations under
 * the License.
 ******************************************************************************/
package com.github.pfmiles.dstier1;

import java.lang.annotation.Documented;
import java.lang.annotation.ElementType;
import java.lang.annotation.Retention;
import java.lang.annotation.RetentionPolicy;
import java.lang.annotation.Target;

/**
 * Specify the execution order of request/response filtering method. A lesser
 * value indicates a greater priority which leads to be executed earlier. A
 * filtering method which is not annotated by this annotation is considered to
 * have a priority of 0.
 * 
 * @author pf-miles
 *
 */
@Documented
@Retention(RetentionPolicy.RUNTIME)
@Target(ElementType.METHOD)
public @interface ExeOrder {
	/**
	 * the priority, lesser means earlier
	 * 
	 * @return the priority
	 */
	int value();
}
