/***************************************************************************************************************************************************************
 *
 * Plug-in for Pancake
 *
 * Move and compile Sass partials.
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

var _helpers = require('./helpers');

var _sass = require('./sass');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Module imports
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var pancake = exports.pancake = function pancake(version, modules, settings, GlobalSettings, cwd) {
	_pancake.Loading.start('pancake-sass', _pancake.Log.verboseMode);

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Settings
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var SETTINGS = {
		css: {
			minified: true,
			modules: false,
			browsers: ['last 2 versions', 'ie 8', 'ie 9', 'ie 10'],
			location: 'pancake/css/',
			name: 'pancake.min.css'
		},
		sass: {
			modules: false,
			location: 'pancake/sass/',
			name: 'pancake.scss'
		}
	};

	//merging settings with host settings
	(0, _assign2.default)(SETTINGS.css, settings.css);
	(0, _assign2.default)(SETTINGS.sass, settings.sass);

	return new _promise2.default(function (resolve, reject) {
		//some housekeeping
		if (typeof version !== 'string') {
			reject('Plugin pancake-js got a missmath for the data that was passed to it! ' + _pancake.Style.yellow('version') + ' was ' + _pancake.Style.yellow(typeof version === 'undefined' ? 'undefined' : (0, _typeof3.default)(version)) + ' ' + ('but should have been ' + _pancake.Style.yellow('string')));
		}

		if ((typeof modules === 'undefined' ? 'undefined' : (0, _typeof3.default)(modules)) !== 'object') {
			reject('Plugin pancake-js got a missmath for the data that was passed to it! ' + _pancake.Style.yellow('modules') + ' was ' + _pancake.Style.yellow(typeof modules === 'undefined' ? 'undefined' : (0, _typeof3.default)(modules)) + ' ' + ('but should have been ' + _pancake.Style.yellow('object')));
		}

		if ((typeof settings === 'undefined' ? 'undefined' : (0, _typeof3.default)(settings)) !== 'object') {
			reject('Plugin pancake-js got a missmath for the data that was passed to it! ' + _pancake.Style.yellow('settings') + ' was ' + _pancake.Style.yellow(typeof settings === 'undefined' ? 'undefined' : (0, _typeof3.default)(settings)) + ' ' + ('but should have been ' + _pancake.Style.yellow('object')));
		}

		if (typeof cwd !== 'string') {
			reject('Plugin pancake-js got a missmath for the data that was passed to it! ' + _pancake.Style.yellow('cwd') + ' was ' + _pancake.Style.yellow(typeof cwd === 'undefined' ? 'undefined' : (0, _typeof3.default)(cwd)) + ' ' + ('but should have been ' + _pancake.Style.yellow('string')));
		}

		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		// Variables to be filled
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		var compiledAll = []; //for collect all promises
		var allSass = ''; //all modules to be collected for SETTINGS.css.name file
		var sassVersioning = true; //let’s assume the pancake module was build with sass-versioning

		var Package = require(_path2.default.normalize(__dirname + '/../package.json'));
		var banner = '/*! PANCAKE v' + version + ' PANCAKE-SASS v' + Package.version + ' */\n\n' + '/*\n' + ' * THIS FILE IS AUTOGENERATED EVERY TIME YOU INSTALL A PANCAKE MODULE.\n' + ' * DO NOT EDIT THIS FILE AND AVOID COMMITTING IT TO VERSION CONTROL.\n */\n\n';

		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		// Iterate over each module
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		var _iteratorNormalCompletion = true;
		var _didIteratorError = false;
		var _iteratorError = undefined;

		try {
			for (var _iterator = (0, _getIterator3.default)(modules), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
				var modulePackage = _step.value;

				_pancake.Log.verbose('Sass: Bulding ' + _pancake.Style.yellow(modulePackage.name));

				//check if there are sass files
				var sassModulePath = void 0;
				if (modulePackage.pancake['pancake-module'].sass !== undefined) {
					sassModulePath = _path2.default.normalize(modulePackage.path + '/' + modulePackage.pancake['pancake-module'].sass.path);
				}

				if (!_fs2.default.existsSync(sassModulePath)) {
					_pancake.Log.verbose('Sass: No Sass found in ' + _pancake.Style.yellow(sassModulePath));
				} else {
					_pancake.Log.verbose('Sass: ' + _pancake.Style.green('⌘') + ' Found Sass files in ' + _pancake.Style.yellow(sassModulePath));

					//generate the import statements depending on dependencies
					var sass = (0, _sass.GenerateSass)(modulePackage.path, modulePackage.name, modules, GlobalSettings.npmOrg);
					allSass += sass; //for SETTINGS.css.name file

					// //adding banner and conditional sass-versioning
					if (modulePackage.pancake['pancake-module'].sass['sass-versioning'] === true) {
						sassVersioning = true; //setting this if we encounter at least one module with sass-versioning enabled

						var _sassVersioningPath = _path2.default.normalize(cwd + '/node_modules/sass-versioning/dist/_index.scss').replace(/\\/g, "\\\\");

						sass = '' + banner + ('/* ' + modulePackage.name + ' v' + modulePackage.version + ' */\n\n') + ('@import "' + _sassVersioningPath + '";\n\n') + (sass + '\n') + '@include versioning-check();\n';
					} else {
						sass = '/* ' + modulePackage.name + ' v' + modulePackage.version + ' */\n\n' + sass + '\n';
					}

					//write css file
					if (SETTINGS.css.modules) {
						var location = _path2.default.normalize(cwd + '/' + SETTINGS.css.location + '/' + modulePackage.name.split('/')[1] + '.css');

						compiledAll.push((0, _sass.Sassify)(location, SETTINGS.css, sass) //generate css and write file
						.catch(function (error) {
							_pancake.Log.error(error);
						}));
					}

					//write sass file
					if (SETTINGS.sass.modules) {
						var _location = _path2.default.normalize(cwd + '/' + SETTINGS.sass.location + '/' + modulePackage.name.split('/')[1] + '.scss');

						compiledAll.push((0, _pancake.WriteFile)(_location, sass) //write file
						.catch(function (error) {
							_pancake.Log.error(error);

							process.exit(1);
						}));
					}
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
			_pancake.Loading.stop('pancake-sass', _pancake.Log.verboseMode); //stop loading animation

			_pancake.Log.info('No pancake modules found \uD83D\uDE2C');
			resolve(SETTINGS);
		} else {

			//write the SETTINGS.css.name file
			var locationCSS = _path2.default.normalize(cwd + '/' + SETTINGS.css.location + '/' + SETTINGS.css.name);

			if (sassVersioning === true) {
				var sassVersioningPath = _path2.default.normalize(cwd + '/node_modules/sass-versioning/dist/_index.scss').replace(/\\/g, "\\\\");

				allSass = '' + banner + ('@import "' + sassVersioningPath + '";\n\n') + ((0, _helpers.StripDuplicateLines)(allSass) + '\n\n') + '@include versioning-check();\n';
			} else {
				allSass = '' + banner + (0, _helpers.StripDuplicateLines)(allSass) + '\n';
			}

			//generate SETTINGS.css.name file
			if (SETTINGS.css.name !== false) {
				compiledAll.push((0, _sass.Sassify)(locationCSS, SETTINGS.css, allSass).catch(function (error) {
					_pancake.Log.error(error);
				}));
			}

			//write SETTINGS.sass.name file
			if (SETTINGS.sass.name !== false) {
				var locationSASS = _path2.default.normalize(cwd + '/' + SETTINGS.sass.location + '/' + SETTINGS.sass.name);

				compiledAll.push((0, _pancake.WriteFile)(locationSASS, allSass) //write file
				.catch(function (error) {
					_pancake.Log.error(error);

					process.exit(1);
				}));
			}

			//after all files have been compiled and written
			_promise2.default.all(compiledAll).catch(function (error) {
				_pancake.Loading.stop('pancake-sass', _pancake.Log.verboseMode); //stop loading animation

				_pancake.Log.error('Sass plugin ran into an error: ' + error);
			}).then(function () {
				_pancake.Log.ok('SASS PLUGIN FINISHED');

				_pancake.Loading.stop('pancake-sass', _pancake.Log.verboseMode); //stop loading animation
				resolve(SETTINGS);
			});
		}
	});
};