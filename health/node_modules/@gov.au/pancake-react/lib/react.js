/***************************************************************************************************************************************************************
 *
 * Move react files
 *
 * @repo    - https://github.com/govau/pancake
 * @author  - Dominik Wilkowski and Alex Page
 * @license - https://raw.githubusercontent.com/govau/pancake/master/LICENSE (MIT)
 *
 **************************************************************************************************************************************************************/

'use strict';

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------

Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.HandleReact = undefined;

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _pancake = require('@gov.au/pancake');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Get react file from module and write to disk
 *
 * @param  {string} from     - Where is the module so we can read from there
 * @param  {string} to       - Where shall we write the module to
 * @param  {string} tag      - The tag to be added to the top of the file
 *
 * @return {promise object}  - The js code either minified or bare bone
 */
var HandleReact = exports.HandleReact = function HandleReact(from, to, tag) {
	return new _promise2.default(function (resolve, reject) {
		(0, _pancake.ReadFile)(from) //read the module
		.catch(function (error) {
			_pancake.Log.error('Unable to read file ' + _pancake.Style.yellow(from));
			_pancake.Log.error(error);

			reject(error);
		}).then(function (code) {
			return (0, _pancake.WriteFile)(to, code) //write the generated content to file and return its promise
			.catch(function (error) {
				_pancake.Log.error(error);

				reject(error);
			}).then(function () {
				resolve(code);
			});
		});
	});
};