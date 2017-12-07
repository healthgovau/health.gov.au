/***************************************************************************************************************************************************************
 *
 * Generate and compile Sass
 *
 * @repo    - https://github.com/govau/pancake
 * @author  - Dominik Wilkowski
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
exports.Sassify = exports.GenerateSass = exports.GetDependencies = exports.GetPath = undefined;

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _assign = require('babel-runtime/core-js/object/assign');

var _assign2 = _interopRequireDefault(_assign);

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _getIterator2 = require('babel-runtime/core-js/get-iterator');

var _getIterator3 = _interopRequireDefault(_getIterator2);

var _autoprefixer = require('autoprefixer');

var _autoprefixer2 = _interopRequireDefault(_autoprefixer);

var _postcss = require('postcss');

var _postcss2 = _interopRequireDefault(_postcss);

var _nodeSass = require('node-sass');

var _nodeSass2 = _interopRequireDefault(_nodeSass);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _pancake = require('@gov.au/pancake');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Get the include path for a sass partial
 *
 * @param  {string} module       - The module name
 * @param  {object} modules      - An object of all modules and their settings
 * @param  {string} baseLocation - The current base path
 * @param  {string} npmOrg       - The npm org scope
 *
 * @return {string}              - The path to the sass partial
 */
var GetPath = exports.GetPath = function GetPath(module, modules, baseLocation, npmOrg) {
	var modulePath = '';

	var _iteratorNormalCompletion = true;
	var _didIteratorError = false;
	var _iteratorError = undefined;

	try {
		for (var _iterator = (0, _getIterator3.default)(modules), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
			var item = _step.value;

			if (item.name === module) {
				var moduleName = module.replace(npmOrg + '/', '');

				modulePath = _path2.default.normalize(baseLocation + '/' + moduleName + '/' + item.pancake['pancake-module'].sass.path);

				break;
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

	return modulePath;
};

/**
 * Look up all dependencies of a module by calling yourself
 *
 * @param  {string}  module    - The name of the module
 * @param  {object}  modules   - All modules in an object array
 * @param  {string}  parent    - The name of the parent module, Defaults to the module argument
 * @param  {integer} iteration - The depth of the iteration, defaults to 1
 *
 * @return {object}            - An object array of the dependencies that are needed for the module
 */


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var GetDependencies = exports.GetDependencies = function GetDependencies(module, modules) {
	var parent = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : module;
	var iteration = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 1;

	_pancake.Log.verbose('Sass: Looking up dependencies at level ' + _pancake.Style.yellow(iteration));

	var allDependencies = {};

	if (iteration > 50) {
		_pancake.Log.error('Sass: Looks like we found a circular dependency that seems to go no-where from ' + _pancake.Style.yellow(parent) + '.');
	} else {
		var _iteratorNormalCompletion2 = true;
		var _didIteratorError2 = false;
		var _iteratorError2 = undefined;

		try {

			for (var _iterator2 = (0, _getIterator3.default)(modules), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
				var item = _step2.value;

				if (item.name === module) {
					if (item.peerDependencies) {
						var _iteratorNormalCompletion3 = true;
						var _didIteratorError3 = false;
						var _iteratorError3 = undefined;

						try {
							for (var _iterator3 = (0, _getIterator3.default)((0, _keys2.default)(item.peerDependencies)), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
								var subDependency = _step3.value;

								var subDependencies = GetDependencies(subDependency, modules, parent, iteration + 1);

								allDependencies = (0, _assign2.default)(allDependencies, subDependencies);
							}
						} catch (err) {
							_didIteratorError3 = true;
							_iteratorError3 = err;
						} finally {
							try {
								if (!_iteratorNormalCompletion3 && _iterator3.return) {
									_iterator3.return();
								}
							} finally {
								if (_didIteratorError3) {
									throw _iteratorError3;
								}
							}
						}
					}

					allDependencies = (0, _assign2.default)(allDependencies, item.peerDependencies);

					break;
				}
			}
		} catch (err) {
			_didIteratorError2 = true;
			_iteratorError2 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion2 && _iterator2.return) {
					_iterator2.return();
				}
			} finally {
				if (_didIteratorError2) {
					throw _iteratorError2;
				}
			}
		}
	}

	return allDependencies;
};

/**
 * Generate Sass code for a module and it’s dependencies
 *
 * @param  {string} location - The location of the module to be compiled
 * @param  {object} name     - The name of the module
 * @param  {object} modules  - All modules and their dependencies
 * @param  {object} npmOrg   - The name of the npm org scope
 *
 * @return {string}          - Sass code to tie dependencies and module together
 */
var GenerateSass = exports.GenerateSass = function GenerateSass(location, name, modules, npmOrg) {
	var sass = ''; //the code goes here

	var baseLocation = _path2.default.normalize(location + '/../');
	var dependencies = GetDependencies(name, modules);

	_pancake.Log.verbose('Sass: For ' + _pancake.Style.yellow(name) + ' we found the following dependencies ' + _pancake.Style.yellow((0, _stringify2.default)(dependencies)));

	if (dependencies) {
		var _iteratorNormalCompletion4 = true;
		var _didIteratorError4 = false;
		var _iteratorError4 = undefined;

		try {
			for (var _iterator4 = (0, _getIterator3.default)((0, _keys2.default)(dependencies)), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
				var dependency = _step4.value;

				var _modulePath = GetPath(dependency, modules, baseLocation, npmOrg);

				sass += '@import "' + _modulePath + '";\n';
			}
		} catch (err) {
			_didIteratorError4 = true;
			_iteratorError4 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion4 && _iterator4.return) {
					_iterator4.return();
				}
			} finally {
				if (_didIteratorError4) {
					throw _iteratorError4;
				}
			}
		}
	}

	var modulePath = GetPath(name, modules, baseLocation, npmOrg);
	sass += '@import "' + modulePath + '";\n';

	return sass.replace(/\\/g, "\\\\"); // escape path for silly windows
};

/**
 * Compile Sass, autoprefix it and save it to disk
 *
 * @param  {string} location - The path we want to save the compiled css to
 * @param  {object} settings - The SettingsCSS object
 * @param  {string} sass     - The Sass to be compiled
 *
 * @return {promise object}  - Boolean true for 👍 || string error for 👎
 */
var Sassify = exports.Sassify = function Sassify(location, settings, sass) {
	return new _promise2.default(function (resolve, reject) {
		var compiled = _nodeSass2.default.render({
			data: sass,
			indentType: 'tab', //this is how real developers indent!
			outputStyle: settings.minified ? 'compressed' : 'expanded'
		}, function (error, generated) {
			if (error) {
				_pancake.Log.error('Sass compile failed for ' + _pancake.Style.yellow(location));

				reject(error.message);
			} else {
				_pancake.Log.verbose('Sass: Successfully compiled Sass for ' + _pancake.Style.yellow(location));

				(0, _postcss2.default)([(0, _autoprefixer2.default)({ browsers: settings.browsers })]).process(generated.css).catch(function (error) {
					return reject(error);
				}).then(function (prefixed) {
					if (prefixed) {
						prefixed.warnings().forEach(function (warn) {
							return _pancake.Log.error(warn.toString());
						});

						_pancake.Log.verbose('Sass: Successfully autoprefixed CSS for ' + _pancake.Style.yellow(location));

						(0, _pancake.WriteFile)(location, prefixed.css) //write the generated content to file and return its promise
						.catch(function (error) {
							_pancake.Log.error(error);

							reject(error);
						}).then(function () {
							resolve(true);
						});
					}
				});
			}
		});
	});
};