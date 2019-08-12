/**
 * @copyright Copyright (c) 2019 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

export default class ShareSearch {

	#state;

	constructor() {
		// init empty state
		this.#state = {}
		
		// init default values
		this.#state.results = []
		console.debug('OCA.Sharing.ShareSearch initialized')
	}

	/**
	 * Get the sidebar state
	 *
	 * @readonly
	 * @memberof Sidebar
	 * @returns {Object} the data state
	 */
	get state() {
		return this.#state
	}

	/**
	 * Register a new result
	 * 
	 * @param {Object} result
	 * @param {string} [result.user] entry user
	 * @param {string} result.displayName entry first line
	 * @param {string} [result.desc] entry second line 
	 * @param {string} [result.icon] entry icon
	 * @param {function} result.handler function to run on entry selection
	 * @param {function} [result.condition] condition to add entry or not
	 */
	addNewResult(result) {
		if (result.displayName.trim() !== ''
			&& typeof result.handler === 'function') {
			this.#state.results.push(result)
			return true
		}
		console.error(`Invalid search result provided`, result);
		return false
	}
}
