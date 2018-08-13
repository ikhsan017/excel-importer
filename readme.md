###Usage

Import all cells that contain data and formula
```
php import.php source_file.xls target_file.json
```

Or, import cells that only contain formula

```
php import-formula.php source_file.xls target_file.json
```

###Use data with calx

build the html with only ```data-cell``` attribute, then load the generated json and pass to calx

```
var calxData = $.getJson('target_file.json');

$('#calculator').calx({data: calxData});
```
