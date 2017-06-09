  1 ﻿/*
  2 Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
  3 For licensing, see LICENSE.html or http://ckeditor.com/license
  4 */
  5 
  6 /**
  7  * @fileOverview The "filebrowser" plugin that adds support for file uploads and
  8  *               browsing.
  9  *
 10  * When a file is uploaded or selected inside the file browser, its URL is
 11  * inserted automatically into a field defined in the <code>filebrowser</code>
 12  * attribute. In order to specify a field that should be updated, pass the tab ID and
 13  * the element ID, separated with a colon.<br /><br />
 14  *
 15  * <strong>Example 1: (Browse)</strong>
 16  *
 17  * <pre>
 18  * {
 19  * 	type : 'button',
 20  * 	id : 'browse',
 21  * 	filebrowser : 'tabId:elementId',
 22  * 	label : editor.lang.common.browseServer
 23  * }
 24  * </pre>
 25  *
 26  * If you set the <code>filebrowser</code> attribute for an element other than
 27  * the <code>fileButton</code>, the <code>Browse</code> action will be triggered.<br /><br />
 28  *
 29  * <strong>Example 2: (Quick Upload)</strong>
 30  *
 31  * <pre>
 32  * {
 33  * 	type : 'fileButton',
 34  * 	id : 'uploadButton',
 35  * 	filebrowser : 'tabId:elementId',
 36  * 	label : editor.lang.common.uploadSubmit,
 37  * 	'for' : [ 'upload', 'upload' ]
 38  * }
 39  * </pre>
 40  *
 41  * If you set the <code>filebrowser</code> attribute for a <code>fileButton</code>
 42  * element, the <code>QuickUpload</code> action will be executed.<br /><br />
 43  *
 44  * The filebrowser plugin also supports more advanced configuration performed through
 45  * a JavaScript object.
 46  *
 47  * The following settings are supported:
 48  *
 49  * <ul>
 50  * <li><code>action</code> – <code>Browse</code> or <code>QuickUpload</code>.</li>
 51  * <li><code>target</code> – the field to update in the <code><em>tabId:elementId</em></code> format.</li>
 52  * <li><code>params</code> – additional arguments to be passed to the server connector (optional).</li>
 53  * <li><code>onSelect</code> – a function to execute when the file is selected/uploaded (optional).</li>
 54  * <li><code>url</code> – the URL to be called (optional).</li>
 55  * </ul>
 56  *
 57  * <strong>Example 3: (Quick Upload)</strong>
 58  *
 59  * <pre>
 60  * {
 61  * 	type : 'fileButton',
 62  * 	label : editor.lang.common.uploadSubmit,
 63  * 	id : 'buttonId',
 64  * 	filebrowser :
 65  * 	{
 66  * 		action : 'QuickUpload', // required
 67  * 		target : 'tab1:elementId', // required
 68  * 		params : // optional
 69  * 		{
 70  * 			type : 'Files',
 71  * 			currentFolder : '/folder/'
 72  * 		},
 73  * 		onSelect : function( fileUrl, errorMessage ) // optional
 74  * 		{
 75  * 			// Do not call the built-in selectFuntion.
 76  * 			// return false;
 77  * 		}
 78  * 	},
 79  * 	'for' : [ 'tab1', 'myFile' ]
 80  * }
 81  * </pre>
 82  *
 83  * Suppose you have a file element with an ID of <code>myFile</code>, a text
 84  * field with an ID of <code>elementId</code> and a <code>fileButton</code>.
 85  * If the <code>filebowser.url</code> attribute is not specified explicitly,
 86  * the form action will be set to <code>filebrowser[<em>DialogWindowName</em>]UploadUrl</code>
 87  * or, if not specified, to <code>filebrowserUploadUrl</code>. Additional parameters
 88  * from the <code>params</code> object will be added to the query string. It is
 89  * possible to create your own <code>uploadHandler</code> and cancel the built-in
 90  * <code>updateTargetElement</code> command.<br /><br />
 91  *
 92  * <strong>Example 4: (Browse)</strong>
 93  *
 94  * <pre>
 95  * {
 96  * 	type : 'button',
 97  * 	id : 'buttonId',
 98  * 	label : editor.lang.common.browseServer,
 99  * 	filebrowser :
100  * 	{
101  * 		action : 'Browse',
102  * 		url : '/ckfinder/ckfinder.html&type=Images',
103  * 		target : 'tab1:elementId'
104  * 	}
105  * }
106  * </pre>
107  *
108  * In this example, when the button is pressed, the file browser will be opened in a
109  * popup window. If you do not specify the <code>filebrowser.url</code> attribute,
110  * <code>filebrowser[<em>DialogName</em>]BrowseUrl</code> or
111  * <code>filebrowserBrowseUrl</code> will be used. After selecting a file in the file
112  * browser, an element with an ID of <code>elementId</code> will be updated. Just
113  * like in the third example, a custom <code>onSelect</code> function may be defined.
114  */
115 ( function()
116 {
117 	/*
118 	 * Adds (additional) arguments to given url.
119 	 *
120 	 * @param {String}
121 	 *            url The url.
122 	 * @param {Object}
123 	 *            params Additional parameters.
124 	 */
125 	function addQueryString( url, params )
126 	{
127 		var queryString = [];
128 
129 		if ( !params )
130 			return url;
131 		else
132 		{
133 			for ( var i in params )
134 				queryString.push( i + "=" + encodeURIComponent( params[ i ] ) );
135 		}
136 
137 		return url + ( ( url.indexOf( "?" ) != -1 ) ? "&" : "?" ) + queryString.join( "&" );
138 	}
139 
140 	/*
141 	 * Make a string's first character uppercase.
142 	 *
143 	 * @param {String}
144 	 *            str String.
145 	 */
146 	function ucFirst( str )
147 	{
148 		str += '';
149 		var f = str.charAt( 0 ).toUpperCase();
150 		return f + str.substr( 1 );
151 	}
152 
153 	/*
154 	 * The onlick function assigned to the 'Browse Server' button. Opens the
155 	 * file browser and updates target field when file is selected.
156 	 *
157 	 * @param {CKEDITOR.event}
158 	 *            evt The event object.
159 	 */
160 	function browseServer( evt )
161 	{
162 		var dialog = this.getDialog();
163 		var editor = dialog.getParentEditor();
164 
165 		editor._.filebrowserSe = this;
166 
167 		var width = editor.config[ 'filebrowser' + ucFirst( dialog.getName() ) + 'WindowWidth' ]
168 				|| editor.config.filebrowserWindowWidth || '80%';
169 		var height = editor.config[ 'filebrowser' + ucFirst( dialog.getName() ) + 'WindowHeight' ]
170 				|| editor.config.filebrowserWindowHeight || '70%';
171 
172 		var params = this.filebrowser.params || {};
173 		params.CKEditor = editor.name;
174 		params.CKEditorFuncNum = editor._.filebrowserFn;
175 		if ( !params.langCode )
176 			params.langCode = editor.langCode;
177 
178 		var url = addQueryString( this.filebrowser.url, params );
179 		// TODO: V4: Remove backward compatibility (#8163).
180 		editor.popup( url, width, height, editor.config.filebrowserWindowFeatures || editor.config.fileBrowserWindowFeatures );
181 	}
182 
183 	/*
184 	 * The onlick function assigned to the 'Upload' button. Makes the final
185 	 * decision whether form is really submitted and updates target field when
186 	 * file is uploaded.
187 	 *
188 	 * @param {CKEDITOR.event}
189 	 *            evt The event object.
190 	 */
191 	function uploadFile( evt )
192 	{
193 		var dialog = this.getDialog();
194 		var editor = dialog.getParentEditor();
195 
196 		editor._.filebrowserSe = this;
197 
198 		// If user didn't select the file, stop the upload.
199 		if ( !dialog.getContentElement( this[ 'for' ][ 0 ], this[ 'for' ][ 1 ] ).getInputElement().$.value )
200 			return false;
201 
202 		if ( !dialog.getContentElement( this[ 'for' ][ 0 ], this[ 'for' ][ 1 ] ).getAction() )
203 			return false;
204 
205 		return true;
206 	}
207 
208 	/*
209 	 * Setups the file element.
210 	 *
211 	 * @param {CKEDITOR.ui.dialog.file}
212 	 *            fileInput The file element used during file upload.
213 	 * @param {Object}
214 	 *            filebrowser Object containing filebrowser settings assigned to
215 	 *            the fileButton associated with this file element.
216 	 */
217 	function setupFileElement( editor, fileInput, filebrowser )
218 	{
219 		var params = filebrowser.params || {};
220 		params.CKEditor = editor.name;
221 		params.CKEditorFuncNum = editor._.filebrowserFn;
222 		if ( !params.langCode )
223 			params.langCode = editor.langCode;
224 
225 		fileInput.action = addQueryString( filebrowser.url, params );
226 		fileInput.filebrowser = filebrowser;
227 	}
228 
229 	/*
230 	 * Traverse through the content definition and attach filebrowser to
231 	 * elements with 'filebrowser' attribute.
232 	 *
233 	 * @param String
234 	 *            dialogName Dialog name.
235 	 * @param {CKEDITOR.dialog.definitionObject}
236 	 *            definition Dialog definition.
237 	 * @param {Array}
238 	 *            elements Array of {@link CKEDITOR.dialog.definition.content}
239 	 *            objects.
240 	 */
241 	function attachFileBrowser( editor, dialogName, definition, elements )
242 	{
243 		var element, fileInput;
244 
245 		for ( var i in elements )
246 		{
247 			element = elements[ i ];
248 
249 			if ( element.type == 'hbox' || element.type == 'vbox' )
250 				attachFileBrowser( editor, dialogName, definition, element.children );
251 
252 			if ( !element.filebrowser )
253 				continue;
254 
255 			if ( typeof element.filebrowser == 'string' )
256 			{
257 				var fb =
258 				{
259 					action : ( element.type == 'fileButton' ) ? 'QuickUpload' : 'Browse',
260 					target : element.filebrowser
261 				};
262 				element.filebrowser = fb;
263 			}
264 
265 			if ( element.filebrowser.action == 'Browse' )
266 			{
267 				var url = element.filebrowser.url;
268 				if ( url === undefined )
269 				{
270 					url = editor.config[ 'filebrowser' + ucFirst( dialogName ) + 'BrowseUrl' ];
271 					if ( url === undefined )
272 						url = editor.config.filebrowserBrowseUrl;
273 				}
274 
275 				if ( url )
276 				{
277 					element.onClick = browseServer;
278 					element.filebrowser.url = url;
279 					element.hidden = false;
280 				}
281 			}
282 			else if ( element.filebrowser.action == 'QuickUpload' && element[ 'for' ] )
283 			{
284 				url = element.filebrowser.url;
285 				if ( url === undefined )
286 				{
287 					url = editor.config[ 'filebrowser' + ucFirst( dialogName ) + 'UploadUrl' ];
288 					if ( url === undefined )
289 						url = editor.config.filebrowserUploadUrl;
290 				}
291 
292 				if ( url )
293 				{
294 					var onClick = element.onClick;
295 					element.onClick = function( evt )
296 					{
297 						// "element" here means the definition object, so we need to find the correct
298 						// button to scope the event call
299 						var sender = evt.sender;
300 						if ( onClick && onClick.call( sender, evt ) === false )
301 							return false;
302 
303 						return uploadFile.call( sender, evt );
304 					};
305 
306 					element.filebrowser.url = url;
307 					element.hidden = false;
308 					setupFileElement( editor, definition.getContents( element[ 'for' ][ 0 ] ).get( element[ 'for' ][ 1 ] ), element.filebrowser );
309 				}
310 			}
311 		}
312 	}
313 
314 	/*
315 	 * Updates the target element with the url of uploaded/selected file.
316 	 *
317 	 * @param {String}
318 	 *            url The url of a file.
319 	 */
320 	function updateTargetElement( url, sourceElement )
321 	{
322 		var dialog = sourceElement.getDialog();
323 		var targetElement = sourceElement.filebrowser.target || null;
324 		url = url.replace( /#/g, '%23' );
325 
326 		// If there is a reference to targetElement, update it.
327 		if ( targetElement )
328 		{
329 			var target = targetElement.split( ':' );
330 			var element = dialog.getContentElement( target[ 0 ], target[ 1 ] );
331 			if ( element )
332 			{
333 				element.setValue( url );
334 				dialog.selectPage( target[ 0 ] );
335 			}
336 		}
337 	}
338 
339 	/*
340 	 * Returns true if filebrowser is configured in one of the elements.
341 	 *
342 	 * @param {CKEDITOR.dialog.definitionObject}
343 	 *            definition Dialog definition.
344 	 * @param String
345 	 *            tabId The tab id where element(s) can be found.
346 	 * @param String
347 	 *            elementId The element id (or ids, separated with a semicolon) to check.
348 	 */
349 	function isConfigured( definition, tabId, elementId )
350 	{
351 		if ( elementId.indexOf( ";" ) !== -1 )
352 		{
353 			var ids = elementId.split( ";" );
354 			for ( var i = 0 ; i < ids.length ; i++ )
355 			{
356 				if ( isConfigured( definition, tabId, ids[i] ) )
357 					return true;
358 			}
359 			return false;
360 		}
361 
362 		var elementFileBrowser = definition.getContents( tabId ).get( elementId ).filebrowser;
363 		return ( elementFileBrowser && elementFileBrowser.url );
364 	}
365 
366 	function setUrl( fileUrl, data )
367 	{
368 		var dialog = this._.filebrowserSe.getDialog(),
369 			targetInput = this._.filebrowserSe[ 'for' ],
370 			onSelect = this._.filebrowserSe.filebrowser.onSelect;
371 
372 		if ( targetInput )
373 			dialog.getContentElement( targetInput[ 0 ], targetInput[ 1 ] ).reset();
374 
375 		if ( typeof data == 'function' && data.call( this._.filebrowserSe ) === false )
376 			return;
377 
378 		if ( onSelect && onSelect.call( this._.filebrowserSe, fileUrl, data ) === false )
379 			return;
380 
381 		// The "data" argument may be used to pass the error message to the editor.
382 		if ( typeof data == 'string' && data )
383 			alert( data );
384 
385 		if ( fileUrl )
386 			updateTargetElement( fileUrl, this._.filebrowserSe );
387 	}
388 
389 	CKEDITOR.plugins.add( 'filebrowser',
390 	{
391 		init : function( editor, pluginPath )
392 		{
393 			editor._.filebrowserFn = CKEDITOR.tools.addFunction( setUrl, editor );
394 			editor.on( 'destroy', function () { CKEDITOR.tools.removeFunction( this._.filebrowserFn ); } );
395 		}
396 	} );
397 
398 	CKEDITOR.on( 'dialogDefinition', function( evt )
399 	{
400 		var definition = evt.data.definition,
401 			element;
402 		// Associate filebrowser to elements with 'filebrowser' attribute.
403 		for ( var i in definition.contents )
404 		{
405 			if ( ( element = definition.contents[ i ] ) )
406 			{
407 				attachFileBrowser( evt.editor, evt.data.name, definition, element.elements );
408 				if ( element.hidden && element.filebrowser )
409 				{
410 					element.hidden = !isConfigured( definition, element[ 'id' ], element.filebrowser );
411 				}
412 			}
413 		}
414 	} );
415 
416 } )();
417 
418 /**
419  * The location of an external file browser that should be launched when the <strong>Browse Server</strong>
420  * button is pressed. If configured, the <strong>Browse Server</strong> button will appear in the
421  * <strong>Link</strong>, <strong>Image</strong>, and <strong>Flash</strong> dialog windows.
422  * @see The <a href="http://docs.cksource.com/CKEditor_3.x/Developers_Guide/File_Browser_(Uploader)">File Browser/Uploader</a> documentation.
423  * @name CKEDITOR.config.filebrowserBrowseUrl
424  * @since 3.0
425  * @type String
426  * @default <code>''</code> (empty string = disabled)
427  * @example
428  * config.filebrowserBrowseUrl = '/browser/browse.php';
429  */
430 
431 /**
432  * The location of the script that handles file uploads.
433  * If set, the <strong>Upload</strong> tab will appear in the <strong>Link</strong>, <strong>Image</strong>,
434  * and <strong>Flash</strong> dialog windows.
435  * @name CKEDITOR.config.filebrowserUploadUrl
436  * @see The <a href="http://docs.cksource.com/CKEditor_3.x/Developers_Guide/File_Browser_(Uploader)">File Browser/Uploader</a> documentation.
437  * @since 3.0
438  * @type String
439  * @default <code>''</code> (empty string = disabled)
440  * @example
441  * config.filebrowserUploadUrl = '/uploader/upload.php';
442  */
443 
444 /**
445  * The location of an external file browser that should be launched when the <strong>Browse Server</strong>
446  * button is pressed in the <strong>Image</strong> dialog window.
447  * If not set, CKEditor will use <code>{@link CKEDITOR.config.filebrowserBrowseUrl}</code>.
448  * @name CKEDITOR.config.filebrowserImageBrowseUrl
449  * @since 3.0
450  * @type String
451  * @default <code>''</code> (empty string = disabled)
452  * @example
453  * config.filebrowserImageBrowseUrl = '/browser/browse.php?type=Images';
454  */
455 
456 /**
457  * The location of an external file browser that should be launched when the <strong>Browse Server</strong>
458  * button is pressed in the <strong>Flash</strong> dialog window.
459  * If not set, CKEditor will use <code>{@link CKEDITOR.config.filebrowserBrowseUrl}</code>.
460  * @name CKEDITOR.config.filebrowserFlashBrowseUrl
461  * @since 3.0
462  * @type String
463  * @default <code>''</code> (empty string = disabled)
464  * @example
465  * config.filebrowserFlashBrowseUrl = '/browser/browse.php?type=Flash';
466  */
467 
468 /**
469  * The location of the script that handles file uploads in the <strong>Image</strong> dialog window.
470  * If not set, CKEditor will use <code>{@link CKEDITOR.config.filebrowserUploadUrl}</code>.
471  * @name CKEDITOR.config.filebrowserImageUploadUrl
472  * @since 3.0
473  * @type String
474  * @default <code>''</code> (empty string = disabled)
475  * @example
476  * config.filebrowserImageUploadUrl = '/uploader/upload.php?type=Images';
477  */
478 
479 /**
480  * The location of the script that handles file uploads in the <strong>Flash</strong> dialog window.
481  * If not set, CKEditor will use <code>{@link CKEDITOR.config.filebrowserUploadUrl}</code>.
482  * @name CKEDITOR.config.filebrowserFlashUploadUrl
483  * @since 3.0
484  * @type String
485  * @default <code>''</code> (empty string = disabled)
486  * @example
487  * config.filebrowserFlashUploadUrl = '/uploader/upload.php?type=Flash';
488  */
489 
490 /**
491  * The location of an external file browser that should be launched when the <strong>Browse Server</strong>
492  * button is pressed in the <strong>Link</strong> tab of the <strong>Image</strong> dialog window.
493  * If not set, CKEditor will use <code>{@link CKEDITOR.config.filebrowserBrowseUrl}</code>.
494  * @name CKEDITOR.config.filebrowserImageBrowseLinkUrl
495  * @since 3.2
496  * @type String
497  * @default <code>''</code> (empty string = disabled)
498  * @example
499  * config.filebrowserImageBrowseLinkUrl = '/browser/browse.php';
500  */
501 
502 /**
503  * The features to use in the file browser popup window.
504  * @name CKEDITOR.config.filebrowserWindowFeatures
505  * @since 3.4.1
506  * @type String
507  * @default <code>'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'</code>
508  * @example
509  * config.filebrowserWindowFeatures = 'resizable=yes,scrollbars=no';
510  */
511 
512 /**
513  * The width of the file browser popup window. It can be a number denoting a value in
514  * pixels or a percent string.
515  * @name CKEDITOR.config.filebrowserWindowWidth
516  * @type Number|String
517  * @default <code>'80%'</code>
518  * @example
519  * config.filebrowserWindowWidth = 750;
520  * @example
521  * config.filebrowserWindowWidth = '50%';
522  */
523 
524 /**
525  * The height of the file browser popup window. It can be a number denoting a value in
526  * pixels or a percent string.
527  * @name CKEDITOR.config.filebrowserWindowHeight
528  * @type Number|String
529  * @default <code>'70%'</code>
530  * @example
531  * config.filebrowserWindowHeight = 580;
532  * @example
533  * config.filebrowserWindowHeight = '50%';
534  */
535 