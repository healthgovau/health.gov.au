/***************************************************************************************************************************************************************
 *
 * Check an module object for conflicts
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
// import Path from 'path';
// import Fs from 'fs';


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------

Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.CheckModules = undefined;

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _slicedToArray2 = require('babel-runtime/helpers/slicedToArray');

var _slicedToArray3 = _interopRequireDefault(_slicedToArray2);

var _getIterator2 = require('babel-runtime/core-js/get-iterator');

var _getIterator3 = _interopRequireDefault(_getIterator2);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _map = require('babel-runtime/core-js/map');

var _map2 = _interopRequireDefault(_map);

var _logging = require('./logging');

var _semver = require('./semver-5-3-0');

var _semver2 = _interopRequireDefault(_semver);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Check modules for conflicts
 *
 * @param  {object} allModules - An object of all modules
 *
 * @return {object}            - An object that can be used to generate an error message
 */
var CheckModules = exports.CheckModules = function CheckModules(allModules) {
	var dependencies = new _map2.default(); //a map we populate with the dependencies of our modules we found
	var modules = new _map2.default(); //a map for all installed modules and their versions

	var result = { //the return object
		conflicts: false, //we always assume the best
		message: '', //a couple message to help understand the shemozzle
		module: '', //the conflict causing module
		dependencies: {} };

	//add all packages into our maps
	var _iteratorNormalCompletion = true;
	var _didIteratorError = false;
	var _iteratorError = undefined;

	try {
		for (var _iterator = (0, _getIterator3.default)(allModules), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
			var modulePackage = _step.value;


			modules.set(modulePackage.name, modulePackage.version); //saving all modules with version for later comparison

			if (modulePackage.peerDependencies !== undefined) {
				dependencies.set(modulePackage.name, modulePackage.peerDependencies); //save the dependencies into the map for later comparison
			}
		}

		//iterate over all dependencies [dependencies] and check against what we have installed [modules]
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

	var _iteratorNormalCompletion2 = true;
	var _didIteratorError2 = false;
	var _iteratorError2 = undefined;

	try {
		for (var _iterator2 = (0, _getIterator3.default)(dependencies), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
			var _step2$value = (0, _slicedToArray3.default)(_step2.value, 2),
			    module = _step2$value[0],
			    moduleDependencies = _step2$value[1];

			_logging.Log.verbose('Checking dependencies for ' + _logging.Style.yellow(module) + ' which are: ' + _logging.Style.yellow((0, _stringify2.default)(moduleDependencies)));

			var _iteratorNormalCompletion3 = true;
			var _didIteratorError3 = false;
			var _iteratorError3 = undefined;

			try {
				for (var _iterator3 = (0, _getIterator3.default)((0, _keys2.default)(moduleDependencies)), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
					var dependency = _step3.value;

					var version = moduleDependencies[dependency]; //the version we require
					var existing = modules.get(dependency); //the version we have

					if (!_semver2.default.satisfies(existing, version) || existing === undefined) {
						(function () {
							//version conflict or not installed at all?
							result.conflicts = true; //we found a conflict
							result.module = dependency; //with this module

							var requires = existing === undefined ? //building error message
							'the module ' + _logging.Style.bold(dependency) + ' but it\u2019s missing.' : _logging.Style.bold(dependency) + ' version ' + _logging.Style.bold(version) + ', however version ' + _logging.Style.bold(existing) + ' was installed.';

							result.message += 'Found conflicting dependenc(ies)\n\n';
							result.message += 'The module ' + _logging.Style.bold(module) + ' requires ' + requires + '\n';

							//let’s look who else depends on this conflicting module

							var otherModules = {};
							var _iteratorNormalCompletion4 = true;
							var _didIteratorError4 = false;
							var _iteratorError4 = undefined;

							try {
								for (var _iterator4 = (0, _getIterator3.default)(dependencies), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
									var _step4$value = (0, _slicedToArray3.default)(_step4.value, 2),
									    subModule = _step4$value[0],
									    subModuleDependencies = _step4$value[1];

									if (subModuleDependencies[dependency] !== undefined) {
										if (otherModules[subModuleDependencies[dependency]] === undefined) {
											otherModules[subModuleDependencies[dependency]] = [];
										}

										otherModules[subModuleDependencies[dependency]].push(subModule); //sort by version
									}
								}

								//sort versions
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

							var otherModulesOrdered = {};
							(0, _keys2.default)(otherModules).sort().forEach(function (key) {
								otherModulesOrdered[key] = otherModules[key];
							});

							result.dependencies = otherModulesOrdered;

							//generate tree
							result.message += '\n\n' + _logging.Style.bold(dependency) + ' is required by the following modules:';

							var _iteratorNormalCompletion5 = true;
							var _didIteratorError5 = false;
							var _iteratorError5 = undefined;

							try {
								for (var _iterator5 = (0, _getIterator3.default)((0, _keys2.default)(otherModulesOrdered)), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
									var key = _step5.value;

									result.message += _logging.Style.bold('\n\n. ' + key);

									for (var i = 0; i < otherModulesOrdered[key].length; i++) {
										result.message += '\n' + (i + 1 == otherModulesOrdered[key].length ? '└' : '├') + '\u2500\u2500 ' + otherModulesOrdered[key][i];
									};
								}
							} catch (err) {
								_didIteratorError5 = true;
								_iteratorError5 = err;
							} finally {
								try {
									if (!_iteratorNormalCompletion5 && _iterator5.return) {
										_iterator5.return();
									}
								} finally {
									if (_didIteratorError5) {
										throw _iteratorError5;
									}
								}
							}

							result.message += '\n\nTo fix this issue make sure all your modules require the same version.';

							//suggestion...
							if ((0, _keys2.default)(otherModules).length == 1) {
								result.message += '/nMaybe upgrade the ' + _logging.Style.bold(dependency) + ' module.';
							}
						})();
					}
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

	_logging.Log.verbose('Result of checking:\n' + _logging.Style.yellow((0, _stringify2.default)(result)));

	return result;
};