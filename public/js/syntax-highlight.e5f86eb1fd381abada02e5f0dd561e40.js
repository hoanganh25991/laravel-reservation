'use strict';

function syntaxHighlight(json) {
	var style = document.querySelector('#syntaxHighlight');
	if (!style) {
		style = document.createElement('style');
		style.setAttribute('id', 'syntaxHighlight');
		style.innerText = ".string { color: #A0B475; } .number { color: #DF9560; } .boolean { color: #79B0C0; } .null { color: #BC8BAC; } .key { color: #AE4544; }";
		var head = document.querySelector('head');
		head.appendChild(style);
	}
	json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
		var cls = 'number';
		if (/^"/.test(match)) {
			if (/:$/.test(match)) {
				cls = 'key';
			} else {
				cls = 'string';
			}
		} else if (/true|false/.test(match)) {
			cls = 'boolean';
		} else if (/null/.test(match)) {
			cls = 'null';
		}
		return '<span class="' + cls + '">' + match + '</span>';
	});
}