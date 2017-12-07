/***************************************************************************************************************************************************************
 *
 * Running pancake inside a cli
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
exports.init = undefined;

var _assign = require('babel-runtime/core-js/object/assign');

var _assign2 = _interopRequireDefault(_assign);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _repeat = require('babel-runtime/core-js/string/repeat');

var _repeat2 = _interopRequireDefault(_repeat);

var _toConsumableArray2 = require('babel-runtime/helpers/toConsumableArray');

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _helpers = require('./helpers');

var _plugins = require('./plugins');

var _modules = require('./modules');

var _logging = require('./logging');

var _parseArguments = require('./parse-arguments');

var _conflicts = require('./conflicts');

var _settings = require('./settings');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Running the program in CLI
 *
 * @param  {array} argv - The arguments passed to node
 */
var init = exports.init = function init() {
	var argv = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : process.argv;

	var pkg = require(_path2.default.normalize(__dirname + '/../package.json'));

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Verbose flag
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var verbose = false;
	if (process.argv.indexOf('-v') !== -1 || process.argv.indexOf('--verbose') !== -1) {
		_logging.Log.verboseMode = true;
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Check npm version
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var npmVersion = (0, _helpers.CheckNPM)();

	//npm 3 and higher is required as below will install dependencies inside each module folder
	if (!npmVersion) {
		_logging.Log.error('Pancake only works with npm 3 and later.');
		_logging.Log.space();
		process.exit(1);
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Get global settings
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var SETTINGS = _settings.Settings.GetGlobal();

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Parsing cli arguments
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var ARGS = (0, _parseArguments.ParseArgs)(SETTINGS, argv);

	//arg overwrites
	SETTINGS.npmOrg = ARGS.org;
	SETTINGS.plugins = ARGS.plugins;
	SETTINGS.ignorePlugins = ARGS.ignorePlugins.length ? ARGS.ignorePlugins : SETTINGS.ignorePlugins;

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Set global settings
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	if (ARGS.set.length > 0) {
		SETTINGS = _settings.Settings.SetGlobal.apply(_settings.Settings, [__dirname, SETTINGS].concat((0, _toConsumableArray3.default)(ARGS.set)));

		_logging.Loading.stop();
		_logging.Log.space();
		process.exit(0); //finish after
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Display help
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	if (ARGS.help) {
		_logging.Log.info('Pancake help');
		_logging.Loading.stop();

		if ((0, _helpers.Size)().width > 110) {
			//only show if we have enough space
			console.log(_logging.Style.yellow('\n                                                 ' + _logging.Style.white('.,;+@@@@@@@@@#+;,\n                                              #+\':               .+@@;\n                                            @`                       `##\n                                           @+   `;@@#+\'      ,+@@@@@@@@@@') + '\n                                 `,;\'\'+#@@++' + _logging.Style.white('@     .,;@;    @@@@@@@@@@@@@ #@@@@') + '+:`\n                          `,\'@@+,`   :;:;+\'' + _logging.Style.white('`:@@;.       `@@@@@@@@@@@+..@@@@@@@@@@@') + '#;`\n                       +@#,        ``.,.  ' + _logging.Style.white('@@.+ @@@\':. `;@#  ;.,+@@@@@@@@@@@@@@@@@@@@@@@@@@@') + '\'\n                   ,#@,     `.`        ' + _logging.Style.white('#@.#@@@@@@#@@@ `: ;@@@@@@@@@@@@@@@@@@@@@@@@@@@@@.@') + '   ;@\'\n               .@@@;:,`  .;++;,      ' + _logging.Style.white('#@#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@') + '        `   `@:\n             @@,   ,;::;;,.        ' + _logging.Style.white('@@@@@@@@@@@@#\':\'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@') + '    `  `,`  `  .#+`\n           \'@.   ..   ,\'+\':`      ' + _logging.Style.white('@@@@@@@@\'`        @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@') + '\'      .,  .`     ,@\'\n        ,@+       \'.               ' + _logging.Style.white('@@@@,           ,@@@@@@:    .#@@@@@@@@@@@@@@@@@@@@@+') + '                  ;@\n      ;@,                                           ' + _logging.Style.white('#@+`          .@@@@@+     .+@@@#+@') + ';                    @\n      @+      .@\'++                                                 ' + _logging.Style.white(':@@@') + '                                  @#\n         +#@,          :;:\':\':                     - pancake -        ' + _logging.Style.white('@@@') + '                         `:\'\':  `@\'\n       \'@@@@@@@@@@,                                                    ' + _logging.Style.white('@@') + ':      \'`  ,\'+##@@@@@@@@@.    ``\n   `@@`   `::,\';:;#@@@@@@#;.  `,;++\',                               .\' ' + _logging.Style.white('@@@') + '  ,@@@,@@@@@@@#+\':,`             ,+#\n  :@`                    .#@@@@@@+#@@@@@@@@@@@@@@#+\'\'+++@@@@@@@+#+++   ' + _logging.Style.white('@@@@') + ';,,;,`                             @\n  `@:                                   `:;+#@@##@@@+;,`              ' + _logging.Style.white('#@\';@@') + '                                 #,\n    ;#+;            ```                                               ' + _logging.Style.white('@@+@@#') + '                             .+\'.\n         \'@@@@@@@@@@@@@@@@#.                             ``           ' + _logging.Style.white('#@@@#') + '     ``         `#@@@@@@:\'@@@@@@,\n                   ``...,,+@@@@@@@@\'.`.,;\'\'#@@@;    `\'@@@@@@@@@@@@@@#:     @@@#\'` `###@@#\'.        ,;;,::\n                                     ,@@@@@@@@#@@@:@@@@#;.'));
		}

		console.log(_logging.Style.yellow('\n  ( ^-^)_\u65E6\n\n') + '  \uD83E\uDD5E  Pancake is an utility to make working with npm modules for the frontend sweet and seamlessly.\n\n' + '  It will check your peerDependencies for conflicts and comes with plugins to compile the contents\n' + '  for you and lists all available modules for you to select and install.\n\n' + ('  ' + _logging.Style.gray((0, _repeat2.default)('-', (0, _helpers.Size)().width > 114 ? 110 : (0, _helpers.Size)().width - 4) + '\n\n')) + ('  ' + _logging.Style.bold('PATH') + '            - Run pancake in a specific path and look for pancake modules there.\n') + ('    $ ' + _logging.Style.yellow('pancake /Users/you/project/folder') + '\n\n') + ('  ' + _logging.Style.bold('SETTINGS') + '        - Set global settings. Available settings are: ' + _logging.Style.yellow((0, _keys2.default)(SETTINGS).join(', ')) + '.\n') + ('    $ ' + _logging.Style.yellow('pancake --set npmOrg "@yourOrg"') + '\n') + ('    $ ' + _logging.Style.yellow('pancake --set ignorePlugins @gov.au/pancake-sass,@gov.au/pancake-svg') + '\n\n') + ('  ' + _logging.Style.bold('ORG') + '             - Change the org scope of the pancake modules you like to use.\n') + ('    $ ' + _logging.Style.yellow('pancake --org "@your.org"') + '\n\n') + ('  ' + _logging.Style.bold('PLUGINS') + '         - Temporarily turn off all plugins.\n') + ('    $ ' + _logging.Style.yellow('pancake --noplugins') + '\n\n') + ('  ' + _logging.Style.bold('IGNORED PLUGINS') + ' - Prevent a certain plugin(s) from being installed and run.\n') + ('    $ ' + _logging.Style.yellow('pancake --ignore @gov.au/pancake-js,@gov.au/pancake-sass') + '\n\n') + ('  ' + _logging.Style.bold('DON\u2019T SAVE') + '      - Prevent pancake to save it\u2019s settings into your package.json.\n') + ('    $ ' + _logging.Style.yellow('pancake --nosave') + '\n\n') + ('  ' + _logging.Style.bold('HELP') + '            - Display the help (this screen).\n') + ('    $ ' + _logging.Style.yellow('pancake --help') + '\n\n') + ('  ' + _logging.Style.bold('VERSION') + '         - Display the version of pancake.\n') + ('    $ ' + _logging.Style.yellow('pancake --version') + '\n\n') + ('  ' + _logging.Style.bold('VERBOSE') + '         - Run pancake in verbose silly mode\n') + ('    $ ' + _logging.Style.yellow('pancake --verbose')));

		_logging.Log.space();
		process.exit(0); //finish after
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Finding the current working directory
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var pkgPath = (0, _helpers.Cwd)(ARGS.cwd);

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Get local settings
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	var SETTINGSlocal = _settings.Settings.GetLocal(pkgPath);

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Display version
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	if (ARGS.version) {
		console.log('v' + pkg.version);

		if (ARGS.verbose) {
			//show some space if we had verbose enabled
			_logging.Log.space();
		}

		process.exit(0); //finish after
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Show banner
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	_logging.Log.info('PANCAKE MIXING THE BATTER');

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Get all modules data
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	_logging.Loading.start();

	(0, _modules.GetModules)(pkgPath, SETTINGS.npmOrg).catch(function (error) {
		_logging.Log.error('Reading all package.json files bumped into an error: ' + error);
		_logging.Log.error(error);

		process.exit(1);
	}).then(function (allModules) {
		//once we got all the content from all package.json files
		_logging.Log.verbose('Gathered all modules:\n' + _logging.Style.yellow((0, _stringify2.default)(allModules)));

		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		// Check for conflicts
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------
		if (allModules.length < 1) {
			_logging.Log.info('No modules found \uD83D\uDE2C');
			_logging.Loading.stop();
		} else {
			var conflicts = (0, _conflicts.CheckModules)(allModules);

			if (conflicts.conflicts) {
				_logging.Log.error(_logging.Style.red(conflicts.message));

				process.exit(1); //error out so npm knows things went wrong
			} else {
				_logging.Log.ok('All modules(' + allModules.length + ') without conflict \uD83D\uDCA5');
			}

			//--------------------------------------------------------------------------------------------------------------------------------------------------------------
			// Install all plugins
			//--------------------------------------------------------------------------------------------------------------------------------------------------------------
			var plugins = []; //gather all plugins we have to run later
			var installed = []; //an array to be filled with a promise from InstallPlugins

			if (SETTINGSlocal.plugins === false || SETTINGS.plugins === false) {
				_logging.Loading.stop();

				_logging.Log.verbose('Skipping plugins');
			} else {
				var allPlugins = (0, _modules.GetPlugins)(allModules);

				allPlugins.map(function (plugin) {
					//filtering out ignored plugins
					if (SETTINGSlocal.ignore.filter(function (ignore) {
						return ignore === plugin;
					}).length === 0 && SETTINGS.ignorePlugins.filter(function (ignore) {
						return ignore === plugin;
					}).length === 0) {
						plugins.push(plugin);
					}
				});

				installed.push((0, _plugins.InstallPlugins)(plugins, pkgPath)); //add the promise to the installed array
			}

			_promise2.default.all(installed) //if we had plugins installed, wait until they are finished
			.catch(function (error) {
				_logging.Log.error(error);

				process.exit(1);
			}).then(function (data) {

				//--------------------------------------------------------------------------------------------------------------------------------------------------------------
				// Run all plugins
				//--------------------------------------------------------------------------------------------------------------------------------------------------------------
				(0, _plugins.RunPlugins)(pkg.version, plugins, pkgPath, allModules, SETTINGSlocal, SETTINGS).catch(function (error) {
					_logging.Loading.stop();

					_logging.Log.error(error);
				}).then(function (settings) {
					_logging.Loading.start();

					//--------------------------------------------------------------------------------------------------------------------------------------------------------------
					// Save local settings into host package.json
					//--------------------------------------------------------------------------------------------------------------------------------------------------------------
					if (SETTINGSlocal['auto-save'] && !ARGS.nosave) {

						//merge all plugin settings
						settings.map(function (setting) {
							(0, _keys2.default)(setting).map(function (key) {
								SETTINGSlocal[key] = (0, _assign2.default)(setting[key], SETTINGSlocal[key]);
							});
						});

						_settings.Settings.SetLocal(SETTINGSlocal, pkgPath) //let’s save all settings
						.catch(function (error) {
							_logging.Log.error('Saving settings caused an error: ' + error);

							process.exit(1);
						}).then(function (SETTINGSlocal) {
							_logging.Log.ok('SETTINGS SAVED'); //all done!

							_logging.Log.done('YOUR PANCAKE IS READY ( \u02D8\u25BD\u02D8)\u3063\u65E6'); //all done!
						});
					} else {
						_logging.Log.done('YOUR PANCAKE IS READY ( \u02D8\u25BD\u02D8)\u3063\u65E6'); //all done!
					}
				});
			});
		}
	});

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	// Adding some event handling to exit signals
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------
	process.on('exit', _helpers.ExitHandler.bind(null, { withoutSpace: false })); //on closing
	process.on('SIGINT', _helpers.ExitHandler.bind(null, { withoutSpace: false })); //on [ctrl] + [c]
	process.on('uncaughtException', _helpers.ExitHandler.bind(null, { withoutSpace: false })); //on uncaught exceptions
};
// import Fs from 'fs';


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Module imports
//--------------------------------------------------------------------------------------------------------------------------------------------------------------