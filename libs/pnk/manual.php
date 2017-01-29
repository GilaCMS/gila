<?php

// Table indexes

"name"		// (Str) Name of the table in db
"title"		// (Str) Title to show
"id"		// (Str) The column used as orimary key
"filters"	// (Str) String added in SQL query after WHERE (ex. "company_id=4" )
"pagination"// (Int) Number of rows in every page
"order-by"	// (Str) Default order in query result
"grouping"	// (Array) Array of fields to appear in select for grouped results (ex. ["company_id","concept"] )
"seach-boxes"	// (Array) Array of fields to add search boxes (ex. ["company_id","concept"] )
"summation"	// List of columns(numeric) to SUM in grouped results
"edit"		// Make table editable to end user. Default: false
"tools"		// Array of tool buttons to be included
"fields"	// Array of the fields(arrays), index is the column name in db (ex. [ "col1"=>[...], ... ] )
"style"		// Add css style in the table (ex. "width:300px;")
"no-edit"	// Filter a field with a value, where edit cannot be permitted in the row (ex. ["published",1])
"pdf-fz"	// Font size for the table in report
"pdf-pa"	// Aligment of the paper, "P" or "L", default: "P"
"onchange"	// Function to run when registry is updated (ex. onchange(&$registry_row))
"oncreate"	// Function to run when registry is created (ex. onchange(&$registry_row))
//"href"		//
	// Field indexes
	'title'		// Title of the field to show
	'pdf-title'	// Title for the pdf report
	'options'	// List of text for values, create select dropdown for input (ex. [1=>"Text1", ...] )
	'qoptions'	// String for SQL select query that return options array (ex. "`ID` as `Index`,`Name` as Text FROM `companies`" )
	'sort'		// Sort the option list. Options: "text"
	'edit'		// Make editable true/false this field. Default table's setting
	'create'	// Set value this field in new registry. Default table's setting
	'type'		// Type of the field, affect the dispay and input. Options: "text","number","email","password","checkbox","datalist"
			"roles" // create links with another table by one join table()
			"joins" // create links with another table by multiselect,
					//require: table indexes>id , field indexes>jt,ot,qoptions/options
					// ex. "jt"=>['user_install', 'UserID', 'InstallID'],	"ot"=>['install','ID','Name'],
			"date" // Show data as date, can have optional field index 'searchbox' with options: "period","month", default: equal date
	'fields'	// 2 Columns of the join table(for roles field), 1st has id from current table,2nd from the roles table (ex. ["user_id","company_id"])
	'dateFormat'// Format of a date field (ex. "d-M-y" )
	'qcolumn'	// SQL syntax for the values of the column in parentesis
				// ex. find user id with join of another table: "(SELECT user_id FROM another_table WHERE id=another_id)"
	'run/value'	// Function, return value for every registry
				// $data['value'] : value of this field
				// $data['row']['col1'] : value of field col1 of same row
				// ex. function(&$data){ return  "<a href='image".$data["value"]."' target='_blank'>".$data["value"]."</a>"; }
	'style'		// Add css style in the table th (ex. "width:300px;")
	'td-style'	// Add css style in the table td (ex. "width:300px;")
	'numeral'	// Format of number display (ex. "0,0.000" ) Requires numeral.js
	'show'		// List the field in pnk-table but can hide it. Default: true
	'pdf'		// List the field in report. Default: true
	'csv'		// List the field in exported csv. Default: true
	'list'		// Read and list the field. Default: true
	x'onchange'	// Function to run when value is changed
	'onupdate'	// Function to run before when value is changed
	'eval'		// A string with js commands to display the value (ex. "dv=pad(cv,5);") to turn 17 into 00017
				// cv the current value, dv the display value, rv[] other values of the registry
	'png_url'	// The address of the png image to be shown instead of the value, value and .png will be added later (ex. "/img/status")

	// Commands
				// default indexes; edit, delete
	'list'		// Array of tables name that should have a <field>_id field to be filtered.
	'pdf'		// Instructions to generate or save a pdf report of the registry
		'template'	// The file used as template ex. "report.php"
		'prefix'	// Filename base ex. "REP" for REP14.pdf
		'folder'	// Foldername where to save the report, they are autoupdated in creation and update of registry

	// Tabl indexes *
	'title'		// Title of the tab
	'filters'	// Filters override


?>
