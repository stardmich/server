<?php
/**
 * @copyright Copyright (c) 2019 Morris Jobke <hey@morrisjobke.de>
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

namespace Test\Route;


use OC\Memcache\ArrayCache;
use OC\Remote\Instance;
use OC\Route\Router;
use OCP\ICache;
use OCP\IConfig;
use OCP\ILogger;
use Test\TestCase;
use Test\Traits\ClientServiceTrait;

class RouterTest extends TestCase {

	public function generateRouteProvider() {
		return [
			['files.view.index', '/index.php/apps/files/'],
			// the OCS route is the prefixed one for the AppFramework - see /ocs/v1.php for routing details
			['ocs.dav.direct.getUrl', '/index.php/ocsapp/apps/dav/api/v1/direct'],
		];
	}

	/**
	 * @dataProvider generateRouteProvider
	 */
	public function testGenerate($routeName, $expected) {
		/** @var ILogger $logger */
		$logger = $this->createMock(ILogger::class);
		$router = new Router($logger);

		$this->assertEquals($expected, $router->generate($routeName));
	}
}
