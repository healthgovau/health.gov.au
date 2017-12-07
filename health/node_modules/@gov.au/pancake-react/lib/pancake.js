/***************************************************************************************************************************************************************
 *
 * Plug-in for Pancake
 *
 * Move react files from pancake modules into your pancake folder
 *
 * @repo    - https://github.com/govau/pancake
 * @author  - Alex Page (and Dominik Wilkowski)
 * @license - https://raw.githubusercontent.com/govau/pancake/master/LICENSE (MIT)
 *
 **************************************************************************************************************************************************************/

'use strict';

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Dependencies
//--------------------------------------------------------------------------------------------------------------------------------------------------------------

Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.pancake = undefined;

var _getIterator2 = require('babel-runtime/core-js/get-iterator');

var _getIterator3 = _interopRequireDefault(_getIterator2);

var _typeof2 = require('babel-runtime/helpers/typeof');

var _typeof3 = _interopRequireDefault(_typeof2);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _assign = require('babel-runtime/core-js/object/assign');

var _assign2 = _interopRequireDefault(_assign);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _fs = require('fs');

var _fs2 = _interopRequireDefault(_fs);

var _pancake = require('@gov.au/pancake');

var _react = require('./react');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Module imports
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
_pancake.Log.output = true; //this plugin assumes you run it through pancake


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Plugin export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * The main pancake method for this plugin
 *
 * @param  {array}  version        - The version of mother pancake
 * @param  {array}  modules        - An array of all module objects
 * @param  {object} settings       - An object of the host package.json file and it’s path
 * @param  {object} GlobalSettings - An object of the global settings
 * @param  {object} cwd            - The path to the working directory of our host package.json file
 *
 * @return {Promise object}  - Returns an object of the settings we want to save
 */
var pancake = exports.pancake = function pancake(version, modules, settings, GlobalSettings, cwd) {
	_pancake.Loading.start('pancake-react', _pancake.Log.verboseMode);

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Settings
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var SETTINGS = {
		react: {
			location: 'pancake/react/'
		}
	};

	//merging settings with host settings
	(0, _assign2.default)(SETTINGS.react, settings.react);

	return new _promise2.default(function (resolve, reject) {
		//some housekeeping
		if (typeof version !== 'string') {
			reject('Plugin pancake-react got a mismatch for the data that was passed to it! ' + _pancake.Style.yellow('version') + ' was ' + _pancake.Style.yellow(typeof version === 'undefined' ? 'undefined' : (0, _typeof3.default)(version)) + ' ' + ('but should have been ' + _pancake.Style.yellow('string')));
		}

		if ((typeof modules === 'undefined' ? 'undefined' : (0, _typeof3.default)(modules)) !== 'object') {
			reject('Plugin pancake-react got a mismatch for the data that was passed to it! ' + _pancake.Style.yellow('modules') + ' was ' + _pancake.Style.yellow(typeof modules === 'undefined' ? 'undefined' : (0, _typeof3.default)(modules)) + ' ' + ('but should have been ' + _pancake.Style.yellow('object')));
		}

		if ((typeof settings === 'undefined' ? 'undefined' : (0, _typeof3.default)(settings)) !== 'object') {
			reject('Plugin pancake-react got a mismatch for the data that was passed to it! ' + _pancake.Style.yellow('settings') + ' was ' + _pancake.Style.yellow(typeof settings === 'undefined' ? 'undefined' : (0, _typeof3.default)(settings)) + ' ' + ('but should have been ' + _pancake.Style.yellow('object')));
		}

		if (typeof cwd !== 'string') {
			reject('Plugin pancake-react got a mismatch for the data that was passed to it! ' + _pancake.Style.yellow('cwd') + ' was ' + _pancake.Style.yellow(typeof cwd === 'undefined' ? 'undefined' : (0, _typeof3.default)(cwd)) + ' ' + ('but should have been ' + _pancake.Style.yellow('string')));
		}

		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		// Iterate over each module
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		var reactModules = []; // for collect all promises


		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		// Iterate over each module
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		var _iteratorNormalCompletion = true;
		var _didIteratorError = false;
		var _iteratorError = undefined;

		try {
			for (var _iterator = (0, _getIterator3.default)(modules), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
				var modulePackage = _step.value;

				_pancake.Log.verbose('React: Building ' + _pancake.Style.yellow(modulePackage.name));

				//check if there are react files
				var reactModulePath = void 0;
				if (modulePackage.pancake['pancake-module'].react !== undefined) {
					reactModulePath = _path2.default.normalize(modulePackage.path + '/' + modulePackage.pancake['pancake-module'].react.path);
				}

				if (!_fs2.default.existsSync(reactModulePath)) {
					_pancake.Log.verbose('React: No React found in ' + _pancake.Style.yellow(reactModulePath));
				} else {
					_pancake.Log.verbose('React: ' + _pancake.Style.green('⌘') + ' Found React files in ' + _pancake.Style.yellow(reactModulePath));

					var reactModuleToPath = _path2.default.normalize(cwd + '/' + SETTINGS.react.location + '/' + modulePackage.name.split('/')[1] + '.js');

					//move react file depending on settings
					var reactPromise = (0, _react.HandleReact)(reactModulePath, reactModuleToPath, modulePackage.name + ' v' + modulePackage.version).catch(function (error) {
						_pancake.Log.error(error);
					});

					reactModules.push(reactPromise);
				}
			}
		} catch (err) {
			_didIteratorError = true;
			_iteratorError = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion && _iterator.return) {
					_iterator.return();
				}
			} finally {
				if (_didIteratorError) {
					throw _iteratorError;
				}
			}
		}

		if (modules.length < 1) {
			_pancake.Loading.stop('pancake-react', _pancake.Log.verboseMode); //stop loading animation

			_pancake.Log.info('No pancake modules found \uD83D\uDE2C');
			resolve(SETTINGS);
		} else {

			//after all files have been compiled and written
			_promise2.default.all(reactModules).catch(function (error) {
				_pancake.Loading.stop('pancake-react', _pancake.Log.verboseMode); //stop loading animation

				_pancake.Log.error('React plugin ran into an error: ' + error);
			}).then(function () {
				_pancake.Log.ok('REACT PLUGIN FINISHED');

				_pancake.Loading.stop('pancake-react', _pancake.Log.verboseMode); //stop loading animation
				resolve(SETTINGS);
			});
		}
	});
};