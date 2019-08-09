<?php
/**
 * @copyright Copyright (c) 2016 Morris Jobke <hey@morrisjobke.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

return [
	'routes' => [
		['name' => 'flowOperations#getOperations', 'url' => '/operations', 'verb' => 'GET'],
		['name' => 'flowOperations#addOperation', 'url' => '/operations', 'verb' => 'POST'],
		['name' => 'flowOperations#testOperation', 'url' => '/operations/test', 'verb' => 'POST'],
		['name' => 'flowOperations#updateOperation', 'url' => '/operations/{id}', 'verb' => 'PUT'],
		['name' => 'flowOperations#deleteOperation', 'url' => '/operations/{id}', 'verb' => 'DELETE'],
		['name' => 'requestTime#getTimezones', 'url' => '/timezones', 'verb' => 'GET'],
	],
	'ocs-resources' => [
		'global_workflows' => ['url' => '/api/v1/workflows/global'],
	],
];
