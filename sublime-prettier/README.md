[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

See alternative: [Package Control](https://packagecontrol.io/packages/JsPrettier) | [Repo](https://github.com/jonlabelle/SublimeJsPrettier)
___

# Prettier

This is a Sublime Text 3 plugin for the [prettier](https://github.com/prettier/prettier) JavaScript formatter.

## Installation

You need `prettier` installed globally for this plugin to work. See the [installation instructions](https://prettier.io/docs/en/install.html).

`npm install -g prettier`

### Manually

1. Go to
    * (Mac OS/OS X): `~/Library/Application\ Support/Sublime\ Text\ 3/Packages/"`
    * (Windows): `C:\Users\[username]\AppData\Roaming\Sublime Text 3\Packages`

2. `git clone git@github.com:danreeves/sublime-prettier.git` or [download](https://github.com/danreeves/sublime-prettier/archive/master.zip) the zip and extract to that location.

## Usage

### Command Palette

<dl>
    <dt>Format the entire file:</dt>
    <dd><code>Prettier: Format this file</code></dd>
    <dt>Format the current selection(s):</dt>
    <dd><code>Prettier: Format this selection</code></dd>
</dl>

### Hotkeys

- Linux: <kbd>ctrl+alt+p</kbd>
- Windows: <kbd>ctrl+alt+p</kbd>
- OS X: <kbd>ctrl+alt+p</kbd>

You can add [custom key bindings](https://www.sublimetext.com/docs/3/settings.html) using the commands `prettier` and `prettier_selection`.

## Configuration

The plugin takes the same settings and the `prettier` tool. See the [`prettier` repo](https://github.com/prettier/prettier/blob/master/docs/options.md) or [this repo](https://github.com/danreeves/sublime-prettier/blob/master/Prettier.sublime-settings). You can configure them in Sublime at `Preferences > Package Settings > Prettier`.

You can turn on the auto formatting on save by setting `autoformat` to `true`.

By default prettier config is searched by `prettier --find-config-path`, but you can define custom locations to search through `configLocations`.

For example:

```js
{
  // Turns on/off autoformatting on save
  "autoformat": true,

  // Only attempt to format files with extensions set there
  "extensions": ["js", "jsx"],

  // Fit code within this line limit
  "printWidth": 80,

  // Number of spaces it should use per tab
  "tabWidth": 2,

  // Use the flow parser instead of babylon
  "useFlowParser": false,

  // If true, will use single instead of double quotes
  "singleQuote": false,

  // Controls the printing of trailing commas wherever possible
  "trailingComma": false,

  // Controls the printing of spaces inside array and objects
  "bracketSpacing": true,

  // Try prettier config in the user's home folder and in current opened folder
  "configLocations": ["${home_path}/.prettierrc", "${folder}/.prettierrc"]
}
```
