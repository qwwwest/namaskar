<?php
/* =======================================================================
 * Improved File Manager
 * ---------------------
 * License: This project is provided under the terms of the MIT LICENSE
 * http://github.com/misterunknown/ifm/blob/master/LICENSE
 * =======================================================================
 *
 * main
*/

error_reporting(E_ALL);
ini_set('display_errors', 'OFF');

class IFM
{
	private $defaultconfig = array(
		// general config
		"auth" => 0,
		"auth_source" => 'inline;admin:$2y$10$0Bnm5L4wKFHRxJgNq.oZv.v7yXhkJZQvinJYR2p6X1zPvzyDRUVRC',
		"root_dir" => "",
		"root_public_url" => "",
		"tmp_dir" => "",
		"timezone" => "",
		"forbiddenChars" => array(),
		"dateLocale" => "en-US",
		"language" => "en",
		"selfoverwrite" => 0,

		// api controls
		"ajaxrequest" => 1,
		"chmod" => 1,
		"copymove" => 1,
		"createdir" => 1,
		"createfile" => 1,
		"edit" => 1,
		"delete" => 1,
		"download" => 1,
		"extract" => 1,
		"upload" => 1,
		"remoteupload" => 1,
		"rename" => 1,
		"zipnload" => 1,
		"createarchive" => 1,
		"search" => 1,
		"pagination" => 0,

		// gui controls
		"showlastmodified" => 0,
		"showfilesize" => 1,
		"showowner" => 1,
		"showgroup" => 1,
		"showpermissions" => 2,
		"showhtdocs" => 0,
		"showhiddenfiles" => 1,
		"showpath" => 0,
		"contextmenu" => 1,
		"disable_mime_detection" => 0,
		"showrefresh" => 1,
		"forceproxy" => 0,
		"confirmoverwrite" => 1
	);

	private $config = array();
	private $templates = array();
	private $i18n = array();
	public $mode = "standalone";

	public function __construct($config = array())
	{

		// load the default config
		$this->config = $this->defaultconfig;

		// load config from environment variables
		$this->config['auth'] =  getenv('IFM_AUTH') !== false ? intval(getenv('IFM_AUTH')) : $this->config['auth'];
		$this->config['auth_source'] =  getenv('IFM_AUTH_SOURCE') !== false ? getenv('IFM_AUTH_SOURCE') : $this->config['auth_source'];
		$this->config['root_dir'] =  getenv('IFM_ROOT_DIR') !== false ? getenv('IFM_ROOT_DIR') : $this->config['root_dir'];
		$this->config['root_public_url'] =  getenv('IFM_ROOT_PUBLIC_URL') !== false ? getenv('IFM_ROOT_PUBLIC_URL') : $this->config['root_public_url'];
		$this->config['tmp_dir'] =  getenv('IFM_TMP_DIR') !== false ? getenv('IFM_TMP_DIR') : $this->config['tmp_dir'];
		$this->config['timezone'] =  getenv('IFM_TIMEZONE') !== false ? getenv('IFM_TIMEZONE') : $this->config['timezone'];
		$this->config['dateLocale'] =  getenv('IFM_DATELOCALE') !== false ? getenv('IFM_DATELOCALE') : $this->config['dateLocale'];
		$this->config['forbiddenChars'] =  getenv('IFM_FORBIDDENCHARS') !== false ? str_split(getenv('IFM_FORBIDDENCHARS')) : $this->config['forbiddenChars'];
		$this->config['language'] =  getenv('IFM_LANGUAGE') !== false ? getenv('IFM_LANGUAGE') : $this->config['language'];
		$this->config['selfoverwrite'] =  getenv('IFM_SELFOVERWRITE') !== false ? getenv('IFM_SELFOVERWRITE') : $this->config['selfoverwrite'];
		$this->config['ajaxrequest'] =  getenv('IFM_API_AJAXREQUEST') !== false ? intval(getenv('IFM_API_AJAXREQUEST')) : $this->config['ajaxrequest'];
		$this->config['chmod'] =  getenv('IFM_API_CHMOD') !== false ? intval(getenv('IFM_API_CHMOD')) : $this->config['chmod'];
		$this->config['copymove'] =  getenv('IFM_API_COPYMOVE') !== false ? intval(getenv('IFM_API_COPYMOVE')) : $this->config['copymove'];
		$this->config['createdir'] =  getenv('IFM_API_CREATEDIR') !== false ? intval(getenv('IFM_API_CREATEDIR')) : $this->config['createdir'];
		$this->config['createfile'] =  getenv('IFM_API_CREATEFILE') !== false ? intval(getenv('IFM_API_CREATEFILE')) : $this->config['createfile'];
		$this->config['edit'] =  getenv('IFM_API_EDIT') !== false ? intval(getenv('IFM_API_EDIT')) : $this->config['edit'];
		$this->config['delete'] =  getenv('IFM_API_DELETE') !== false ? intval(getenv('IFM_API_DELETE')) : $this->config['delete'];
		$this->config['download'] =  getenv('IFM_API_DOWNLOAD') !== false ? intval(getenv('IFM_API_DOWNLOAD')) : $this->config['download'];
		$this->config['extract'] =  getenv('IFM_API_EXTRACT') !== false ? intval(getenv('IFM_API_EXTRACT')) : $this->config['extract'];
		$this->config['upload'] =  getenv('IFM_API_UPLOAD') !== false ? intval(getenv('IFM_API_UPLOAD')) : $this->config['upload'];
		$this->config['remoteupload'] =  getenv('IFM_API_REMOTEUPLOAD') !== false ? intval(getenv('IFM_API_REMOTEUPLOAD')) : $this->config['remoteupload'];
		$this->config['rename'] =  getenv('IFM_API_RENAME') !== false ? intval(getenv('IFM_API_RENAME')) : $this->config['rename'];
		$this->config['zipnload'] =  getenv('IFM_API_ZIPNLOAD') !== false ? intval(getenv('IFM_API_ZIPNLOAD')) : $this->config['zipnload'];
		$this->config['createarchive'] =  getenv('IFM_API_CREATEARCHIVE') !== false ? intval(getenv('IFM_API_CREATEARCHIVE')) : $this->config['createarchive'];
		$this->config['showlastmodified'] =  getenv('IFM_GUI_SHOWLASTMODIFIED') !== false ? intval(getenv('IFM_GUI_SHOWLASTMODIFIED')) : $this->config['showlastmodified'];
		$this->config['showfilesize'] =  getenv('IFM_GUI_SHOWFILESIZE') !== false ? intval(getenv('IFM_GUI_SHOWFILESIZE')) : $this->config['showfilesize'];
		$this->config['showowner'] =  getenv('IFM_GUI_SHOWOWNER') !== false ? intval(getenv('IFM_GUI_SHOWOWNER')) : $this->config['showowner'];
		$this->config['showgroup'] =  getenv('IFM_GUI_SHOWGROUP') !== false ? intval(getenv('IFM_GUI_SHOWGROUP')) : $this->config['showgroup'];
		$this->config['showpermissions'] =  getenv('IFM_GUI_SHOWPERMISSIONS') !== false ? intval(getenv('IFM_GUI_SHOWPERMISSIONS')) : $this->config['showpermissions'];
		$this->config['showhtdocs'] =  getenv('IFM_GUI_SHOWHTDOCS') !== false ? intval(getenv('IFM_GUI_SHOWHTDOCS')) : $this->config['showhtdocs'];
		$this->config['showhiddenfiles'] =  getenv('IFM_GUI_SHOWHIDDENFILES') !== false ? intval(getenv('IFM_GUI_SHOWHIDDENFILES')) : $this->config['showhiddenfiles'];
		$this->config['showpath'] =  getenv('IFM_GUI_SHOWPATH') !== false ? intval(getenv('IFM_GUI_SHOWPATH')) : $this->config['showpath'];
		$this->config['contextmenu'] =  getenv('IFM_GUI_CONTEXTMENU') !== false ? intval(getenv('IFM_GUI_CONTEXTMENU')) : $this->config['contextmenu'];
		$this->config['search'] =  getenv('IFM_API_SEARCH') !== false ? intval(getenv('IFM_API_SEARCH')) : $this->config['search'];
		$this->config['showrefresh'] =  getenv('IFM_GUI_REFRESH') !== false ? intval(getenv('IFM_GUI_REFRESH')) : $this->config['showrefresh'];
		$this->config['forceproxy'] =  getenv('IFM_GUI_FORCEPROXY') !== false ? intval(getenv('IFM_GUI_FORCEPROXY')) : $this->config['forceproxy'];
		$this->config['confirmoverwrite'] =  getenv('IFM_GUI_CONFIRMOVERWRITE') !== false ? intval(getenv('IFM_GUI_CONFIRMOVERWRITE')) : $this->config['confirmoverwrite'];

		// optional settings
		if (getenv('IFM_SESSION_LIFETIME') !== false)
			$this->config['session_lifetime'] = getenv('IFM_SESSION_LIFETIME');
		if (getenv('IFM_FORCE_SESSION_LIFETIME') !== false)
			$this->config['session_lifetime'] = getenv('IFM_FORCE_SESSION_LIFETIME');

		// load config from passed array
		$this->config = array_merge($this->config, $config);

		// get list of ace includes
		$this->config['ace_includes'] = <<<'f00bar'

f00bar;

		// templates
		$templates = array();
		$templates['app'] = <<<'f00bar'
<nav class="navbar navbar-expand-lg navbar-dark 
	{{^config.inline}}
	fixed-top
	{{/config.inline}} bg-dark">
	<div class="container">
		<a class="navbar-brand" href="#">Namaskar - File Manager</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="true" aria-label="{{i18n.toggle_nav}}">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarCollapse">
			<div class="navbar-nav mr-auto">
				<form class="form-inline mt-2 mt-md-0">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" id="currentDirLabel">{{i18n.path_content}} {{showpath}}</span>
						</div>
						<input class="form-control" id="currentDir" aria-describedby="currentDirLabel" type="text">
					</div>      
				</form>
			</div>
			<ul class="navbar-nav">
				{{#config.showrefresh}}
				<li class="nav-item">
					<a id="refresh" class="nav-link"><span title="{{i18n.refresh}}" class="icon icon-arrows-cw" href="#"></span> <span class="d-inline d-lg-none">{{i18n.refresh}}</span></a>
				</li>
				{{/config.showrefresh}}
				{{#config.search}}
				<li class="nav-item">
					<a id="search" class="nav-link"><span title="{{i18n.search}}" class="icon icon-search" href="#"></span> <span class="d-inline d-lg-none">{{i18n.search}}</span></a>
				</li>
				{{/config.search}}
				{{#config.upload}}
				<li class="nav-item">
					<a id="upload" class="nav-link"><span title="{{i18n.upload}}" class="icon icon-upload" href="#"></span> <span class="d-inline d-lg-none">{{i18n.upload}}</span></a>
				</li>
				{{/config.upload}}
				{{#config.createfile}}
				<li class="nav-item">
					<a id="createFile" class="nav-link"><span title="{{i18n.file_new}}" class="icon icon-doc-inv" href="#"></span> <span class="d-inline d-lg-none">{{i18n.file_new}}</span></a>
				</li>
				{{/config.createfile}}
				{{#config.createdir}}
				<li class="nav-item">
					<a id="createDir" class="nav-link"><span title="{{i18n.folder_new}}" class="icon icon-folder" href="#"></span> <span class="d-inline d-lg-none">{{i18n.folder_new}}</span></a>
				</li>
				{{/config.createdir}}
				{{#generic.hasdropdown}}
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="menu">
						{{#config.remoteupload}}
						<a class="dropdown-item" href="#" id="buttonRemoteUpload"><span class="icon icon-upload-cloud"></span> {{i18n.upload_remote}}</a>
						{{/config.remoteupload}}
						{{#config.ajaxrequest}}
						<a class="dropdown-item" href="#" id="buttonAjaxRequest"><span class="icon icon-link-ext"></span> {{i18n.ajax_request}}</a>
						{{/config.ajaxrequest}}
						{{#config.auth}}
						<a class="dropdown-item" href="?api=logout" id="buttonLogout"><span class="icon icon-logout"></span> {{i18n.logout}}</a>
						{{/config.auth}}
					</div>
				</li>
				{{/generic.hasdropdown}}
			</ul>
		</div>
	</div>
</nav>
<div id="filedropoverlay">
	<h1>{{i18n.upload_drop}}</h1>
</div>
<main role="main">
	<div class="container">
		<table id="filetable" class="table">
			<thead>
				<tr>
					<th class="th-meta hidden" data-visible="false"></th>
					<th class="th-filename">{{i18n.filename}}</th>
					{{#config.download}}
					<th class="th-download"></th>
					{{/config.download}}
					{{#config.showlastmodified}}
					<th class="th-lastmod">{{i18n.last_modified}}</th>
					{{/config.showlastmodified}}
					{{#config.showfilesize}}
					<th class="th-size">{{i18n.size}}</th>
					{{/config.showfilesize}}
					{{#config.showpermissions}}
					<th class="th-permissions d-none d-md-table-cell">{{i18n.permissions}}</th>
					{{/config.showpermissions}}
					{{#config.showowner}}
					<th class="th-owner d-none d-lg-table-cell">{{i18n.owner}}</th>
					{{/config.showowner}}
					{{#config.showgroup}}
					<th class="th-group d-none d-xl-table-cell">{{i18n.group}}</th>
					{{/config.showgroup}}
					<th class="th-buttons buttons"></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>        
	</div>
	<div class="container">
		<div class="card ifminfo">
			<div class="card-body p-2">
				<div style="float:left; padding-left: 10px;">Namaskar File Manager is provided by &nbsp;	<a style="float:right; padding-right: 10px;" href="http://github.com/misterunknown/ifm" target="_blank">IFM</a></div>
				
			</div>
		</div>
	</div>
</main>

f00bar;
		$templates['login'] = <<<'f00bar'
<style type="text/css">
html,
body {
  height: 100%;
}

body {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: center;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #f5f5f5;
}

.form-signin {
  width: 100%;
  max-width: 420px;
  padding: 15px;
  margin: auto;
}

.form-label-group {
  position: relative;
  margin-bottom: 1rem;
}

.form-label-group > input,
.form-label-group > label {
  height: 3.125rem;
  padding: .75rem;
}

.form-label-group > label {
  position: absolute;
  top: 0;
  left: 0;
  display: block;
  width: 100%;
  margin-bottom: 0; /* Override default `<label>` margin */
  line-height: 1.5;
  color: #495057;
  pointer-events: none;
  cursor: text; /* Match the input under the label */
  border: 1px solid transparent;
  border-radius: .25rem;
  transition: all .1s ease-in-out;
}

.form-label-group input::-webkit-input-placeholder {
  color: transparent;
}

.form-label-group input:-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-moz-placeholder {
  color: transparent;
}

.form-label-group input::placeholder {
  color: transparent;
}

.form-label-group input:not(:placeholder-shown) {
  padding-top: 1.25rem;
  padding-bottom: .25rem;
}

.form-label-group input:not(:placeholder-shown) ~ label {
  padding-top: .25rem;
  padding-bottom: .25rem;
  font-size: 12px;
  color: #777;
}

/* Fallback for Edge
-------------------------------------------------- */
@supports (-ms-ime-align: auto) {
  .form-label-group > label {
    display: none;
  }
  .form-label-group input::-ms-input-placeholder {
    color: #777;
  }
}

/* Fallback for IE
-------------------------------------------------- */
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
  .form-label-group > label {
    display: none;
  }
  .form-label-group input:-ms-input-placeholder {
    color: #777;
  }
}
</style>

<form class="form-signin" method="POST" action>
  <div class="text-center mb-4">
  	<h1 class="h3 mb-3 font-weight-normal">IFM {{i18n.login}}</h1>
  </div>
  {{error}}
  <div class="form-label-group">
    <input type="text" name="inputLogin" id="inputLogin" class="form-control" placeholder="{{i18n.username}}" required="" autofocus="">
    <label for="inputLogin">{{i18n.username}}</label>
  </div>
  <div class="form-label-group">
    <input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="{{i18n.password}}" required="">
    <label for="inputPassword">{{i18n.password}}</label>
  </div>
  <div class="alert alert-danger d-none" role="alert"></div>
   
  <button class="btn btn-lg btn-primary btn-block" type="submit">{{i18n.login}}</button>
  
</form>
f00bar;
		$templates['filetable'] = <<<'f00bar'
{{#items}}
<tr class="clickable-row {{rowclasses}}" {{{dragdrop}}} data-id="{{guid}}" data-filename="{{name}}" data-eaction="{{eaction}}">
	{{#fixtop}}
	<td data-order="{{fixtop}}"></td>
	{{/fixtop}}
	{{^fixtop}}
	<td data-order="0"></td>
	{{/fixtop}}
	<td>
		<a href="{{{link}}}" tabindex="0" id="{{guid}}" class="ifmitem" {{{popover}}} data-type="{{type}}">
			<span class="{{icon}}"></span>
			{{linkname}}
		</a>
	</td>
	{{#config.download}}
	<td>
		<a href="{{download.link}}"><span class="{{download.icon}}"></span></a>
	</td>
	{{/config.download}}
	{{#config.showlastmodified}}
	<td data-order="{{lastmodified}}">{{lastmodified_hr}}</td>
	{{/config.showlastmodified}}
	{{#config.showfilesize}}
	<td data-order="{{size_raw}}">{{size}}</td>
	{{/config.showfilesize}}
	{{#config.showpermissions}}
	<td class="d-none d-md-table-cell">
		<input type="text" size="11" name="newpermissions" class="form-control {{filepermmode}}" value="{{fileperms}}" data-filename="{{name}}" {{readonly}}>
	</td>
	{{/config.showpermissions}}
	{{#config.showowner}}
	<td class="d-none d-lg-table-cell">
		{{owner}}
	</td>
	{{/config.showowner}}
	{{#config.showgroup}}
	<td class="d-none d-xl-table-cell">
		{{group}}
	</td>
	{{/config.showgroup}}
	<td>
		{{#button}}
		<a tabindex="0" name="do-{{action}}" data-id="{{guid}}">
			<span class="{{icon}}" title="{{title}}"</span>
		</a>
		{{/button}}
	</td>
</tr>
{{/items}}

f00bar;
		$templates['footer'] = <<<'f00bar'
<footer class="footer mt-auto py-3">
	<div id="wq_container" class="container">
		<div class="row">
			<div class="col-md-2 mb-1">
				<a type="button" class="btn btn-light btn-block" name="showAll">{{i18n.tasks}} <span class="badge badge-secondary" name="taskCount">1</span></a>
			</div>
			<div id="waitqueue" class="col-md-10">
			</div>
		</div>
	</div>
</footer>

f00bar;
		$templates['task'] = <<<'f00bar'
<div id="{{id}}" class="card mb-1">
	<div class="card-body">
		<div class="progress">
			<div class="progress-bar bg-{{type}} progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemax="100" style="width:100%">
			{{name}}
			</div>
		</div>
	</div>
</div>

f00bar;
		$templates['ajaxrequest'] = <<<'f00bar'
<form id="formAjaxRequest">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="ajaxurl">URL</label>
			<input class="form-control" type="url" id="ajaxurl" required>
		</div>
		<div class="form-group">
			<label for="ajaxdata">{{i18n.data}}</label>
			<textarea class="form-control" id="ajaxdata"></textarea>
		</div>
		<div class="form-group">
			<legend>{{i18n.method}}</legend>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="radioget" name="arMethod" value="GET">
				<label class="form-check-label" for="radioget">GET</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="radiopost" name="arMethod" value="POST" checked="checked">
				<label class="form-check-label" for="radiopost">POST</label>
			</div>
		</div>
		<button type="button" class="btn btn-success" id="buttonRequest">{{i18n.request}}</button>
		<button type="button" class="btn btn-danger" id="buttonClose">{{i18n.cancel}}</button>
		<div class="form-group">
			<label for="ajaxresponse">{{i18n.response}}</label>
			<textarea class="form-control" id="ajaxresponse"></textarea>
		</div>
	</fieldset>
</div>
</form>

f00bar;
		$templates['copymove'] = <<<'f00bar'
<form id="formCopyMove">
<fieldset>
	<div class="modal-body">
		<div class="form-group">
			<label for="copyMoveTree">{{i18n.select_destination}}:</label>
			<div id="copyMoveTree"><div class="text-center"><span class="icon icon-spin5 animate-spin"></span></div></div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" id="copyButton">{{i18n.copy}}</button>
		<button type="button" class="btn btn-secondary" id="moveButton">{{i18n.move}}</button>
		<button type="button" class="btn btn-danger" id="cancelButton">{{i18n.cancel}}</button>
	</div>
</fieldset>
</form>

f00bar;
		$templates['createdir'] = <<<'f00bar'
<form id="formCreateDir">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="dirname">{{i18n.directoryname}}:</label>
			<input class="form-control" id="dirname" type="text" name="dirname" value="" />
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-success" id="buttonSave">{{i18n.save}}</button>
	<button type="button" class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['createarchive'] = <<<'f00bar'
<form id="formCreateArchive">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="archivename">{{i18n.archivename}}:</label>
			<input id="archivename" class="form-control" type="text" name="archivename" value="" />
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-success" id="buttonSave">{{i18n.save}}</button>
	<button type="button" class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['deletefile'] = <<<'f00bar'
<form id="formDeleteFiles">
<div class="modal-body">
	{{#multiple}}
	<label>{{i18n.file_delete_confirm}} <code>{{count}}</code>?</label>
	{{/multiple}}
	{{^multiple}}
	<label>{{i18n.file_delete_confirm}} <code>{{filename}}</code>?</label>
	{{/multiple}}
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" id="buttonYes">{{i18n.delete}}</button>
	<button type="button" class="btn btn-secondary" id="buttonNo">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['extractfile'] = <<<'f00bar'
<form id="formExtractFile">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label>{{i18n.extract_filename}} {{filename}}:</label>
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text">
						<input type="radio" name="extractTargetLocation" value="./" checked="checked">
					</div>
				</div>
				<input class="form-control" type="text" placeholder="./" readonly>
			</div>
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text">
						<input type="radio" name="extractTargetLocation" value="./{{destination}}">
					</div>
				</div>
				<input class="form-control" type="text" placeholder="./{{destination}}" readonly>
			</div>
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text">
						<input type="radio" name="extractTargetLocation" value="custom">
					</div>
				</div>
				<input id="extractCustomLocation" type="text" class="form-control" placeholder="custom location" value="">
			</div>
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="buttonExtract">{{i18n.extract}}</button>
	<button type="button" class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['file'] = <<<'f00bar'
<form id="formFile">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="filename">{{i18n.filename}}:</label>
			<input type="text" class="form-control" name="filename" id="filename" value="{{filename}}">
		</div>
		<div class="form-group" id="content" name="content"></div>
		<button type="button" class="btn btn-secondary" id="editoroptions">{{i18n.editor_options}}</button>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" id="buttonSave" class="btn btn-primary">{{i18n.save}}</button>
	<button type="button" id="buttonSaveNotClose" class="btn btn-success">{{i18n.save_wo_close}}</button>
	<button type="button" id="buttonClose" class="btn btn-danger">{{i18n.close}}</button>
</div>
</form>

f00bar;
		$templates['file_editoroptions'] = <<<'f00bar'
<form>
	<div class="form-group">
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="editor-wordwrap" 
				{{#wordwrap}}
				checked="checked"
				{{/wordwrap}}
			>
			<label class="form-check-label" for="editor-wordwrap">{{i18n.word_wrap}}</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="editor-softtabs"
				{{#softtabs}}
				checked="checked"
				{{/softtabs}}
			>
			<label class="form-check-label" for="editor-softtabs">{{i18n.soft_tabs}}</label>
		</div>
	</div>
	<div class="input-group">
		<div class="input-group-prepend">
			<span class="input-group-text" id="editor-tabsize-label">{{i18n.tab_size}}</span>
		</div>
		<input class="form-control" type="number" min="1" max="9" maxlength="1" id="editor-tabsize" title="{{i18n.tab_size}}" value="{{tabsize}}" aria-describedby="editor-tabsize-label">
	</div>
	{{#ace_includes}}
	<select class="form-control selectpicker" data-toggle="dropdown" data-live-search="true" data-size="15" id="editor-syntax">
		{{#modes}}
		<option value="ace/mode/{{.}}" {{{ace_mode_selected}}}>{{.}}</option>
		{{/modes}}
	</select>
	{{/ace_includes}}
</form>

f00bar;
		$templates['remoteupload'] = <<<'f00bar'
<form id="formRemoteUpload">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="url">{{i18n.upload_remote_url}}</label>
			<input class="form-control" type="url" id="url" name="url" required>
		</div>
		<div class="form-group">
			<label for="filename">{{i18n.filename}}</label>
			<input class="form-control" type="text" id="filename" name="filename" required>
		</div>
		<div class="form-group">
			<legend>{{i18n.method}}</legend>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="radiocurl" name="method" value="curl" checked="checked">
				<label class="form-check-label" for="radiocurl">cURL</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" id="radiofile" name="method" value="file">
				<label class="form-check-label" for="radiofile">file</label>
			</div>
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="buttonUpload">{{i18n.upload}}</button>
	<button type="button" class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['renamefile'] = <<<'f00bar'
<form id="formRenameFile">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="newname">{{i18n.rename_filename}} {{filename}}:</label>
			<input id="newname" class="form-control" type="text" name="newname" value="" />
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" id="buttonRename">{{i18n.file_rename}}</button>
	<button type="button" class="btn btn-danger" id="buttonCancel">{{i18n.cancel}} </button>
</div>
</form>

f00bar;
		$templates['search'] = <<<'f00bar'
<form id="searchForm">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<label for="searchPattern">{{i18n.search_pattern}}:</label>
			<input type="search" class="form-control" id="searchPattern" name="pattern" autocomplete="off" value="{{lastSearch}}">
		</div>
		<table id="searchResults" class="table">
		</table>
	</fieldset>
</div>
</form>

f00bar;
		$templates['searchresults'] = <<<'f00bar'
{{#items}}
<tr class="{{rowclasses}}" data-filename="{{name}}">
	<td>
		<a tabindex="0" id="{{guid}}" class="searchitem" {{{popover}}} data-type="{{type}}" data-folder="{{folder}}">
			<span class="{{icon}}"></span> {{linkname}} <span style="color:#999">({{folder}})</span>
		</a>
	</td>
</tr>
{{/items}}
{{^items}}
<tr>
	<td>
		No results found.
	</td>
</tr>
{{/items}}

f00bar;
		$templates['uploadfile'] = <<<'f00bar'
<form id="formUploadFile">
<div class="modal-body">
	<fieldset>
		<div class="form-group">
			<div class="custom-file">
				<label class="custom-file-label" for="fileselect">{{i18n.upload_file}}</label>
				<input class="custom-file-input" type="file" name="files" id="fileselect" multiple>
			</div>
		</div>
		<div class="form-group">
			<label for="filename">{{i18n.filename_new}}</label>
			<input class="form-control" type="text" name="newfilename" id="filename">
		</div>
	</fieldset>
</div>
<div class="modal-footer">
	<button class="btn btn-primary" id="buttonUpload">{{i18n.upload}}</button>
	<button class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$templates['uploadconfirmoverwrite'] = <<<'f00bar'
<form id="formUploadConfirmOverwrite">
<div class="modal-body">
{{i18n.upload_overwrite_hint}}
<ul>
{{#files}}
<li>{{.}}</li>
{{/files}}
</ul>
{{i18n.upload_overwrite_confirm}}
</div>
<div class="modal-footer">
	<button class="btn btn-primary" id="buttonConfirm">{{i18n.upload}}</button>
	<button class="btn btn-danger" id="buttonCancel">{{i18n.cancel}}</button>
</div>
</form>

f00bar;
		$this->templates = $templates;

		$i18n = array();
		$i18n["en"] = <<<'f00bar'
{
    "ajax_request": "AJAX request",
    "archive_create_error": "Could not create archive.",
    "archive_create_success": "Archive successfully created.",
    "archive_invalid_format": "Invalid archive format given.",
    "archivename": "Name of the archive",
    "cancel": "Cancel",
    "close": "Close",
    "copy": "Copy",
    "copy_error": "The following files could not be copied:",
    "copy_success": "File(s) copied successfully.",
    "copylink": "Copy link",
    "create_archive": "Create archive",
    "data": "Data",
    "delete": "Delete",
    "directoryname": "Directory Name",
    "download": "Download",
    "edit": "Edit",
    "editor_options": "Editor Options",
    "error": "Error:",
    "extract": "Extract",
    "extract_error": "Could not extract archive.",
    "extract_filename": "Extract file -",
    "extract_success": "Archive extracted successfully.",
    "file_copy_to": "to",
    "file_delete_confirm": "Do you really want to delete the following file -",
    "file_delete_error": "File(s) could not be deleted.",
    "file_delete_success": "File(s) successfully deleted.",
    "file_display_error": "This file cannot be displayed or edited.",
    "file_load_error": "Content could not be loaded.",
    "file_new": "New file",
    "file_no_permission": "No permission to edit/create file.",
    "file_not_found": "File was not found or could not be opened.",
    "file_open_error": "Could not open the file.",
    "file_rename": "Rename File",
    "file_rename_error": "File could not be renamed:",
    "file_rename_success": "File successfully renamed.",
    "file_save_confirm": "Do you want to save the following file -",
    "file_save_error": "File could not be saved.",
    "file_save_success": "File was saved successfully.",
    "file_upload_error": "File could not be uploaded.",
    "file_upload_success": "File successfully uploaded.",
    "filename": "Filename",
    "filename_new": "New Filename",
    "filename_slashes": "The filename must not contain slashes.",
    "filter": "Filter",
    "folder_create_error": "Directory could not be created:",
    "folder_create_success": "Directory sucessfully created.",
    "folder_new": "New Folder",
    "folder_not_found": "The directory could not be found.",
    "folder_tree_load_error": "Error while fetching the folder tree.",
    "footer": "IFM - improved file manager | ifm.php hidden |",
    "general_error": "General error occured: No or broken response.",
    "github": "Visit the project on GitHub",
    "group": "Group",
    "invalid_action": "Invalid action given.",
    "invalid_archive_format": "Invalid archive format given. Possible formats are zip, tar, tar.gz or tar.bz2.",
    "invalid_data": "Invalid data from server.",
    "invalid_dir": "Invalid directory given.",
    "invalid_filename": "Invalid filename given.",
    "invalid_params": "Invalid parameter given.",
    "invalid_url": "Invalid URL given.",
    "json_encode_error": "Could not format the response as JSON:",
    "last_modified": "Last Modified",
    "load_config_error": "Could not load configuration.",
    "load_template_error": "Could not load templates.",
    "load_text_error": "Could not load texts.",
    "login": "Login",
    "login_failed": "Login failed.",
    "logout": "Log Off",
    "method": "Method",
    "move": "Move",
    "move_error": "The following files could not be moved:",
    "move_success": "File(s) successfully moved.",
    "nopermissions": "You do not have the permissions to do that.",
    "options": "Options",
    "owner": "Owner",
    "password": "Password",
    "path_content": "Content of",
    "pattern_error_slashes": "Pattern must not contain slashes.",
    "permission_change_error": "Permissions could not be changed:",
    "permission_change_success": "Permissions successfully changed.",
    "permission_parse_error": "Permissions could not be parsed correctly.",
    "permissions": "Permissions",
    "refresh": "Refresh",
    "remaining_tasks": "There are remaining tasks. Do you really want to reload?",
    "rename": "Rename",
    "rename_filename": "Rename file -",
    "request": "Request",
    "response": "Response",
    "save": "Save",
    "save_wo_close": "Save w/o Close",
    "search": "Search",
    "search_pattern": "Pattern",
    "select_destination": "Select Destination",
    "size": "Size",
    "soft_tabs": "Soft Tabs",
    "tab_size": "Tab Size",
    "tasks": "Tasks",
    "toggle_nav": "Toggle navigation",
    "upload": "Upload",
    "upload_drop": "Drop files to upload",
    "upload_file": "Upload File",
    "upload_overwrite_hint": "The following files will be overwritten:",
    "upload_overwrite_confirm": "Upload anyway?",
    "upload_remote": "Remote Upload",
    "upload_remote_url": "Remote Upload URL",
    "username": "Username",
    "word_wrap": "Word Wrap"
}

f00bar;
		$i18n["en"] = json_decode($i18n["en"], true);

		$this->i18n = $i18n;

		if (in_array($this->config['language'], array_keys($this->i18n)))
			// Merge english with the language in case of missing keys
			$this->l = array_merge($this->i18n['en'], $this->i18n[$this->config['language']]);
		else
			$this->l = $this->i18n['en'];

		if ($this->config['timezone'])
			date_default_timezone_set($this->config['timezone']);
	}

	/**
	 * This function contains the client-side application
	 */
	public function getApplication()
	{
		$this->getHTMLHeader();
		print '<div id="ifm"></div>';
		$this->getJS();
		print '<script>var ifm = new IFM(); ifm.init("ifm");</script>';
		$this->getHTMLFooter();
	}

	public function getInlineApplication()
	{
		$this->getCSS();
		print '<div id="ifm"></div>';
		$this->getJS();
	}

	public function getCSS()
	{
		print '
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/>
            <style type="text/css">'; ?> .treeview .list-group-item{cursor:pointer}.treeview span.indent{margin-left:10px;margin-right:10px}.treeview span.icon,.treeview span.image{width:12px;margin-right:5px}.treeview .node-disabled{color:silver;cursor:not-allowed}.treeview .node-hidden{display:none}.treeview span.image{display:inline-block;height:1.19em;vertical-align:middle;background-size:contain;background-repeat:no-repeat;line-height:1em} <?php print '</style>
            <style type="text/css">'; ?> @font-face {
		font-family: 'fontello';
		src: url('data:application/octet-stream;base64,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') format('woff'),
		url('data:application/octet-stream;base64,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AggGAwEHBgQECgUGBAEIAQIBAwMEBAQEBgEGAggJBQQGAgQDAQgMAQUcBAMCAgEIAQ4BAgcJAwQEAQQCAwEHCgIEBQ0DAxQOEwQIBgECAQIFCQIBEwkGBAIFBgoDCAQHBQIDBgkEBgEFCQQFAwMCBQQBDgcLDwQQAwMBCAQIAQgDAQgEAwICAwQCBBIFAwwMAQMDAgwZGwMGBQUTBQMLBA0LAQQCBgQIBAkEUTIEBQIGBQMBGAoBAgcFBAMEBAQBAgEBAQIKBwcSBAcJBAMIBAIOAQECAg4CBAICDwgDBAMCAwUBBAoKAQQIBAUMBwIDCAMJBxYGBgUICBAEFAoBAgQCBgMOAwQBCgUIEQoCAgICAQUCBAEKAgMMAwIIAQIIAwEDAgcLBAECAggUAwgKAQIBBAIDBQIBAwIBAwEEGAMJAwEBAQMNAg4EAgMBBAMFAgYIBAICAQgEBAcIBQcMBAQCAgIGAQUEAwIDBQwEAhIBBAICBQ4JAgIKCAUJAgYGBwUJDAppc1ABDAENAQQDFQEDBQIDAgIBBQwIAwYGBgYBAQQIBAoBBwYCCgIEAQwBAQICBAsPAQIJCgEDC3TE6sR0dMTqxHT+3QEIAgYGAQQIAwULAQwBAwICDAEKBwIDBAIEAQIGDAUGAwMCBAEBAwMEAgQBAwMCAggEAgYEAQMEAQQEBgcDCAcKBwQFBgUMAwECBAIBAwwJDgMEBQcIBQMRAgMOCAUMAwEDCQkGBAMGAQ4ECgQBAgUCAgYKBAcHBwEJBQgHCAMCBwMCBAIGAgQFCgMDDgIFAgIFBAcCAQoIDwIDAwcDAg4DAgMEBgQGBAQBAS1PBAEIBAMEBg8KAgYEBQQFDgkUCwIBBhoCARcFBAYDBRQDAxAFAgEECAUIBAELGA0FDAICBAQMCA4EDgEKCxQHCAEFAw0CAQIBEgMKBAQJBQYCAwoDAgMFDAIQCBIDAwQEBgIECgcOAQUCBAEEAgIQBQ8FAgUDAgsCCAQEAgIEGA4JDgUJAQQGAQIDAgEEAwYHBgUCDwoBBAECAwECAwgFFwQCCAgDBQ4CCgoFAQIDBAsJBQICAgIGAgoGCgQEBAMBBAoEBgEHAgEHBgUEAgMBBQQC/g0VVQICBQQGAg8BAQIBAgEBAwIKAwYCAgUGBwMOBgIBBQQCCAECCAICAgIFHAgRCQ4JDAIEEAcAAv//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ZW4LdHJhc2gtZW1wdHkEcGx1cwxwbHVzLWNpcmNsZWQFbWludXMNbWludXMtY2lyY2xlZAlkb3duLW9wZW4HdXAtb3BlbgZ3cmVuY2gHY29nLWFsdANjb2cFYmxvY2sJYXR0ZW50aW9uB3NxdWFyZXMGbG9nb3V0BWdsb2JlBnNlYXJjaAVzcGluMwVzcGluNAVzcGluNQVzcGluNghsaW5rLWV4dARkb2NzBHNvcnQJc29ydC1kb3duB3NvcnQtdXAOZG93bmxvYWQtY2xvdWQMdXBsb2FkLWNsb3VkCGRvYy10ZXh0DHBsdXMtc3F1YXJlZAhhbmdsZS11cAphbmdsZS1kb3duDGZvbGRlci1lbXB0eRFmb2xkZXItb3Blbi1lbXB0eQh0ZXJtaW5hbA1taW51cy1zcXVhcmVkEW1pbnVzLXNxdWFyZWQtYWx0DnBlbmNpbC1zcXVhcmVkDGxpbmstZXh0LWFsdAdkb2MtaW52DGRvYy10ZXh0LWludgxzb3J0LW5hbWUtdXAOc29ydC1uYW1lLWRvd24Lc29ydC1hbHQtdXANc29ydC1hbHQtZG93bg5zb3J0LW51bWJlci11cBBzb3J0LW51bWJlci1kb3duEHBsdXMtc3F1YXJlZC1hbHQIbGFuZ3VhZ2UIZmlsZS1wZGYJZmlsZS13b3JkCmZpbGUtZXhjZWwPZmlsZS1wb3dlcnBvaW50CmZpbGUtaW1hZ2UMZmlsZS1hcmNoaXZlCmZpbGUtYXVkaW8KZmlsZS12aWRlbwlmaWxlLWNvZGUHc2xpZGVycwVzaGFyZQ1zaGFyZS1zcXVhcmVkBXRyYXNoAAAAAAEAAf//AA8AAAAAAAAAAAAAAAAAAAAAABgAGAAYABgDUv9bA1L/W7AALCCwAFVYRVkgIEu4AA5RS7AGU1pYsDQbsChZYGYgilVYsAIlYbkIAAgAY2MjYhshIbAAWbAAQyNEsgABAENgQi2wASywIGBmLbACLCBkILDAULAEJlqyKAEKQ0VjRVJbWCEjIRuKWCCwUFBYIbBAWRsgsDhQWCGwOFlZILEBCkNFY0VhZLAoUFghsQEKQ0VjRSCwMFBYIbAwWRsgsMBQWCBmIIqKYSCwClBYYBsgsCBQWCGwCmAbILA2UFghsDZgG2BZWVkbsAErWVkjsABQWGVZWS2wAywgRSCwBCVh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UlggPFkusSsBFCstsDwsIyAuRrACJUZQWCA8WS6xKwEUKy2wPSwjIC5GsAIlRlJYIDxZIyAuRrACJUZQWCA8WS6xKwEUKy2wPiywNSsjIC5GsAIlRlJYIDxZLrErARQrLbA/LLA2K4ogIDywBCNCijgjIC5GsAIlRlJYIDxZLrErARQrsARDLrArKy2wQCywABawBCWwBCYgLkcjRyNhsAlDKyMgPCAuIzixKwEUKy2wQSyxCAQlQrAAFrAEJbAEJSAuRyNHI2EgsAQjQrAJQysgsGBQWCCwQFFYswIgAyAbswImAxpZQkIjIEewBEOwAmIgsABQWLBAYFlmsAFjYCCwASsgiophILACQ2BkI7ADQ2FkUFiwAkNhG7ADQ2BZsAMlsAJiILAAUFiwQGBZZrABY2GwAiVGYTgjIDwjOBshICBGI0ewASsjYTghWbErARQrLbBCLLA1Ky6xKwEUKy2wQyywNishIyAgPLAEI0IjOLErARQrsARDLrArKy2wRCywABUgR7AAI0KyAAEBFRQTLrAxKi2wRSywABUgR7AAI0KyAAEBFRQTLrAxKi2wRiyxAAEUE7AyKi2wRyywNCotsEgssAAWRSMgLiBGiiNhOLErARQrLbBJLLAII0KwSCstsEossgAAQSstsEsssgABQSstsEwssgEAQSstsE0ssgEBQSstsE4ssgAAQistsE8ssgABQistsFAssgEAQistsFEssgEBQistsFIssgAAPistsFMssgABPistsFQssgEAPistsFUssgEBPistsFYssgAAQCstsFcssgABQCstsFgssgEAQCstsFkssgEBQCstsFossgAAQystsFsssgABQystsFwssgEAQystsF0ssgEBQystsF4ssgAAPystsF8ssgABPystsGAssgEAPystsGEssgEBPystsGIssDcrLrErARQrLbBjLLA3K7A7Ky2wZCywNyuwPCstsGUssAAWsDcrsD0rLbBmLLA4Ky6xKwEUKy2wZyywOCuwOystsGgssDgrsDwrLbBpLLA4K7A9Ky2waiywOSsusSsBFCstsGsssDkrsDsrLbBsLLA5K7A8Ky2wbSywOSuwPSstsG4ssDorLrErARQrLbBvLLA6K7A7Ky2wcCywOiuwPCstsHEssDorsD0rLbByLLMJBAIDRVghGyMhWUIrsAhlsAMkUHiwARUwLQBLuADIUlixAQGOWbABuQgACABjcLEABUKyAAEAKrEABUKzCgIBCCqxAAVCsw4AAQgqsQAGQroCwAABAAkqsQAHQroAQAABAAkqsQMARLEkAYhRWLBAiFixA2REsSYBiFFYugiAAAEEQIhjVFixAwBEWVlZWbMMAgEMKrgB/4WwBI2xAgBEAAA=') format('truetype');
		}
		/* Chrome hack: SVG is rendered more smooth in Windozze. 100% magic, uncomment if you need it. */
		/* Note, that will break hinting! In other OS-es font will be not as sharp as it could be */
		/*
		@media screen and (-webkit-min-device-pixel-ratio:0) {
		@font-face {
		font-family: 'fontello';
		src: url('../font/fontello.svg?80353264#fontello') format('svg');
		}
		}
		*/

		[class^="icon-"]:before, [class*=" icon-"]:before {
		font-family: "fontello";
		font-style: normal;
		font-weight: normal;
		speak: none;

		display: inline-block;
		text-decoration: inherit;
		width: 1em;
		margin-right: .2em;
		text-align: center;
		/* opacity: .8; */

		/* For safety - reset parent styles, that can break glyph codes*/
		font-variant: normal;
		text-transform: none;

		/* fix buttons height, for twitter bootstrap */
		line-height: 1em;

		/* Animation center compensation - margins should be symmetric */
		/* remove if not needed */
		margin-left: .2em;

		/* you can be more comfortable with increased icons size */
		/* font-size: 120%; */

		/* Uncomment for 3D effect */
		/* text-shadow: 1px 1px 1px rgba(127, 127, 127, 0.3); */
		}
		.icon-arrows-cw:before { content: '\e800'; } /* '' */
		.icon-doc:before { content: '\e801'; } /* '' */
		.icon-archive:before { content: '\e802'; } /* '' */
		.icon-download:before { content: '\e803'; } /* '' */
		.icon-upload:before { content: '\e804'; } /* '' */
		.icon-edit:before { content: '\e805'; } /* '' */
		.icon-pencil:before { content: '\e806'; } /* '' */
		.icon-folder:before { content: '\e807'; } /* '' */
		.icon-folder-open:before { content: '\e808'; } /* '' */
		.icon-trash-empty:before { content: '\e809'; } /* '' */
		.icon-plus:before { content: '\e80a'; } /* '' */
		.icon-plus-circled:before { content: '\e80b'; } /* '' */
		.icon-minus:before { content: '\e80c'; } /* '' */
		.icon-minus-circled:before { content: '\e80d'; } /* '' */
		.icon-down-open:before { content: '\e80e'; } /* '' */
		.icon-up-open:before { content: '\e80f'; } /* '' */
		.icon-wrench:before { content: '\e810'; } /* '' */
		.icon-cog-alt:before { content: '\e811'; } /* '' */
		.icon-cog:before { content: '\e812'; } /* '' */
		.icon-block:before { content: '\e813'; } /* '' */
		.icon-attention:before { content: '\e814'; } /* '' */
		.icon-squares:before { content: '\e815'; } /* '' */
		.icon-logout:before { content: '\e816'; } /* '' */
		.icon-globe:before { content: '\e817'; } /* '' */
		.icon-search:before { content: '\e818'; } /* '' */
		.icon-spin3:before { content: '\e832'; } /* '' */
		.icon-spin4:before { content: '\e834'; } /* '' */
		.icon-spin5:before { content: '\e838'; } /* '' */
		.icon-spin6:before { content: '\e839'; } /* '' */
		.icon-link-ext:before { content: '\f08e'; } /* '' */
		.icon-docs:before { content: '\f0c5'; } /* '' */
		.icon-sort:before { content: '\f0dc'; } /* '' */
		.icon-sort-down:before { content: '\f0dd'; } /* '' */
		.icon-sort-up:before { content: '\f0de'; } /* '' */
		.icon-download-cloud:before { content: '\f0ed'; } /* '' */
		.icon-upload-cloud:before { content: '\f0ee'; } /* '' */
		.icon-doc-text:before { content: '\f0f6'; } /* '' */
		.icon-plus-squared:before { content: '\f0fe'; } /* '' */
		.icon-angle-up:before { content: '\f106'; } /* '' */
		.icon-angle-down:before { content: '\f107'; } /* '' */
		.icon-folder-empty:before { content: '\f114'; } /* '' */
		.icon-folder-open-empty:before { content: '\f115'; } /* '' */
		.icon-terminal:before { content: '\f120'; } /* '' */
		.icon-minus-squared:before { content: '\f146'; } /* '' */
		.icon-minus-squared-alt:before { content: '\f147'; } /* '' */
		.icon-pencil-squared:before { content: '\f14b'; } /* '' */
		.icon-link-ext-alt:before { content: '\f14c'; } /* '' */
		.icon-doc-inv:before { content: '\f15b'; } /* '' */
		.icon-doc-text-inv:before { content: '\f15c'; } /* '' */
		.icon-sort-name-up:before { content: '\f15d'; } /* '' */
		.icon-sort-name-down:before { content: '\f15e'; } /* '' */
		.icon-sort-alt-up:before { content: '\f160'; } /* '' */
		.icon-sort-alt-down:before { content: '\f161'; } /* '' */
		.icon-sort-number-up:before { content: '\f162'; } /* '' */
		.icon-sort-number-down:before { content: '\f163'; } /* '' */
		.icon-plus-squared-alt:before { content: '\f196'; } /* '' */
		.icon-language:before { content: '\f1ab'; } /* '' */
		.icon-file-pdf:before { content: '\f1c1'; } /* '' */
		.icon-file-word:before { content: '\f1c2'; } /* '' */
		.icon-file-excel:before { content: '\f1c3'; } /* '' */
		.icon-file-powerpoint:before { content: '\f1c4'; } /* '' */
		.icon-file-image:before { content: '\f1c5'; } /* '' */
		.icon-file-archive:before { content: '\f1c6'; } /* '' */
		.icon-file-audio:before { content: '\f1c7'; } /* '' */
		.icon-file-video:before { content: '\f1c8'; } /* '' */
		.icon-file-code:before { content: '\f1c9'; } /* '' */
		.icon-sliders:before { content: '\f1de'; } /* '' */
		.icon-share:before { content: '\f1e0'; } /* '' */
		.icon-share-squared:before { content: '\f1e1'; } /* '' */
		.icon-trash:before { content: '\f1f8'; } /* '' */ <?php print '</style>
            <style type="text/css">'; ?> /*
		Animation example, for spinners
		*/
		.animate-spin {
		-moz-animation: spin 2s infinite linear;
		-o-animation: spin 2s infinite linear;
		-webkit-animation: spin 2s infinite linear;
		animation: spin 2s infinite linear;
		display: inline-block;
		}
		@-moz-keyframes spin {
		0% {
		-moz-transform: rotate(0deg);
		-o-transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		}

		100% {
		-moz-transform: rotate(359deg);
		-o-transform: rotate(359deg);
		-webkit-transform: rotate(359deg);
		transform: rotate(359deg);
		}
		}
		@-webkit-keyframes spin {
		0% {
		-moz-transform: rotate(0deg);
		-o-transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		}

		100% {
		-moz-transform: rotate(359deg);
		-o-transform: rotate(359deg);
		-webkit-transform: rotate(359deg);
		transform: rotate(359deg);
		}
		}
		@-o-keyframes spin {
		0% {
		-moz-transform: rotate(0deg);
		-o-transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		}

		100% {
		-moz-transform: rotate(359deg);
		-o-transform: rotate(359deg);
		-webkit-transform: rotate(359deg);
		transform: rotate(359deg);
		}
		}
		@-ms-keyframes spin {
		0% {
		-moz-transform: rotate(0deg);
		-o-transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		}

		100% {
		-moz-transform: rotate(359deg);
		-o-transform: rotate(359deg);
		-webkit-transform: rotate(359deg);
		transform: rotate(359deg);
		}
		}
		@keyframes spin {
		0% {
		-moz-transform: rotate(0deg);
		-o-transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
		}

		100% {
		-moz-transform: rotate(359deg);
		-o-transform: rotate(359deg);
		-webkit-transform: rotate(359deg);
		transform: rotate(359deg);
		}
		}
		<?php print '</style>
            <style type="text/css">'; ?> body {
		padding-top: 70px;
		overflow-y: scroll !important;
		padding-right: 0px !important;
		}

		main {
		margin-bottom: 1rem;
		}

		a {
		cursor: pointer !important;
		}

		a.ifmitem:focus {
		outline: 0
		}

		img.imgpreview {
		max-width: 100%;
		background-repeat: repeat repeat;
		background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAAAAACoWZBhAAABI2lDQ1BJQ0MgcHJvZmlsZQAAKJGdkLFKw1AUhr9UsaXYSXEQhwyuBRHM5FIVgqAQYwWrU5qkWExiSFKKb+Cb6MN0EASfwCdQcPa/0cHBLF44/B+Hc/7/3gstOwnTcnkH0qwqXH8wuhxd2e032nTosksvCMt84HknNJ7PVyyjL33j1Tz351mJ4jKULlRZmBcVWPtiZ17lhlWs3w79Q/GD2I7SLBI/ibejNDJsdv00mYU/nuY2q3F2cW76qi1cjjnFw2bMjCkJFX1pps4RDntSl4KAe0pCaUKs3lwzFTeiUk4uB6KhSLdpyNus8zyljOUxlZdJuCOVp8nD/O/32sdZvWltLPKgCOrWkqo1mcD7I/RGsPYM3euGrM7vtzXMOPXMP9/4BdaxUFxWskm6AAAAAmJLR0QAy6Y7OAoAAAAJcEhZcwAALiMAAC4jAXilP3YAAAAiSURBVAjXY/zPwMDAcIaBgYGBiQEOCDJZzjAwMDCYkKoNAPmXAuEuYku0AAAAAElFTkSuQmCC");
		}

		div#content { /* is for the ACE editor */
		width: 100%;
		height: 350px;
		}

		/* Make tables more compact (overwrites bootstrap default of 0.75rem) */
		.table td, .table th {
		padding: 0.25rem;
		}

		/* narrow navbar */
		.navbar {
		padding: 0.3rem !important;
		}

		/*
		* Icon size
		*/
		.icon {
		font-size: 14pt;
		}
		@media (max-width: 768px) {
		.icon { font-size: 12pt; }
		#filetable tr th.buttons { min-width: 85px !important; }
		}

		/*
		* Filetable related settings
		*/
		#filetable th {
		border-top: 0;
		}
		#filetable td:nth-child(5), #filetable th:nth-child(5) {
		text-align: center;
		}
		#filetable td:nth-child(6), #filetable th:nth-child(6) {
		text-align: center;
		}
		#filetable tr td:last-child {
		text-align: right;
		}
		#filetable td:last-child a:hover {
		text-decoration: none;
		}
		.td-permissions { width: 1px; }

		input[name=newpermissions] {
		padding: 6px 8px;
		width: 6.5rem;
		}

		#filetable tr th.buttons { min-width: 95px; }
		#filetable tbody tr.highlightedItem { box-shadow: 0px 0px 10px 2px #337ab7; }
		#filetable tbody tr.highlightedItem td:first-child a { outline: none; }
		#filetable tbody tr.selectedItem { background-color: #337ab7; color: #FFF; }
		#filetable tbody tr.selectedItem * a { color: #FFF; }
		#filetable tbody tr td { vertical-align: inherit; }

		div.ifminfo { color: #adadad; font-size: 10pt; }
		div.ifminfo div.panel div.panel-body { padding: 0px !important; text-align: center; }

		/*
		* Footer / Task-Queue settings
		*/
		footer {
		position: fixed;
		padding-top: 1em;
		border-top: 1px;
		background-color: #EEE;
		bottom: 0;
		width: 100%;
		overflow: hidden;
		}

		#waitqueue {
		max-height: 6rem;
		}

		#waitqueue .progress {
		position: relative;
		margin-bottom: 0;
		}
		#waitqueue .progbarlabel {
		position:absolute;
		top: 0;
		left: 10px;
		font-weight: bold;
		}

		/*
		* File drop overlay
		*/
		#filedropoverlay {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		text-align: center;
		color: #FFF;
		background-color: lightblue;
		filter: alpha(opacity=70);
		-moz-opacity: 0.7;
		opacity: 0.7;
		z-index: 1000;
		}

		#filedropoverlay h1 {
		border-radius: 5px;
		color: #000;
		position:relative;
		top:50%;
		font-size: 6em;
		pointer-events: none;
		}


		/*
		* Datatables related settings
		*/
		table.dataTable thead th {
		position: relative;
		background-image: none !important;
		}

		/* remove original sort icons */
		table.dataTable thead .sorting:before,
		table.dataTable thead .sorting_asc:before,
		table.dataTable thead .sorting_desc:before,
		table.dataTable thead .sorting_asc_disabled:before,
		table.dataTable thead .sorting_desc_disabled:before {
		right: 0 !important;
		content: "" !important;
		}
		/* custom sort icons */
		table.dataTable thead th.sorting:after,
		table.dataTable thead th.sorting_asc:after,
		table.dataTable thead th.sorting_desc:after {
		position: absolute;
		top: 6px;
		right: 8px;
		display: block;
		font-family: fontello;
		font-size: 0.8em;
		opacity: 1;
		color: #000;
		}
		table.dataTable thead th.sorting:after {
		content: "\F0DC";
		color: #ddd;
		}
		table.dataTable thead th.sorting_asc:after {
		content: "\f0de";
		}
		table.dataTable thead th.sorting_desc:after {
		content: "\f0dd";
		}

		/*
		* Modal related settings
		*/
		#copyMoveTree {
		max-height: 80vh;
		overflow: auto;
		}


		@media (min-width: 576px) {
		.modal-dialog {
		max-width: 600px;
		margin: 1.75rem auto;
		}
		}

		@media (min-width: 992px) {
		.modal-lg, .modal-xl {
		max-width: 800px;
		}
		}
<?php print '</style>
        ';
	}

	public function getJS()
	{
		print '
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
            <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.8/ace.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.2/mustache.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.7/bootstrap-notify.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/classnames/2.2.6/index.min.js"></script> 
        ';
		echo <<<'f00bar'
        <script>
                    !function(a,b,c,d){"use strict";var e="treeview",f={};f.settings={injectStyle:!0,levels:2,expandIcon:"glyphicon glyphicon-plus",collapseIcon:"glyphicon glyphicon-minus",loadingIcon:"glyphicon glyphicon-hourglass",emptyIcon:"glyphicon",nodeIcon:"",selectedIcon:"",checkedIcon:"glyphicon glyphicon-check",partiallyCheckedIcon:"glyphicon glyphicon-expand",uncheckedIcon:"glyphicon glyphicon-unchecked",tagsClass:"badge",color:d,backColor:d,borderColor:d,changedNodeColor:"#39A5DC",onhoverColor:"#F5F5F5",selectedColor:"#FFFFFF",selectedBackColor:"#428bca",searchResultColor:"#D9534F",searchResultBackColor:d,highlightSelected:!0,highlightSearchResults:!0,showBorder:!0,showIcon:!0,showImage:!1,showCheckbox:!1,checkboxFirst:!1,highlightChanges:!1,showTags:!1,multiSelect:!1,preventUnselect:!1,allowReselect:!1,hierarchicalCheck:!1,propagateCheckEvent:!1,wrapNodeText:!1,onLoading:d,onLoadingFailed:d,onInitialized:d,onNodeRendered:d,onRendered:d,onDestroyed:d,onNodeChecked:d,onNodeCollapsed:d,onNodeDisabled:d,onNodeEnabled:d,onNodeExpanded:d,onNodeSelected:d,onNodeUnchecked:d,onNodeUnselected:d,onSearchComplete:d,onSearchCleared:d},f.options={silent:!1,ignoreChildren:!1},f.searchOptions={ignoreCase:!0,exactMatch:!1,revealResults:!0},f.dataUrl={method:"GET",dataType:"json",cache:!1};var g=function(b,c){return this.$element=a(b),this._elementId=b.id,this._styleId=this._elementId+"-style",this._init(c),{options:this._options,init:a.proxy(this._init,this),remove:a.proxy(this._remove,this),findNodes:a.proxy(this.findNodes,this),getNodes:a.proxy(this.getNodes,this),getParents:a.proxy(this.getParents,this),getSiblings:a.proxy(this.getSiblings,this),getSelected:a.proxy(this.getSelected,this),getUnselected:a.proxy(this.getUnselected,this),getExpanded:a.proxy(this.getExpanded,this),getCollapsed:a.proxy(this.getCollapsed,this),getChecked:a.proxy(this.getChecked,this),getUnchecked:a.proxy(this.getUnchecked,this),getDisabled:a.proxy(this.getDisabled,this),getEnabled:a.proxy(this.getEnabled,this),addNode:a.proxy(this.addNode,this),addNodeAfter:a.proxy(this.addNodeAfter,this),addNodeBefore:a.proxy(this.addNodeBefore,this),removeNode:a.proxy(this.removeNode,this),updateNode:a.proxy(this.updateNode,this),selectNode:a.proxy(this.selectNode,this),unselectNode:a.proxy(this.unselectNode,this),toggleNodeSelected:a.proxy(this.toggleNodeSelected,this),collapseAll:a.proxy(this.collapseAll,this),collapseNode:a.proxy(this.collapseNode,this),expandAll:a.proxy(this.expandAll,this),expandNode:a.proxy(this.expandNode,this),toggleNodeExpanded:a.proxy(this.toggleNodeExpanded,this),revealNode:a.proxy(this.revealNode,this),checkAll:a.proxy(this.checkAll,this),checkNode:a.proxy(this.checkNode,this),uncheckAll:a.proxy(this.uncheckAll,this),uncheckNode:a.proxy(this.uncheckNode,this),toggleNodeChecked:a.proxy(this.toggleNodeChecked,this),unmarkCheckboxChanges:a.proxy(this.unmarkCheckboxChanges,this),disableAll:a.proxy(this.disableAll,this),disableNode:a.proxy(this.disableNode,this),enableAll:a.proxy(this.enableAll,this),enableNode:a.proxy(this.enableNode,this),toggleNodeDisabled:a.proxy(this.toggleNodeDisabled,this),search:a.proxy(this.search,this),clearSearch:a.proxy(this.clearSearch,this)}};g.prototype._init=function(b){this._tree=[],this._initialized=!1,this._options=a.extend({},f.settings,b),this._template.icon.empty.addClass(this._options.emptyIcon),this._destroy(),this._subscribeEvents(),this._triggerEvent("loading",null,f.options),this._load(b).then(a.proxy(function(b){return this._tree=a.extend(!0,[],b)},this),a.proxy(function(a){this._triggerEvent("loadingFailed",a,f.options)},this)).then(a.proxy(function(a){return this._setInitialStates({nodes:a},0)},this)).then(a.proxy(function(){this._render()},this))},g.prototype._load=function(b){var c=new a.Deferred;return b.data?this._loadLocalData(b,c):b.dataUrl&&this._loadRemoteData(b,c),c.promise()},g.prototype._loadRemoteData=function(b,c){a.ajax(a.extend(!0,{},f.dataUrl,b.dataUrl)).done(function(a){c.resolve(a)}).fail(function(a,b,d){c.reject(d)})},g.prototype._loadLocalData=function(b,c){c.resolve("string"==typeof b.data?JSON.parse(b.data):a.extend(!0,[],b.data))},g.prototype._remove=function(){this._destroy(),a.removeData(this,e),a("#"+this._styleId).remove()},g.prototype._destroy=function(){this._initialized&&(this._initialized=!1,this._triggerEvent("destroyed",null,f.options),this._unsubscribeEvents(),this.$wrapper.remove(),this.$wrapper=null)},g.prototype._unsubscribeEvents=function(){this.$element.off("loading"),this.$element.off("loadingFailed"),this.$element.off("initialized"),this.$element.off("nodeRendered"),this.$element.off("rendered"),this.$element.off("destroyed"),this.$element.off("click"),this.$element.off("nodeChecked"),this.$element.off("nodeCollapsed"),this.$element.off("nodeDisabled"),this.$element.off("nodeEnabled"),this.$element.off("nodeExpanded"),this.$element.off("nodeSelected"),this.$element.off("nodeUnchecked"),this.$element.off("nodeUnselected"),this.$element.off("searchComplete"),this.$element.off("searchCleared")},g.prototype._subscribeEvents=function(){this._unsubscribeEvents(),"function"==typeof this._options.onLoading&&this.$element.on("loading",this._options.onLoading),"function"==typeof this._options.onLoadingFailed&&this.$element.on("loadingFailed",this._options.onLoadingFailed),"function"==typeof this._options.onInitialized&&this.$element.on("initialized",this._options.onInitialized),"function"==typeof this._options.onNodeRendered&&this.$element.on("nodeRendered",this._options.onNodeRendered),"function"==typeof this._options.onRendered&&this.$element.on("rendered",this._options.onRendered),"function"==typeof this._options.onDestroyed&&this.$element.on("destroyed",this._options.onDestroyed),this.$element.on("click",a.proxy(this._clickHandler,this)),"function"==typeof this._options.onNodeChecked&&this.$element.on("nodeChecked",this._options.onNodeChecked),"function"==typeof this._options.onNodeCollapsed&&this.$element.on("nodeCollapsed",this._options.onNodeCollapsed),"function"==typeof this._options.onNodeDisabled&&this.$element.on("nodeDisabled",this._options.onNodeDisabled),"function"==typeof this._options.onNodeEnabled&&this.$element.on("nodeEnabled",this._options.onNodeEnabled),"function"==typeof this._options.onNodeExpanded&&this.$element.on("nodeExpanded",this._options.onNodeExpanded),"function"==typeof this._options.onNodeSelected&&this.$element.on("nodeSelected",this._options.onNodeSelected),"function"==typeof this._options.onNodeUnchecked&&this.$element.on("nodeUnchecked",this._options.onNodeUnchecked),"function"==typeof this._options.onNodeUnselected&&this.$element.on("nodeUnselected",this._options.onNodeUnselected),"function"==typeof this._options.onSearchComplete&&this.$element.on("searchComplete",this._options.onSearchComplete),"function"==typeof this._options.onSearchCleared&&this.$element.on("searchCleared",this._options.onSearchCleared)},g.prototype._triggerEvent=function(b,c,d){d&&!d.silent&&this.$element.trigger(b,a.extend(!0,{},c))},g.prototype._setInitialStates=function(b,c){return this._nodes={},a.when.apply(this,this._setInitialState(b,c)).done(a.proxy(function(){this._orderedNodes=this._sortNodes(),this._inheritCheckboxChanges(),this._triggerEvent("initialized",this._orderedNodes,f.options)},this))},g.prototype._setInitialState=function(b,c,e){if(b.nodes){c+=1,e=e||[];var f=b;return a.each(b.nodes,a.proxy(function(b,g){var h=new a.Deferred;e.push(h.promise()),g.level=c,g.index=b,g.nodeId=f&&f.nodeId?f.nodeId+"."+g.index:c-1+"."+g.index,g.parentId=f.nodeId,g.hasOwnProperty("selectable")||(g.selectable=!0),g.hasOwnProperty("checkable")||(g.checkable=!0),g.state=g.state||{},g.state.hasOwnProperty("checked")||(g.state.checked=!1),this._options.hierarchicalCheck&&"undefined"===g.state.checked&&(g.state.checked=d),g.state.hasOwnProperty("disabled")||(g.state.disabled=!1),g.state.hasOwnProperty("expanded")||(!g.state.disabled&&c<this._options.levels&&g.nodes&&g.nodes.length>0?g.state.expanded=!0:g.state.expanded=!1),g.state.hasOwnProperty("selected")||(g.state.selected=!1),f&&f.state&&f.state.expanded||c<=this._options.levels?g.state.visible=!0:g.state.visible=!1,g.nodes&&(g.nodes.length>0?this._setInitialState(g,c,e):delete g.nodes),this._nodes[g.nodeId]=g,h.resolve()},this)),e}},g.prototype._sortNodes=function(){return a.map(Object.keys(this._nodes).sort(function(a,b){if(a===b)return 0;for(var a=a.split(".").map(function(a){return parseInt(a)}),b=b.split(".").map(function(a){return parseInt(a)}),c=Math.max(a.length,b.length),e=0;e<c;e++){if(a[e]===d)return-1;if(b[e]===d)return 1;if(a[e]-b[e]>0)return 1;if(a[e]-b[e]<0)return-1}}),a.proxy(function(a,b){return this._nodes[a]},this))},g.prototype._clickHandler=function(b){var c=a(b.target),d=this.targetNode(c);if(d&&!d.state.disabled){var e=c.attr("class")?c.attr("class").split(" "):[];e.indexOf("expand-icon")!==-1?this._toggleExpanded(d,a.extend({},f.options)):e.indexOf("check-icon")!==-1?d.checkable&&this._toggleChecked(d,a.extend({},f.options)):d.selectable?this._toggleSelected(d,a.extend({},f.options)):this._toggleExpanded(d,a.extend({},f.options))}},g.prototype.targetNode=function(a){var b=a.closest("li.list-group-item").attr("data-nodeId"),c=this._nodes[b];return c||console.log("Error: node does not exist"),c},g.prototype._toggleExpanded=function(a,b){a&&("function"==typeof this._options.lazyLoad&&a.lazyLoad?this._lazyLoad(a):this._setExpanded(a,!a.state.expanded,b))},g.prototype._lazyLoad=function(a){a.$el.children("span.expand-icon").removeClass(this._options.expandIcon).addClass(this._options.loadingIcon);var b=this;this._options.lazyLoad(a,function(c){b.addNode(c,a)}),delete a.lazyLoad},g.prototype._setExpanded=function(b,c,d){d&&c===b.state.expanded||(c&&b.nodes?(b.state.expanded=!0,b.$el&&b.$el.children("span.expand-icon").removeClass(this._options.expandIcon).removeClass(this._options.loadingIcon).addClass(this._options.collapseIcon),b.nodes&&d&&a.each(b.nodes,a.proxy(function(a,b){this._setVisible(b,!0,d)},this)),this._triggerEvent("nodeExpanded",b,d)):c||(b.state.expanded=!1,b.$el&&b.$el.children("span.expand-icon").removeClass(this._options.collapseIcon).addClass(this._options.expandIcon),b.nodes&&d&&a.each(b.nodes,a.proxy(function(a,b){this._setVisible(b,!1,d),this._setExpanded(b,!1,d)},this)),this._triggerEvent("nodeCollapsed",b,d)))},g.prototype._setVisible=function(a,b,c){c&&b===a.state.visible||(b?(a.state.visible=!0,a.$el&&a.$el.removeClass("node-hidden")):(a.state.visible=!1,a.$el&&a.$el.addClass("node-hidden")))},g.prototype._toggleSelected=function(a,b){if(a)return this._setSelected(a,!a.state.selected,b),this},g.prototype._setSelected=function(b,c,d){if(!d||c!==b.state.selected){if(c)this._options.multiSelect||a.each(this._findNodes("true","state.selected"),a.proxy(function(b,c){this._setSelected(c,!1,a.extend(d,{unselecting:!0}))},this)),b.state.selected=!0,b.$el&&(b.$el.addClass("node-selected"),(b.selectedIcon||this._options.selectedIcon)&&b.$el.children("span.node-icon").removeClass(b.icon||this._options.nodeIcon).addClass(b.selectedIcon||this._options.selectedIcon)),this._triggerEvent("nodeSelected",b,d);else{if(this._options.preventUnselect&&d&&!d.unselecting&&1===this._findNodes("true","state.selected").length)return this._options.allowReselect&&this._triggerEvent("nodeSelected",b,d),this;b.state.selected=!1,b.$el&&(b.$el.removeClass("node-selected"),(b.selectedIcon||this._options.selectedIcon)&&b.$el.children("span.node-icon").removeClass(b.selectedIcon||this._options.selectedIcon).addClass(b.icon||this._options.nodeIcon)),this._triggerEvent("nodeUnselected",b,d)}return this}},g.prototype._inheritCheckboxChanges=function(){this._options.showCheckbox&&this._options.highlightChanges&&(this._checkedNodes=a.grep(this._orderedNodes,function(a){return a.state.checked}))},g.prototype._toggleChecked=function(b,c){if(b){if(this._options.hierarchicalCheck){var e,f=a.extend({},c,{silent:c.silent||!this._options.propagateCheckEvent}),g=b;for(b.state.checked=!b.state.checked;g=this._nodes[g.parentId];)e=g.nodes.reduce(function(a,b){return a===b.state.checked?a:d},g.nodes[0].state.checked),this._setChecked(g,e,f);if(b.nodes&&b.nodes.length>0)for(var h,i=b.nodes.slice();i&&i.length>0;)h=i.pop(),this._setChecked(h,b.state.checked,f),h.nodes&&h.nodes.length>0&&(i=i.concat(h.nodes.slice()));b.state.checked=!b.state.checked}this._setChecked(b,!b.state.checked,c)}},g.prototype._setChecked=function(a,b,c){c&&b===a.state.checked||(this._options.highlightChanges&&a.$el.toggleClass("node-check-changed",this._checkedNodes.indexOf(a)==-1==b),b?(a.state.checked=!0,a.$el&&(a.$el.addClass("node-checked").removeClass("node-checked-partial"),a.$el.children("span.check-icon").removeClass(this._options.uncheckedIcon).removeClass(this._options.partiallyCheckedIcon).addClass(this._options.checkedIcon)),this._triggerEvent("nodeChecked",a,c)):b===d&&this._options.hierarchicalCheck?(a.state.checked=d,a.$el&&(a.$el.addClass("node-checked-partial").removeClass("node-checked"),a.$el.children("span.check-icon").removeClass(this._options.uncheckedIcon).removeClass(this._options.checkedIcon).addClass(this._options.partiallyCheckedIcon)),this._triggerEvent("nodeUnchecked",a,c)):(a.state.checked=!1,a.$el&&(a.$el.removeClass("node-checked node-checked-partial"),a.$el.children("span.check-icon").removeClass(this._options.checkedIcon).removeClass(this._options.partiallyCheckedIcon).addClass(this._options.uncheckedIcon)),this._triggerEvent("nodeUnchecked",a,c)))},g.prototype._setDisabled=function(a,b,c){c&&b===a.state.disabled||(b?(a.state.disabled=!0,c&&!c.keepState&&(this._setSelected(a,!1,c),this._setChecked(a,!1,c),this._setExpanded(a,!1,c)),a.$el&&a.$el.addClass("node-disabled"),this._triggerEvent("nodeDisabled",a,c)):(a.state.disabled=!1,a.$el&&a.$el.removeClass("node-disabled"),this._triggerEvent("nodeEnabled",a,c)))},g.prototype._setSearchResult=function(a,b,c){c&&b===a.searchResult||(b?(a.searchResult=!0,a.$el&&a.$el.addClass("node-result")):(a.searchResult=!1,a.$el&&a.$el.removeClass("node-result")))},g.prototype._render=function(){this._initialized||(this.$wrapper=this._template.tree.clone(),this.$element.empty().addClass(e).append(this.$wrapper),this._injectStyle(),this._initialized=!0);var b;a.each(this._orderedNodes,a.proxy(function(a,c){this._renderNode(c,b),b=c},this)),this._triggerEvent("rendered",this._orderedNodes,f.options)},g.prototype._renderNode=function(b,c){if(b){b.$el?b.$el.empty():b.$el=this._newNodeEl(b,c).addClass("node-"+this._elementId),b.$el.addClass(b["class"]),b.id&&b.$el.attr("id",b.id),b.dataAttr&&a.each(b.dataAttr,function(a,c){b.$el.attr("data-"+a,c)}),b.$el.attr("data-nodeId",b.nodeId),b.tooltip&&b.$el.attr("title",b.tooltip);for(var e=0;e<b.level-1;e++)b.$el.append(this._template.indent.clone());if(b.$el.append(b.nodes||b.lazyLoad?this._template.icon.expand.clone():this._template.icon.empty.clone()),this._options.checkboxFirst?(this._addCheckbox(b),this._addIcon(b),this._addImage(b)):(this._addIcon(b),this._addImage(b),this._addCheckbox(b)),this._options.wrapNodeText){var g=this._template.text.clone();b.$el.append(g),g.append(b.text)}else b.$el.append(b.text);this._options.showTags&&b.tags&&a.each(b.tags,a.proxy(function(a,c){b.$el.append(this._template.badge.clone().addClass(("object"==typeof c?c["class"]:d)||b.tagsClass||this._options.tagsClass).append(("object"==typeof c?c.text:d)||c))},this)),this._setSelected(b,b.state.selected),this._setChecked(b,b.state.checked),this._setSearchResult(b,b.searchResult),this._setExpanded(b,b.state.expanded),this._setDisabled(b,b.state.disabled),this._setVisible(b,b.state.visible),this._triggerEvent("nodeRendered",b,f.options)}},g.prototype._addCheckbox=function(a){!this._options.showCheckbox||a.hideCheckbox!==d&&a.hideCheckbox!==!1||a.$el.append(this._template.icon.check.clone())},g.prototype._addIcon=function(a){!this._options.showIcon||this._options.showImage&&a.image||a.$el.append(this._template.icon.node.clone().addClass(a.icon||this._options.nodeIcon))},g.prototype._addImage=function(a){this._options.showImage&&a.image&&a.$el.append(this._template.image.clone().addClass("node-image").css("background-image","url('"+a.image+"')"))},g.prototype._newNodeEl=function(a,b){var c=this._template.node.clone();return b?b.$el.after(c):this.$wrapper.prepend(c),c},g.prototype._removeNodeEl=function(b){b&&(b.nodes&&a.each(b.nodes,a.proxy(function(a,b){this._removeNodeEl(b)},this)),b.$el.remove())},g.prototype._expandNode=function(b){b.nodes&&a.each(b.nodes.slice(0).reverse(),a.proxy(function(a,c){c.level=b.level+1,this._renderNode(c,b.$el)},this))},g.prototype._injectStyle=function(){this._options.injectStyle&&!c.getElementById(this._styleId)&&a('<style type="text/css" id="'+this._styleId+'"> '+this._buildStyle()+" </style>").appendTo("head")},g.prototype._buildStyle=function(){var b=".node-"+this._elementId+"{";if(this._options.color&&(b+="color:"+this._options.color+";"),this._options.backColor&&(b+="background-color:"+this._options.backColor+";"),this._options.showBorder?this._options.borderColor&&(b+="border:1px solid "+this._options.borderColor+";"):b+="border:none;",b+="}",this._options.onhoverColor&&(b+=".node-"+this._elementId+":not(.node-disabled):hover{background-color:"+this._options.onhoverColor+";}"),this._options.highlightSearchResults&&(this._options.searchResultColor||this._options.searchResultBackColor)){var c="";this._options.searchResultColor&&(c+="color:"+this._options.searchResultColor+";"),this._options.searchResultBackColor&&(c+="background-color:"+this._options.searchResultBackColor+";"),b+=".node-"+this._elementId+".node-result{"+c+"}",b+=".node-"+this._elementId+".node-result:hover{"+c+"}"}if(this._options.highlightSelected&&(this._options.selectedColor||this._options.selectedBackColor)){var c="";this._options.selectedColor&&(c+="color:"+this._options.selectedColor+";"),this._options.selectedBackColor&&(c+="background-color:"+this._options.selectedBackColor+";"),b+=".node-"+this._elementId+".node-selected{"+c+"}",b+=".node-"+this._elementId+".node-selected:hover{"+c+"}"}if(this._options.highlightChanges){var c="color: "+this._options.changedNodeColor+";";b+=".node-"+this._elementId+".node-check-changed{"+c+"}"}return a.each(this._orderedNodes,a.proxy(function(a,c){if(c.color||c.backColor){var d="";c.color&&(d+="color:"+c.color+";"),c.backColor&&(d+="background-color:"+c.backColor+";"),b+=".node-"+this._elementId+'[data-nodeId="'+c.nodeId+'"]{'+d+"}"}if(c.iconColor){var d="color:"+c.iconColor+";";b+=".node-"+this._elementId+'[data-nodeId="'+c.nodeId+'"] .node-icon{'+d+"}"}},this)),this._css+b},g.prototype._template={tree:a('<ul class="list-group"></ul>'),node:a('<li class="list-group-item"></li>'),indent:a('<span class="indent"></span>'),icon:{node:a('<span class="icon node-icon"></span>'),expand:a('<span class="icon expand-icon"></span>'),check:a('<span class="icon check-icon"></span>'),empty:a('<span class="icon"></span>')},image:a('<span class="image"></span>'),badge:a("<span></span>"),text:a('<span class="text"></span>')},g.prototype._css=".treeview .list-group-item{cursor:pointer}.treeview span.indent{margin-left:10px;margin-right:10px}.treeview span.icon{width:12px;margin-right:5px}.treeview .node-disabled{color:silver;cursor:not-allowed}",g.prototype.findNodes=function(a,b){return this._findNodes(a,b)},g.prototype.getNodes=function(){return this._orderedNodes},g.prototype.getParents=function(b){b instanceof Array||(b=[b]);var c=[];return a.each(b,a.proxy(function(a,b){var d=!!b.parentId&&this._nodes[b.parentId];d&&c.push(d)},this)),c},g.prototype.getSiblings=function(b){b instanceof Array||(b=[b]);var c=[];return a.each(b,a.proxy(function(a,b){var d=this.getParents([b]),e=d[0]?d[0].nodes:this._tree;c=e.filter(function(a){return a.nodeId!==b.nodeId})},this)),a.map(c,function(a){return a})},g.prototype.getSelected=function(){return this._findNodes("true","state.selected")},g.prototype.getUnselected=function(){return this._findNodes("false","state.selected")},g.prototype.getExpanded=function(){return this._findNodes("true","state.expanded")},g.prototype.getCollapsed=function(){return this._findNodes("false","state.expanded")},g.prototype.getChecked=function(){return this._findNodes("true","state.checked")},g.prototype.getUnchecked=function(){return this._findNodes("false","state.checked")},g.prototype.getDisabled=function(){return this._findNodes("true","state.disabled")},g.prototype.getEnabled=function(){return this._findNodes("false","state.disabled")},g.prototype.addNode=function(b,c,d,e){b instanceof Array||(b=[b]),c instanceof Array&&(c=c[0]),e=a.extend({},f.options,e);var g;g=c&&c.nodes?c.nodes:c?c.nodes=[]:this._tree,a.each(b,a.proxy(function(a,b){var c="number"==typeof d?d+a:g.length+1;g.splice(c,0,b)},this)),this._setInitialStates({nodes:this._tree},0).done(a.proxy(function(){c&&!c.state.expanded&&this._setExpanded(c,!0,e),this._render()},this))},g.prototype.addNodeAfter=function(b,c,d){b instanceof Array||(b=[b]),c instanceof Array&&(c=c[0]),d=a.extend({},f.options,d),this.addNode(b,this.getParents(c)[0],c.index+1,d)},g.prototype.addNodeBefore=function(b,c,d){b instanceof Array||(b=[b]),c instanceof Array&&(c=c[0]),d=a.extend({},f.options,d),this.addNode(b,this.getParents(c)[0],c.index,d)},g.prototype.removeNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c);var d,e;a.each(b,a.proxy(function(a,b){e=this._nodes[b.parentId],d=e?e.nodes:this._tree,d.splice(b.index,1),this._removeNodeEl(b)},this)),this._setInitialStates({nodes:this._tree},0).done(this._render.bind(this))},g.prototype.updateNode=function(b,c,d){b instanceof Array&&(b=b[0]),d=a.extend({},f.options,d);var e,g=this._nodes[b.parentId];e=g?g.nodes:this._tree,e.splice(b.index,1,c),this._removeNodeEl(b),this._setInitialStates({nodes:this._tree},0).done(this._render.bind(this))},g.prototype.selectNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setSelected(b,!0,c)},this))},g.prototype.unselectNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setSelected(b,!1,c)},this))},g.prototype.toggleNodeSelected=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._toggleSelected(b,c)},this))},g.prototype.collapseAll=function(b){b=a.extend({},f.options,b),b.levels=b.levels||999,this.collapseNode(this._tree,b)},g.prototype.collapseNode=function(b,c){c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setExpanded(b,!1,c)},this))},g.prototype.expandAll=function(b){b=a.extend({},f.options,b),b.levels=b.levels||999,this.expandNode(this._tree,b)},g.prototype.expandNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){b.state.expanded||("function"==typeof this._options.lazyLoad&&b.lazyLoad&&this._lazyLoad(b),this._setExpanded(b,!0,c),b.nodes&&this._expandLevels(b.nodes,c.levels-1,c))},this))},g.prototype._expandLevels=function(b,c,d){b instanceof Array||(b=[b]),d=a.extend({},f.options,d),a.each(b,a.proxy(function(a,b){this._setExpanded(b,c>0,d),b.nodes&&this._expandLevels(b.nodes,c-1,d)},this))},g.prototype.revealNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){for(var d,e=b;d=this.getParents([e])[0];)e=d,this._setExpanded(e,!0,c)},this))},g.prototype.toggleNodeExpanded=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._toggleExpanded(b,c)},this))},g.prototype.checkAll=function(b){b=a.extend({},f.options,b);var c=a.grep(this._orderedNodes,function(a){return!a.state.checked});a.each(c,a.proxy(function(a,c){this._setChecked(c,!0,b)},this))},g.prototype.checkNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setChecked(b,!0,c)},this))},g.prototype.uncheckAll=function(b){b=a.extend({},f.options,b);var c=a.grep(this._orderedNodes,function(a){return a.state.checked||a.state.checked===d});a.each(c,a.proxy(function(a,c){this._setChecked(c,!1,b)},this))},g.prototype.uncheckNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setChecked(b,!1,c)},this))},g.prototype.toggleNodeChecked=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._toggleChecked(b,c)},this))},g.prototype.unmarkCheckboxChanges=function(){this._inheritCheckboxChanges(),a.each(this._nodes,function(a,b){b.$el.removeClass("node-check-changed")})},g.prototype.disableAll=function(b){b=a.extend({},f.options,b);var c=this._findNodes("false","state.disabled");a.each(c,a.proxy(function(a,c){this._setDisabled(c,!0,b)},this))},g.prototype.disableNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setDisabled(b,!0,c)},this))},g.prototype.enableAll=function(b){b=a.extend({},f.options,b);var c=this._findNodes("true","state.disabled");a.each(c,a.proxy(function(a,c){this._setDisabled(c,!1,b)},this))},g.prototype.enableNode=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setDisabled(b,!1,c)},this))},g.prototype.toggleNodeDisabled=function(b,c){b instanceof Array||(b=[b]),c=a.extend({},f.options,c),a.each(b,a.proxy(function(a,b){this._setDisabled(b,!b.state.disabled,c)},this))},g.prototype.search=function(b,c){c=a.extend({},f.searchOptions,c);var d=this._getSearchResults(),e=[];if(b&&b.length>0){c.exactMatch&&(b="^"+b+"$");var g="g";c.ignoreCase&&(g+="i"),e=this._findNodes(b,"text",g)}return a.each(this._diffArray(e,d),a.proxy(function(a,b){this._setSearchResult(b,!1,c)},this)),a.each(this._diffArray(d,e),a.proxy(function(a,b){this._setSearchResult(b,!0,c)},this)),e&&c.revealResults&&this.revealNode(e),this._triggerEvent("searchComplete",e,c),e},g.prototype.clearSearch=function(b){b=a.extend({},{render:!0},b);var c=a.each(this._getSearchResults(),a.proxy(function(a,c){this._setSearchResult(c,!1,b)},this));this._triggerEvent("searchCleared",c,b)},g.prototype._getSearchResults=function(){return this._findNodes("true","searchResult")},g.prototype._diffArray=function(b,c){var d=[];return a.grep(c,function(c){a.inArray(c,b)===-1&&d.push(c)}),d},g.prototype._findNodes=function(b,c,d){return c=c||"text",d=d||"g",a.grep(this._orderedNodes,a.proxy(function(a){var e=this._getNodeValue(a,c);if("string"==typeof e)return e.match(new RegExp(b,d))},this))},g.prototype._getNodeValue=function(a,b){var c=b.indexOf(".");if(c>0){var e=a[b.substring(0,c)],f=b.substring(c+1,b.length);return this._getNodeValue(e,f)}return a.hasOwnProperty(b)&&a[b]!==d?a[b].toString():d};var h=function(a){b.console&&b.console.error(a)};a.fn[e]=function(b,c){var d;if(0==this.length)throw"No element has been found!";return this.each(function(){var f=a.data(this,e);"string"==typeof b?f?a.isFunction(f[b])&&"_"!==b.charAt(0)?(c instanceof Array||(c=[c]),d=f[b].apply(f,c)):h("No such method : "+b):h("Not initialized, can not call method : "+b):"boolean"==typeof b?d=f:a.data(this,e,new g(this,a.extend(!0,{},b)))}),d||this}}(jQuery,window,document);
                    (function(){function l(b){var a=$('<div class="dropdown bootstrapMenu" style="z-index:10000;position:absolute;" />'),c=$('<ul class="dropdown-menu" style="position:static;display:block;font-size:0.9em;" />'),e=[[]];_.each(b.options.actionsGroups,function(b,a){e[a+1]=[]});var d=!1;_.each(b.options.actions,function(a,c){var h=!1;_.each(b.options.actionsGroups,function(b,a){_.includes(b,c)&&(e[a+1].push(c),h=!0)});!1===h&&e[0].push(c);"undefined"!==typeof a.iconClass&&(d=!0)});var f=!0;_.each(e,function(a){0!=
a.length&&(!1===f&&c.append('<li class="dropdown-divider"></li>'),f=!1,_.each(a,function(a){var h=b.options.actions[a];!0===d?c.append('<li role="presentation" data-action="'+a+'"><a href="#" role="menuitem" class="dropdown-item"><i class="fa fa-fw fa-lg '+(h.iconClass||"")+'"></i> <span class="actionName"></span></a></li>'):c.append('<li role="presentation" data-action="'+a+'"><a href="#" role="menuitem" class="dropdown-item"><span class="actionName"></span></a></li>')}),c.append('<li role="presentation" class="noActionsMessage disabled"><a href="#" role="menuitem" class="dropdown-item"><span>'+
b.options.noActionsMessage+"</span></a></li>"))});return a.append(c)}function m(b){var a=null;switch(b.options.menuEvent){case "click":a="click";break;case "right-click":a="contextmenu";break;case "hover":a="mouseenter";break;default:throw Error("Unknown BootstrapMenu 'menuEvent' option");}b.$container.on(a+b.namespace,b.selector,function(a){var c=$(this);b.open(c,a);return!1})}function n(b){b.$menu.on(b.options._actionSelectEvent+b.namespace,function(a){a.preventDefault();a.stopPropagation();if((a=
$(a.target).closest("[data-action]"))&&a.length&&!a.is(".disabled")){a=a.data("action");var c=b.options.fetchElementData(b.$openTarget);b.options.actions[a].onClick(c);b.close()}})}function p(b){switch(b.options.menuEvent){case "click":break;case "right-click":break;case "hover":var a=b.$openTarget.add(b.$menu);a.on("mouseleave"+b.closeNamespace,function(c){c=c.toElement||c.relatedTarget;b.$openTarget.is(c)||b.$menu.is(c)||(a.off(b.closeNamespace),b.close())});break;default:throw Error("Unknown BootstrapMenu 'menuEvent' option");
}b.$container.on("click"+b.closeNamespace,function(){b.close()})}var q={container:"body",fetchElementData:_.noop,menuSource:"mouse",menuPosition:"belowLeft",menuEvent:"right-click",actionsGroups:[],noActionsMessage:"No available actions",_actionSelectEvent:"click"},d=function(b,a){this.selector=b;this.options=_.extend({},q,a);this.namespace=_.uniqueId(".BootstrapMenu_");this.closeNamespace=_.uniqueId(".BootstrapMenuClose_");this.init()},g=[];d.prototype.init=function(){this.$container=$(this.options.container);
this.$menu=l(this);this.$menuList=this.$menu.children();this.$menu.hide().appendTo(this.$container);this.openEvent=this.$openTarget=null;m(this);n(this);g.push(this)};d.prototype.updatePosition=function(){switch(this.options.menuSource){case "element":var b=this.$openTarget;break;case "mouse":b=this.openEvent;break;default:throw Error("Unknown BootstrapMenu 'menuSource' option");}switch(this.options.menuPosition){case "belowRight":var a="right top";var c="right bottom";break;case "belowLeft":a="left top";
c="left bottom";break;case "aboveRight":a="right bottom";c="right top";break;case "aboveLeft":a="left bottom";c="left top";break;default:throw Error("Unknown BootstrapMenu 'menuPosition' option");}this.$menu.css({display:"block"});this.$menu.css({height:this.$menuList.height(),width:this.$menuList.width()});this.$menu.position({my:a,at:c,of:b})};d.prototype.open=function(b,a){var c=this;d.closeAll();this.$openTarget=b;this.openEvent=a;var e=c.options.fetchElementData(c.$openTarget),g=this.$menu.find("[data-action]"),
f=this.$menu.find(".noActionsMessage");g.show();f.hide();var k=0;g.each(function(){var b=$(this),a=b.data("action");a=c.options.actions[a];var d=a.classNames||null;d&&_.isFunction(d)&&(d=d(e));b.attr("class",classNames(d||""));a.isShown&&!1===a.isShown(e)?b.hide():(k++,b.find(".actionName").html(_.isFunction(a.name)&&a.name(e)||a.name),a.isEnabled&&!1===a.isEnabled(e)&&b.addClass("disabled"))});0===k&&f.show();this.updatePosition();this.$menu.show();p(this)};d.prototype.close=function(){this.$menu.hide();
this.$container.off(this.closeNamespace)};d.prototype.destroy=function(){this.close();this.$container.off(this.namespace);this.$menu.off(this.namespace)};d.closeAll=function(){_.each(g,function(b){b.close()})};window.BootstrapMenu=d})(jQuery);
                    /**
 * IFM constructor
 *
 * @param object params - object with some configuration values, currently you only can set the api url
 */
function IFM(params) {
	// reference to ourself, because "this" does not work within callbacks
	var self = this;

	params = params || {};
	// set the backend for the application
	self.api = params.api || window.location.href.replace(/#.*/, "");

	this.editor = null;		// global ace editor
	this.fileChanged = false;	// flag for check if file was changed already
	this.currentDir = "";		// this is the global variable for the current directory; it is used for AJAX requests
	this.rootElement = "";		// global root element, currently not used
	this.fileCache = [];		// holds the current set of files
	this.search = {};		// holds the last search query, as well as the search results

	// This indicates if the modal was closed by a button or not, to prevent the user
	// from accidentially close it while editing a file.
	this.isModalClosedByButton = false;

	this.datatable = null; // Reference for the data table

	/**
	 * Shows a bootstrap modal
	 *
	 * @param {string} content - HTML content of the modal
	 * @param {object} options - options for the modal ({ large: false })
	 */
	this.showModal = function( content, options ) {
		options = options || {};
		var modal = document.createElement( 'div' );
		modal.classList.add( 'modal' );
		modal.id = 'ifmmodal';
		modal.attributes.role = 'dialog';
		var modalDialog = document.createElement( 'div' );
		modalDialog.classList.add( 'modal-dialog' );
		modalDialog.attributes.role = 'document';
		if( options.large == true ) modalDialog.classList.add( 'modal-lg' );
		var modalContent = document.createElement('div');
		modalContent.classList.add( 'modal-content' );
		modalContent.innerHTML = content;
		modalDialog.appendChild( modalContent );
		modal.appendChild( modalDialog );
		document.body.appendChild( modal );

		// For this we have to use jquery, because bootstrap modals depend on them. Also the bs.modal
		// events require jquery, as they cannot be handled by addEventListener()
		$(modal)
			.on( 'hide.bs.modal', function( e ) {
				if( document.forms.formFile && self.fileChanged && !self.isModalClosedByButton ) {
					self.log( "Prevented closing modal because the file was changed and no button was clicked." );
					e.preventDefault();
				} else
					$(this).remove();
			})
			.on( 'shown.bs.modal', function( e ) {
				var formElements = $(this).find('input, button');
				if( formElements.length > 0 ) {
					formElements.first().focus();
				}
			})
			.modal('show');
	};

	/**
	 * Hides a the current bootstrap modal
	 */
	this.hideModal = function() {
		// Hide the modal via jquery to get the hide.bs.modal event triggered
		$( '#ifmmodal' ).modal( 'hide' );
		self.isModalClosedByButton = false;
	};

	/**
	 * Refreshes the file table
	 */
	this.refreshFileTable = function () {
		var taskid = self.generateGuid();
		self.task_add( { id: taskid, name: self.i18n.refresh } );
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "getFiles",
				dir: self.currentDir
			},
			dataType: "json",
			success: self.rebuildFileTable,
			error: function() { self.showMessage( self.i18n.general_error, "e" ); },
			complete: function() { self.task_done( taskid ); }
		});
	};

	/**
	 * Rebuilds the file table with fetched items
	 *
	 * @param object data - object with items
	 */
	this.rebuildFileTable = function( data ) {
		if( data.status == "ERROR" ) {
			this.showMessage( data.message, "e" );
			return;
		} else if ( ! Array.isArray( data ) ) {
			this.showMessage( self.i18n.invalid_data, "e" );
			return;
		}
		data.forEach( function( item ) {
			item.guid = self.generateGuid();
			item.linkname = ( item.name == ".." ) ? "[ up ]" : item.name;
			if( item.name == ".." )
				item.fixtop = 100;
			item.download = {};
			item.download.name = ( item.name == ".." ) ? "." : item.name;
			item.lastmodified_hr = self.formatDate( item.lastmodified );
			if( ! self.config.chmod )
				item.readonly = "readonly";
			if( self.config.edit || self.config.rename || self.config.delete || self.config.extract || self.config.copymove ) {
				item.ftbuttons = true;
				item.button = [];
			}
			if( item.type == "dir" ) {
				if( self.config.download && self.config.zipnload ) {
					item.download.action = "zipnload";
					item.download.icon = "icon icon-download-cloud";
				}
				item.rowclasses = "isDir";
			} else {
				if( self.config.download ) {
					item.download.action = "download";
					item.download.icon = "icon icon-download";
				}
				if( item.icon.indexOf( 'file-image' ) !== -1  ) {
					item.popover = 'data-toggle="popover"';
				}
				if( self.config.extract && self.inArray( item.ext, ["zip","tar","tgz","tar.gz","tar.xz","tar.bz2"] ) ) {
					item.eaction = "extract";
					item.button.push({
						action: "extract",
						icon: "icon icon-archive",
						title: "extract"
					});
				} else if(
					self.config.edit &&
					(
						self.config.disable_mime_detection ||
						(
							typeof item.mime_type === "string" && (
								item.mime_type.substr( 0, 4 ) == "text"
								|| item.mime_type.indexOf("x-empty") != -1
								|| item.mime_type.indexOf("xml") != -1
								|| item.mime_type.indexOf("json") != -1
							)
						)
					)
				) {
					item.eaction = "edit";
					item.button.push({
						action: "edit",
						icon: "icon icon-pencil",
						title: "edit"
					});
				}
			}
			item.download.link = self.api+"?api="+item.download.action+"&dir="+self.hrefEncode(self.currentDir)+"&filename="+self.hrefEncode(item.download.name);
			if( self.config.isDocroot && !self.config.forceproxy )
				item.link = self.hrefEncode( self.pathCombine( window.location.path, self.currentDir, item.name ) );
			else if (self.config.download && self.config.zipnload) {
				if (self.config.root_public_url) {
					if (self.config.root_public_url.charAt(0) == "/")
						item.link = self.pathCombine(window.location.origin, self.config.root_public_url, self.hrefEncode(self.currentDir), self.hrefEncode(item.name) );
					else
						item.link = self.pathCombine(self.config.root_public_url, self.hrefEncode(self.currentDir), self.hrefEncode(item.name) );
				} else
					item.link = self.api+"?api="+(item.download.action=="zipnload"?"zipnload":"proxy")+"&dir="+self.hrefEncode(self.currentDir)+"&filename="+self.hrefEncode(item.download.name);
			} else
				item.link = '#';
			if( ! self.inArray( item.name, [".", ".."] ) ) {
				item.dragdrop = 'draggable="true"';
				if( self.config.copymove )
					item.button.push({
						action: "copymove",
						icon: "icon icon-folder-open-empty",
						title: "copy/move"
					});
				if( self.config.rename )
					item.button.push({
						action: "rename",
						icon: "icon icon-terminal",
						title: "rename"
					});
				if( self.config.delete )
					item.button.push({
						action: "delete",
						icon: "icon icon-trash",
						title: "delete"
					});
			}
		});

		// save items to file cache
		self.fileCache = data;


		// build new tbody and replace the old one with the new
		var newTBody = Mustache.render( self.templates.filetable, { items: data, config: self.config, i18n: self.i18n, api: self.api } );
		var filetable = document.getElementById( 'filetable' );
		filetable.tBodies[0].remove();
		filetable.append( document.createElement( 'tbody' ) );
		filetable.tBodies[0].innerHTML = newTBody;

		if( self.datatable ) self.datatable.destroy();
		self.datatable = $('#filetable').DataTable({
			paging: self.config.pagination,
			pageLength: 50,
			info: false,
			autoWidth: false,
			columnDefs: [
				{ "orderable": false, "targets": ["th-download","th-permissions","th-buttons"] }
			],
			orderFixed: [0, 'desc'],
			language: {
				"search": self.i18n.filter
			},
			stateSave: true
		});


		// add event listeners
		filetable.tBodies[0].addEventListener( 'keypress', function( e ) {
			if( e.target.name == 'newpermissions' && !!self.config.chmod && e.key == 'Enter' )
				self.changePermissions( e.target.dataset.filename, e.target.value );
		});
		filetable.tBodies[0].addEventListener( 'click', function( e ) {
			if( e.target.tagName == "TD" && e.target.parentElement.classList.contains( 'clickable-row' ) && e.target.parentElement.dataset.filename !== ".." && e.ctrlKey )
				e.target.parentElement.classList.toggle( 'selectedItem' );
			else if( e.target.classList.contains( 'ifmitem' ) || e.target.parentElement.classList.contains( 'ifmitem' ) ) {
				ifmitem = ( e.target.classList.contains( 'ifmitem' ) ? e.target : e.target.parentElement );
				if( ifmitem.dataset.type == "dir" ) {
					e.stopPropagation();
					e.preventDefault();
					self.changeDirectory( ifmitem.parentElement.parentElement.dataset.filename );
				}
			} else if( e.target.parentElement.name == 'start_download' ) {
				e.stopPropagation();
				e.preventDefault();
				document.forms["d_"+e.target.parentElement.dataset.guid].submit();
			} else if( e.target.parentElement.name && e.target.parentElement.name.substring(0, 3) == "do-" ) {
				e.stopPropagation();
				e.preventDefault();
				var item = self.fileCache.find( function( x ) { if( x.guid === e.target.parentElement.dataset.id ) return x; } );
				switch( e.target.parentElement.name.substr( 3 ) ) {
					case "rename":
						self.showRenameFileDialog( item.name );
						break;
					case "extract":
						self.showExtractFileDialog( item.name );
						break;
					case "edit":
						self.editFile( item.name );
						break;
					case "delete":
						self.showDeleteDialog( item );
						break;
					case "copymove":
						self.showCopyMoveDialog( item );
						break;
				}
			}
		});
		// has to be jquery, since this is a bootstrap feature
		$( 'a[data-toggle="popover"]' ).popover({
			content: function() {
				var item = self.fileCache.find( x => x.guid == $(this).attr('id') );
				var popover = document.createElement( 'img' );
				if( self.config.isDocroot )
					popover.src = encodeURI( self.pathCombine( self.currentDir, item.name ) ).replace( '#', '%23' ).replace( '?', '%3F' );
				else
					popover.src = self.api + "?api=proxy&dir=" + encodeURIComponent( self.currentDir ) + "&filename=" + encodeURIComponent( item.name );
				popover.classList.add( 'imgpreview' );
				return popover;
			},
			animated: 'fade',
			placement: 'bottom',
			trigger: 'hover',
			html: true
		});

		if( self.config.contextmenu && !!( self.config.edit || self.config.extract || self.config.rename || self.config.copymove || self.config.download || self.config.delete ) ) {
			// create the context menu, this also uses jquery, AFAIK
			var contextMenu = new BootstrapMenu( '.clickable-row', {
				fetchElementData: function( row ) {
					var data = {};
					data.selected =
						Array.prototype.slice.call( document.getElementsByClassName( 'selectedItem' ) )
						.map( function(e){ return self.fileCache.find( x => x.guid == e.children[0].children[0].id ); } );
					data.clicked = self.fileCache.find( x => x.guid == row[0].children[0].children[0].id );
					return data;
				},
				actionsGroups:[
					['edit', 'extract', 'rename', 'copylink'],
					['copymove', 'download', 'createarchive', 'delete']
				],
				actions: {
					edit: {
						name: self.i18n.edit,
						onClick: function( data ) {
							self.editFile( data.clicked.name );
						},
						iconClass: "icon icon-pencil",
						isShown: function( data ) {
							return !!( self.config.edit && data.clicked.eaction == "edit" && !data.selected.length );
						}
					},
					extract: {
						name: self.i18n.extract,
						onClick: function( data ) {
							self.showExtractFileDialog( data.clicked.name );
						},
						iconClass: "icon icon-archive",
						isShown: function( data ) {
							return !!( self.config.extract && data.clicked.eaction == "extract" && !data.selected.length );
						}
					},
					rename: {
						name: self.i18n.rename,
						onClick: function( data ) {
							self.showRenameFileDialog( data.clicked.name );
						},
						iconClass: "icon icon-terminal",
						isShown: function( data ) { return !!( self.config.rename && !data.selected.length && data.clicked.name != ".." ); }
					},
					copylink: {
						name: self.i18n.copylink,
						onClick: function( data ) {
							if( data.clicked.link.toLowerCase().substr(0,4) == "http" )
								self.copyToClipboard( data.clicked.link );
							else {
								var pathname = window.location.pathname.replace( /^\/*/g, '' ).split( '/' );
								pathname.pop();
								var link = self.pathCombine( window.location.origin, data.clicked.link )
								if( pathname.length > 0 )
									link = self.pathCombine( window.location.origin, pathname.join( '/' ), data.clicked.link )
								self.copyToClipboard( link );
							}
						},
						iconClass: "icon icon-link-ext",
						isShown: function( data ) { return !!( !data.selected.length && data.clicked.name != ".." ); }
					},
					copymove: {
						name: function( data ) {
							if( data.selected.length > 0 )
								return self.i18n.copy+'/'+self.i18n.move+' <span class="badge">'+data.selected.length+'</span>';
							else
								return self.i18n.copy+'/'+self.i18n.move;
						},
						onClick: function( data ) {
							if( data.selected.length > 0 )
								self.showCopyMoveDialog( data.selected );
							else
								self.showCopyMoveDialog( data.clicked );
						},
						iconClass: "icon icon-folder-empty",
						isShown: function( data ) { return !!( self.config.copymove && data.clicked.name != ".." ); }
					},
					download: {
						name: function( data ) {
							if( data.selected.length > 0 )
								return self.i18n.download+' <span class="badge">'+data.selected.length+'</span>';
							else
								return self.i18n.download;
						},
						onClick: function( data ) {
							if( data.selected.length > 0 )
								self.showMessage( "At the moment it is not possible to download a set of files." );
							else
								window.location = data.clicked.download.link;
						},
						iconClass: "icon icon-download",
						isShown: function() { return !!self.config.download; }
					},
					createarchive: {
						name: function( data ) {
							if( data.selected.length > 0 )
								return self.i18n.create_archive+' <span class="badge">'+data.selected.length+'</span>';
							else
								return self.i18n.create_archive;
						},
						onClick: function( data ) {
							if( data.selected.length > 0 )
								self.showCreateArchiveDialog( data.selected );
							else
								self.showCreateArchiveDialog( data.clicked );
						},
						iconClass: "icon icon-archive",
						isShown: function( data ) { return !!( self.config.createarchive && data.clicked.name != ".." ); }
					},
					'delete': {
						name: function( data ) {
							if( data.selected.length > 0 )
								return self.i18n.delete+' <span class="badge">'+data.selected.length+'</span>';
							else
								return self.i18n.delete;
						},
						onClick: function( data ) {
							if( data.selected.length > 0 )
								self.showDeleteDialog( data.selected );
							else
								self.showDeleteDialog( data.clicked );
						},
						iconClass: "icon icon-trash",
						isShown: function( data ) { return !!( self.config.delete && data.clicked.name != ".." ); }
					}
				}
			});
		}
	};

	/**
	 * Changes the current directory
	 *
	 * @param string newdir - target directory
	 * @param object options - options for changing the directory
	 */
	this.changeDirectory = function( newdir, options ) {
		options = options || {};
		config = { absolute: false, pushState: true };
		jQuery.extend( config, options );
		if( ! config.absolute ) newdir = self.pathCombine( self.currentDir, newdir );
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "getRealpath",
				dir: newdir
			}),
			dataType: "json",
			success: function( data ) {
				self.currentDir = data.realpath;
				self.refreshFileTable();
				$( "#currentDir" ).val( self.currentDir );
				if( config.pushState ) history.pushState( { dir: self.currentDir }, self.currentDir, "#"+encodeURIComponent( self.currentDir ) );
			},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); }
		});
	};

	/**
	 * Shows a file, either a new file or an existing
	 */
	this.showFileDialog = function () {
		var filename = arguments.length > 0 ? arguments[0] : "newfile.txt";
		var content = arguments.length > 1 ? arguments[1] : "";
		self.showModal( Mustache.render( self.templates.file, { filename: filename, i18n: self.i18n } ), { large: true } );

		var form = document.getElementById( 'formFile' );
		form.addEventListener( 'keypress', function( e ) {
			if( e.target.name == 'filename' && e.key == 'Enter' )
				e.preventDefault();
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == "buttonSave" ) {
				e.preventDefault();
				self.saveFile( document.querySelector( '#formFile input[name=filename]' ).value, self.editor.getValue() );
				self.isModalClosedByButton = true;
				self.hideModal();
			} else if( e.target.id == "buttonSaveNotClose" ) {
				e.preventDefault();
				self.saveFile( document.querySelector( '#formFile input[name=filename]' ).value, self.editor.getValue() );
			} else if( e.target.id == "buttonClose" ) {
				e.preventDefault();
				self.isModalClosedByButton = true;
				self.hideModal();
			}
		});

		$('#editoroptions').popover({
			html: true,
			title: self.i18n.options,
			content: function() {
				// see https://github.com/twbs/bootstrap/issues/12571
				// var ihatethisfuckingpopoverworkaround = $('#editoroptions').data('bs.popover');
				// $(ihatethisfuckingpopoverworkaround.tip).find( '.popover-body' ).empty();

				var aceSession = self.editor.getSession();
				var content = self.getNodeFromString(
					Mustache.render(
						self.templates.file_editoroptions,
						{
							wordwrap: ( aceSession.getOption( 'wrap' ) == 'off' ? false : true ),
							softtabs: aceSession.getOption( 'useSoftTabs' ),
							tabsize: aceSession.getOption( 'tabSize' ),
							ace_includes: self.ace,
							ace_mode_selected: function() {
								return ( aceSession.$modeId == "ace/mode/"+this ) ? 'selected="selected"' : '';
							},
							i18n: self.i18n
						}
					)
				);
				if( el = content.querySelector("#editor-wordwrap" )) {
					el.addEventListener( 'change', function( e ) {
						aceSession.setOption( 'wrap', e.srcElement.checked );
					});
				}
				if( el = content.querySelector("#editor-softtabs" ))
					el.addEventListener( 'change', function( e ) {
						aceSession.setOption( 'useSoftTabs', e.srcElement.checked );
					});
				if( el = content.querySelector("#editor-tabsize" )) {
					el.addEventListener( 'keydown', function( e ) {
						if( e.key == 'Enter' ) {
							e.preventDefault();
							aceSession.setOption( 'tabSize', e.srcElement.value );
						}
					});
				}
				if( el = content.querySelector("#editor-syntax" ))
					el.addEventListener( 'change', function( e ) {
						aceSession.getSession().setMode( e.target.value );
					});
				return content;

			}
		});

		// Start ACE
		self.editor = ace.edit("content");
		self.editor.$blockScrolling = 'Infinity';
		self.editor.getSession().setValue(content);
		self.editor.focus();
		self.editor.on("change", function() { self.fileChanged = true; });
		if( self.ace && self.inArray( "ext-modelist", self.ace.files ) ) {
			var mode = ace.require( "ace/ext/modelist" ).getModeForPath( filename ).mode;
			if( self.inArray( mode, self.ace.modes.map( x => "ace/mode/"+x ) ) )
				self.editor.getSession().setMode( mode );
		}
		self.editor.commands.addCommand({
			name: "toggleFullscreen",
			bindKey: "Ctrl-Shift-F",
			exec: function(e) {
				var el = e.container;
				if (el.parentElement.tagName == "BODY") {
					el.remove();
					var fieldset = document.getElementsByClassName('modal-body')[0].firstElementChild;
					fieldset.insertBefore(el, fieldset.getElementsByTagName('button')[0].previousElementSibling);
					el.style = Object.assign({}, ifm.tmpEditorStyles);
					ifm.tmpEditorStyles = undefined;
				} else {
					ifm.tmpEditorStyles = Object.assign({}, el.style);
					el.remove();
					document.body.appendChild(el);
					el.style.position = "absolute";
					el.style.top = 0;
					el.style.left = 0;
					el.style.zIndex = 10000;
					el.style.width = "100%";
					el.style.height = "100%";
				}
				e.resize();
				e.focus();
			}
		});
	};

	/**
	 * Saves a file
	 */
	this.saveFile = function( filename, content ) {
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "saveFile",
				dir: self.currentDir,
				filename: filename,
				content: content
			}),
			dataType: "json",
			success: function( data ) {
						if( data.status == "OK" ) {
							self.showMessage( self.i18n.file_save_success, "s" );
							self.refreshFileTable();
						} else self.showMessage( self.i18n.file_save_error + data.message, "e" );
					},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); }
		});
		self.fileChanged = false;
	};

	/**
	 * Edit a file
	 *
	 * @params string name - name of the file
	 */
	this.editFile = function( filename ) {
		$.ajax({
			url: self.api,
			type: "POST",
			dataType: "json",
			data: ({
				api: "getContent",
				dir: self.currentDir,
				filename: filename
			}),
			success: function( data ) {
						if( data.status == "OK" && data.data.content != null ) {
							self.showFileDialog( data.data.filename, data.data.content );
						}
						else if( data.status == "OK" && data.data.content == null ) {
							self.showMessage( self.i18n.file_load_error, "e" );
						}
						else self.showMessage( self.i18n.error +data.message, "e" );
					},
			error: function() { self.showMessage( self.i18n.file_display_error, "e" ); }
		});
	};

	/**
	 * Shows the create directory dialog
	 */
	this.showCreateDirDialog = function() {
		self.showModal( Mustache.render( self.templates.createdir, { i18n: self.i18n } ) );
		var form = document.forms.formCreateDir;
		form.elements.dirname.addEventListener( 'keypress', function( e ) {
			if(e.key == 'Enter' ) {
				e.preventDefault();
				self.createDir( e.target.value );
				self.hideModal();
			}
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonSave' ) {
				e.preventDefault();
				self.createDir( form.elements.dirname.value );
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		}, false );
	};

	/**
	 * Create a directory
	 */
	this.createDir = function( dirname ) {
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "createDir",
				dir: self.currentDir,
				dirname: dirname
			}),
			dataType: "json",
			success: function( data ){
					if( data.status == "OK" ) {
						self.showMessage( self.i18n.folder_create_success, "s" );
						self.refreshFileTable();
					}
					else {
						self.showMessage( self.i18n.folder_create_error +data.message, "e" );
					}
				},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); }
		});
	};

	/**
	 * Shows the delete dialog
	 */
	this.showDeleteDialog = function( items ) {
		self.showModal(	Mustache.render( self.templates.deletefile, {
			multiple: ( items.length > 1 ),
			count: items.length,
			filename: ( Array.isArray( items ) ? items[0].name : items.name ),
			i18n: self.i18n
		}));
		var form = document.forms.formDeleteFiles;
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonYes' ) {
				e.preventDefault();
				self.deleteFiles( items );
				self.hideModal();
			} else if( e.target.id == 'buttonNo' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Deletes files
	 *
	 * @params {array} items - array with objects from the fileCache
	 */
	this.deleteFiles = function( items ) {
		if( ! Array.isArray( items ) )
			items = [items];
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "delete",
				dir: self.currentDir,
				filenames: items.map( function( e ){ return e.name; } )
			}),
			dataType: "json",
			success: function( data ) {
						if( data.status == "OK" ) {
							self.showMessage( self.i18n.file_delete_success, "s" );
							self.refreshFileTable();
						} else self.showMessage( self.i18n.file_delete_error, "e" );
					},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); }
		});
	};

	/**
	 * Show the rename file dialog
	 *
	 * @params string name - name of the file
	 */
	this.showRenameFileDialog = function( filename ) {
		self.showModal( Mustache.render( self.templates.renamefile, { filename: filename, i18n: self.i18n } ) );
		var form = document.forms.formRenameFile;
		form.elements.newname.addEventListener( 'keypress', function( e ) {
			if( e.key == 'Enter' ) {
				e.preventDefault();
				self.renameFile( filename, e.target.value );
				self.hideModal();
			}
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonRename' ) {
				e.preventDefault();
				self.renameFile( filename, form.elements.newname.value );
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Renames a file
	 *
	 * @params string name - name of the file
	 */
	this.renameFile = function( filename, newname ) {
		$.ajax({
			url: ifm.api,
			type: "POST",
			data: ({
				api: "rename",
				dir: ifm.currentDir,
				filename: filename,
				newname: newname
			}),
			dataType: "json",
			success: function(data) {
						if(data.status == "OK") {
							ifm.showMessage( self.i18n.file_rename_success, "s");
							ifm.refreshFileTable();
						} else ifm.showMessage( self.i18n.file_rename_error +data.message, "e");
					},
			error: function() { ifm.showMessage( self.i18n.general_error, "e"); }
		});
	};

	/**
	 * Show the copy/move dialog
	 *
	 * @params string name - name of the file
	 */
	this.showCopyMoveDialog = function( items ) {
		self.showModal( Mustache.render( self.templates.copymove, { i18n: self.i18n } ) );
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "getFolders"
			}),
			dataType: "json",
			success: function( data ) {
				$( '#copyMoveTree' ).treeview({
					data: data,
					levels: 1,
					expandIcon: "icon icon-folder-empty",
					emptyIcon: "icon icon-folder-empty",
					collapseIcon: "icon icon-folder-open-empty",
					loadingIcon: "icon icon-spin5",
					lazyLoad: function( n, cb ) {
						$.ajax({
							url: self.api,
							type: "POST",
							data: {
								api: "getFolders",
								dir: n.dataAttr.path
							},
							dataType: "json",
							success: cb
						});
					}
				});
			},
			error: function() { self.hideModal(); self.showMessage( self.i18n.folder_tree_load_error, "e" ) }
		});
		var form = document.forms.formCopyMove;
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'copyButton' ) {
				e.preventDefault();
				self.copyMove( items, form.getElementsByClassName( 'node-selected' )[0].dataset.path, 'copy' );
				self.hideModal();
			} else if( e.target.id == 'moveButton' ) {
				e.preventDefault();
				self.copyMove( items, form.getElementsByClassName( 'node-selected' )[0].dataset.path, 'move' );
				self.hideModal();
			} else if( e.target.id == 'cancelButton' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Copy or moves a file
	 * 
	 * @params {string} sources - array of fileCache items
	 * @params {string} destination - target directory
	 * @params {string} action - action (copy|move)
	 */
	this.copyMove = function( sources, destination, action ) {
		if( ! Array.isArray( sources ) )
			sources = [sources];
		var id = self.generateGuid();
		self.task_add( { id: id, name: self.i18n[action] + " " + ( sources.length > 1 ? sources.length : sources[0].name ) + " " + self.i18n.file_copy_to + " " + destination } );
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				dir: self.currentDir,
				api: "copyMove",
				action: action,
				filenames: sources.map( function( e ) { return e.name } ),
				destination: destination
			},
			dataType: "json",
			success: function( data ) {
				if( data.status == "OK" ) {
					self.showMessage( data.message, "s" );
				} else {
					self.showMessage( data.message, "e" );
				}
				self.refreshFileTable();
			},
			error: function() {
				self.showMessage( self.i18n.general_error, "e" );
			},
			complete: function() {
				self.task_done( id );
			}
		});
	};

	/**
	 * Shows the extract file dialog
	 *
	 * @param {string} filename - name of the file
	 */
	this.showExtractFileDialog = function( filename ) {
		var targetDirSuggestion = ( filename.lastIndexOf( '.' ) > 1 ) ? filename.substr( 0, filename.lastIndexOf( '.' ) ) : filename;
		self.showModal( Mustache.render( self.templates.extractfile, { filename: filename, destination: targetDirSuggestion, i18n: self.i18n } ) );
		var form = document.forms.formExtractFile;
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonExtract' ) {
				e.preventDefault();
				var loc = form.elements.extractTargetLocation.value;
				self.extractFile( filename, ( loc == "custom" ? form.elements.extractCustomLocation.value : loc ) ); 
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
		form.elements.extractCustomLocation.addEventListener( 'keypress', function( e ) {
			var loc = form.elements.extractTargetLocation.value;
			if( e.key == 'Enter' ) {
				e.preventDefault();
				self.extractFile( filename, ( loc == "custom" ? form.elements.extractCustomLocation.value : loc ) );
				self.hideModal();
			}
		});
		form.elements.extractCustomLocation.addEventListener( 'focus', function( e ) {
			form.elements.extractTargetLocation.value = 'custom';
		});
	};

	/**
	 * Extracts a file
	 *
	 * @param string filename - name of the file
	 * @param string destination - name of the target directory
	 */
	this.extractFile = function( filename, destination ) {
		var id = self.generateGuid();
		self.task_add( { id: id, name: "extract "+filename } );
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "extract",
				dir: self.currentDir,
				filename: filename,
				targetdir: destination
			},
			dataType: "json",
			success: function( data ) {
						if( data.status == "OK" ) {
							self.showMessage( data.message, "s" );
							self.refreshFileTable();
						} else self.showMessage( data.message, "e" );
					},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); },
			complete: function() { self.task_done( id ); }
		});
	};

	/**
	 * Shows the upload file dialog
	 */
	this.showUploadFileDialog = function() {
		self.showModal( Mustache.render( self.templates.uploadfile, { i18n: self.i18n } ) );
		var form = document.forms.formUploadFile;
		form.elements.files.addEventListener( 'change', function( e ) {
			if( e.target.files.length > 1 )
				form.elements.newfilename.readOnly = true;
			else 
				form.elements.newfilename.readOnly = false;
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonUpload' ) {
				e.preventDefault();
				var newfilename = form.elements.newfilename.value;
				var files = Array.prototype.slice.call( form.elements.files.files );
				var existing_files;
				if (files.length > 1)
					existing_files = files.map(x => x.name).filter(item => self.fileCache.map(x => x.name).includes(item));
				else if (newfilename)
					existing_files = self.fileCache.map(x => x.name).indexOf(newfilename) !== -1 ? [newfilename] : [];
				else 
					existing_files = self.fileCache.map(x => x.name).indexOf(files[0].name) !== -1 ? [files[0].name] : [];
				if (existing_files.length > 0 && self.config.confirmoverwrite)
					self.showUploadConfirmOverwrite(files, existing_files, newfilename);
				else {
					if (files.length == 1)
						self.uploadFile(files[0], newfilename);
					else
						files.forEach( function( file ) {
							self.uploadFile( file );
						});
				}
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	this.showUploadConfirmOverwrite = function(files, existing_files, newfilename=undefined) {
		self.showModal(Mustache.render(self.templates.uploadconfirmoverwrite, {files: existing_files, i18n: self.i18n}));
		var form = document.forms.formUploadConfirmOverwrite;
		form.addEventListener('click', function(e) {
			if (e.target.id == "buttonConfirm") {
				e.preventDefault();
				if (files.length == 1 && newfilename)
					self.uploadFile(files[0], newfilename);
				else
					files.forEach(function(file) {
						self.uploadFile(file);
					});
				self.hideModal();
			} else if (e.target.id == 'buttonCancel') {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Uploads a file
	 */
	this.uploadFile = function( file, newfilename ) {
		var data = new FormData();
 		data.append( 'api', 'upload' );
 		data.append( 'dir', self.currentDir );
 		data.append( 'file', file );
		if( newfilename )
			data.append( 'newfilename', newfilename );
		var id = self.generateGuid();
		$.ajax({
			url: self.api,
			type: "POST",
			data: data,
			processData: false,
			contentType: false,
			dataType: "json",
			xhr: function(){
				var xhr = $.ajaxSettings.xhr() ;
				xhr.upload.onprogress = function(evt){ self.task_update(evt.loaded/evt.total*100,id); } ;
				xhr.upload.onload = function(){ self.log('Uploading '+file.name+' done.') } ;
				return xhr ;
			},
			success: function(data) {
				if(data.status == "OK") {
					self.showMessage( self.i18n.file_upload_success, "s");
					if(data.cd == self.currentDir) self.refreshFileTable();
				} else self.showMessage( data.message, "e");
			},
			error: function() { self.showMessage( self.i18n.general_error, "e"); },
			complete: function() { self.task_done(id); }
		});
		self.task_add( { id: id, name: "Upload " + file.name } );
	};

	/**
	 * Change the permissions of a file
	 *
	 * @params object e - event object
	 * @params string name - name of the file
	 */
	this.changePermissions = function( filename, newperms ) {
		$.ajax({
			url: self.api,
			type: "POST",
			data: ({
				api: "changePermissions",
				dir: self.currentDir,
				filename: filename,
				chmod: newperms
			}),
			dataType: "json",
			success: function( data ){
				if( data.status == "OK" ) {
					self.showMessage( self.i18n.permission_change_success, "s" );
					self.refreshFileTable();
				}
				else {
					self.showMessage( data.message, "e");
				}
			},
			error: function() { self.showMessage( self.i18n.general_error, "e"); }
		});
	};

	/**
	 * Show the remote upload dialog
	 */
	this.showRemoteUploadDialog = function() {
		self.showModal( Mustache.render( self.templates.remoteupload, { i18n: self.i18n } ) );
		var form = document.forms.formRemoteUpload;
		var urlChangeHandler = function( e ) {
			form.elements.filename.value = e.target.value.substr( e.target.value.lastIndexOf( '/' ) + 1 );
		};
		form.elements.url.addEventListener( 'keypress', self.preventEnter );
		form.elements.url.addEventListener( 'change', urlChangeHandler );
		form.elements.url.addEventListener( 'keyup', urlChangeHandler );
		form.elements.filename.addEventListener( 'keypress', self.preventEnter );
		form.elements.filename.addEventListener( 'keyup', function( e ) {
			form.elements.url.removeEventListener( 'change', urlChangeHandler );
			form.elements.url.removeEventListener( 'keyup', urlChangeHandler );
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonUpload' ) {
				e.preventDefault();
				self.remoteUpload( form.elements.url.value, form.elements.filename.value, form.elements.method.value );
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Remote uploads a file
	 */
	this.remoteUpload = function( url, filename, method ) {
		var id = ifm.generateGuid();
		$.ajax({
			url: ifm.api,
			type: "POST",
			data: ({
				api: "remoteUpload",
				dir: ifm.currentDir,
				filename: filename,
				method: method,
				url: url
			}),
			dataType: "json",
			success: function(data) {
				if(data.status == "OK") {
					self.showMessage( self.i18n.file_upload_success, "s" );
					self.refreshFileTable();
				} else
					self.showMessage( self.i18n.file_upload_error + data.message, "e" );
			},
			error: function() { self.showMessage( self.i18n.general_error, "e"); },
			complete: function() { self.task_done(id); }
		});
		self.task_add( { id: id, name: self.i18n.upload_remote+" "+filename } );
	};

	/**
	 * Shows the ajax request dialog
	 */
	this.showAjaxRequestDialog = function() {
		self.showModal( Mustache.render( self.templates.ajaxrequest, { i18n: self.i18n } ) );
		var form = document.forms.formAjaxRequest;
		form.elements.ajaxurl.addEventListener( 'keypress', self.preventEnter );
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonRequest' ) {
				e.preventDefault();
				self.ajaxRequest( form.elements.ajaxurl.value, form.elements.ajaxdata.value.replace( /\n/g, '&' ), form.elements.arMethod.value );
			} else if( e.target.id == 'buttonClose' ) {
				e.preventDefault();
				self.hideModal();
			}
		});
	};

	/**
	 * Performs an ajax request
	 */
	this.ajaxRequest = function( url, data, method ) {
		$.ajax({
			url	: url,
			cache	: false,
			data	: data,
			type    : method,
			success	: function( response ) { document.getElementById( 'ajaxresponse' ).innerText = response; },
			error	: function(e) { self.showMessage("Error: "+e, "e"); self.log(e); }
		});
	};

	/**
	 * Shows the search dialog
	 */
	this.showSearchDialog = function() {
		self.showModal( Mustache.render( self.templates.search, { lastSearch: self.search.lastSearch, i18n: self.i18n } ) );

		var updateResults = function( data ) {
			self.log( 'updated search results' );
			var searchresults = document.getElementById( 'searchResults' );
			if( searchresults.tBodies[0] ) searchresults.tBodies[0].remove();
			searchresults.appendChild( document.createElement( 'tbody' ) );
			searchresults.tBodies[0].innerHTML = Mustache.render( self.templates.searchresults, { items: self.search.data } );
			searchresults.tBodies[0].addEventListener( 'click', function( e ) {
				if( e.target.classList.contains( 'searchitem' ) || e.target.parentElement.classList.contains( 'searchitem' ) ) {
					e.preventDefault();
					self.changeDirectory( self.pathCombine( self.search.data.currentDir, e.target.dataset.folder || e.target.parentElement.dataset.folder ), { absolute: true });
					self.hideModal();
				}
			});
			searchresults.tBodies[0].addEventListener( 'keypress', function( e ) {
				if( e.target.classList.contains( 'searchitem' ) || e.target.parentElement.classList.contains( 'searchitem' ) ) {
					e.preventDefault();
					e.target.click();
				}
			});
		};

		updateResults( self.search.data );

		document.getElementById( 'searchPattern' ).addEventListener( 'keypress', function( e ) {
			if( e.key == 'Enter' ) {
				e.preventDefault();
				if( e.target.value.trim() === '' ) return;
				document.getElementById( 'searchResults' ).tBodies[0].innerHTML = '<tr><td style="text-align:center;"><span class="icon icon-spin5 animate-spin"></span></td></tr>';
				self.search.lastSearch = e.target.value;
				$.ajax({
					url: self.api,
					type: "POST",
					data: {
						api: "searchItems",
						dir: self.currentDir,
						pattern: e.target.value
					},
					dataType: "json",
					success: function( data ) {
						if( data.status == 'ERROR' ) {
							self.hideModal();
							self.showMessage( data.message, "e" );
						} else {
							data.forEach( function(e) {
								e.folder = e.name.substr( 0, e.name.lastIndexOf( '/' ) );
								e.linkname = e.name.substr( e.name.lastIndexOf( '/' ) + 1 );
							});
							self.search.data = data;
							if( self.search.data ) self.search.data.currentDir = self.currentDir;
							updateResults( data );
						}
					}
				});
			}
		});
	};

	/**
	 * Shows the create archive dialog
	 */
	this.showCreateArchiveDialog = function( items ) {
		self.showModal( Mustache.render( self.templates.createarchive, { i18n: self.i18n } ) );

		var form = document.forms.formCreateArchive;
		form.elements.archivename.addEventListener( 'keypress', function( e ) {
			if( e.key == 'Enter' ) {
				e.preventDefault();
				self.createArchive( items, e.target.value );
				self.hideModal();
			}
		});
		form.addEventListener( 'click', function( e ) {
			if( e.target.id == 'buttonSave' ) {
				e.preventDefault();
				self.createArchive( items, form.elements.archivename.value );
				self.hideModal();
			} else if( e.target.id == 'buttonCancel' ) {
				e.preventDefault();
				self.hideModal();
			}
		}, false );
	};

	this.createArchive = function( items, archivename ) {
		var type = "";
		if( archivename.substr( -3 ).toLowerCase() == "zip" )
			type = "zip";
		else if( archivename.substr( -3 ).toLowerCase() == "tar" )
			type = "tar";
		else if( archivename.substr( -6 ).toLowerCase() == "tar.gz" )
			type = "tar.gz";
		else if( archivename.substr( -7 ).toLowerCase() == "tar.bz2" )
			type = "tar.bz2";
		else {
			self.showMessage( self.i18n.invalid_archive_format, "e" );
			return;
		}
		var id = self.generateGuid();
		self.task_add( { id: id, name: self.i18n.create_archive+" "+archivename } );

		if( ! Array.isArray( items ) )
			items = [items];

		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "createArchive",
				dir: self.currentDir,
				archivename: archivename,
				filenames: items.map( function( e ) { return e.name; } ),
				format: type
			},
			dataType: "json",
			success: function( data ) {
				self.log( data );
				if( data.status == "OK" ) {
					self.showMessage( data.message, "s" );
					self.refreshFileTable();
				} else
					self.showMessage( data.message, "e" );
			},
			error: function() { self.showMessage( self.i18n.general_error, "e" ); },
			complete: function() { self.task_done( id ); }
		});
	};

	// --------------------
	// helper functions
	// --------------------

	/**
	 * Shows a notification
	 *
	 * @param string m - message text
	 * @param string t - message type (e: error, s: success)
	 */
	this.showMessage = function(m, t) {
		var msgType = ( t == "e" ) ? "danger" : ( t == "s" ) ? "success" : "info";
		var element = ( self.config.inline ) ? self.rootElement : "body";
		$.notify(
			{ message: m },
			{ type: msgType, delay: 3000, mouse_over: 'pause', offset: { x: 15, y: 65 }, element: element }
		);
	};

	/**
	 * Combines two path components
	 *
	 * @param {string} a - component 1
	 * @param {string} b - component 2
	 * @returns {string} - combined path
	 */
	this.pathCombine = function() {
		if( !arguments.length )
			return "";
		var args = Array.prototype.slice.call(arguments);
		args = args.filter( x => typeof x === 'string' && x != '' );

		if( args.length == 0 )
			return "";

		first = "";
		while( first.length < 1 )
			first = args.shift();

		first = first.replace( /\/+$/g, '' );
		if( !args.length )
			return first;

		args.forEach( (v, i) => args[i] = v.replace( /^\/*|\/*$/g, '' ) ); // */
		args.unshift( first );
		return args.join( '/' );
	};

	/**
	 * Prevents a user to submit a form via clicking enter
	 *
	 * @param object e - click event
	 */
	this.preventEnter = function(e) {
		if( e.key == 'Enter' )
			e.preventDefault();
	};

	/**
	 * Checks if an element is part of an array
	 *
	 * @param {object} needle - search item
	 * @param {array} haystack - array to search
	 * @returns {boolean}
	 */
	this.inArray = function(needle, haystack) {
		for( var i = 0; i < haystack.length; i++ )
			if( haystack[i] == needle )
				return true;
		return false;
	};

	/**
	 * Formats a date from an unix timestamp
	 *
	 * @param {integer} timestamp - UNIX timestamp
	 */
	this.formatDate = function( timestamp ) {
		var d = new Date( timestamp * 1000 );

		return d.toLocaleString(self.config.dateLocale);
	};

	this.getClipboardLink = function( relpath ) {
		var link = window.location.origin;
		link += window.location.pathname.substr( 0, window.location.pathname.lastIndexOf( "/" ) );
		link = self.pathCombine( link, relpath );
		return link;
	}

	this.getNodeFromString = function( s ) {
		var template = document.createElement( 'template');
		template.innerHTML = s;
		return template.content.childNodes[0];
	};

	this.getNodesFromString = function( s ) {
		var template = document.createElement( 'template');
		template.innerHTML = s;
		return template.content.childNodes;
	};

	// copypasted from https://hackernoon.com/copying-text-to-clipboard-with-javascript-df4d4988697f
	this.copyToClipboard = function( str ) {
		const el = document.createElement('textarea');
		el.value = str;
		el.setAttribute('readonly', '');
		el.style.position = 'absolute';
		el.style.left = '-9999px';
		document.body.appendChild(el);
		const selected =
			document.getSelection().rangeCount > 0
			? document.getSelection().getRangeAt(0)
			: false;
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		if (selected) {
			document.getSelection().removeAllRanges();
			document.getSelection().addRange(selected);
		}
	};

	/**
	 * Adds a task to the taskbar.
	 *
	 * @param object task - description of the task: { id: "guid", name: "Task Name", type: "(info|warning|danger|success)" }
	 */
	this.task_add = function( task ) {
		if( ! task.id ) {
			self.log( "Error: No task id given.");
			return false;
		}
		if( ! document.querySelector( "footer" ) ) {
			var newFooter = self.getNodeFromString( Mustache.render( self.templates.footer, { i18n: self.i18n } ) );
			newFooter.addEventListener( 'click', function( e ) {
				if( e.target.name == 'showAll' || e.target.parentElement.name == "showAll" ) {
					wq = newFooter.children.wq_container.children[0].children.waitqueue;
					if( wq.style.maxHeight == '70vh' ) {
						wq.style.maxHeight = '6rem';
						wq.style.overflow = 'hidden';
					} else {
						wq.style.maxHeight = '70vh';
						wq.style.overflow = 'auto';
					}
				}
			});
			document.body.appendChild( newFooter );
			document.body.style.paddingBottom = '9rem';
		}
		task.id = "wq-"+task.id;
		task.type = task.type || "info";
		var wq = document.getElementById( 'waitqueue' );
		wq.prepend( self.getNodeFromString( Mustache.render( self.templates.task, task ) ) );
		document.getElementsByName( 'taskCount' )[0].innerText = wq.children.length;
	};

	/**
	 * Removes a task from the taskbar
	 *
	 * @param string id - task identifier
	 */
	this.task_done = function( id ) {
		document.getElementById( 'wq-' + id ).remove();
		var wq = document.getElementById( 'waitqueue' );
		if( wq.children.length == 0 ) {
			document.getElementsByTagName( 'footer' )[0].remove();
			document.body.style.paddingBottom = 0;
		} else {
			document.getElementsByName( 'taskCount' )[0].innerText = wq.children.length;
		}
	};

	/**
	 * Updates a task
	 *
	 * @param integer progress - percentage of status
	 * @param string id - task identifier
	 */
	this.task_update = function( progress, id ) {
		var progbar = document.getElementById( 'wq-'+id ).getElementsByClassName( 'progress-bar' )[0];
		progbar.style.width = progress+'%';
		progbar.setAttribute( 'aria-valuenow', progress );
	};

	/**
	 * Highlights an item in the file table
	 *
	 * @param object param - either an element id or a jQuery object
	 */
	this.highlightItem = function( direction ) {
		var highlight = function( el ) {
			[].slice.call( el.parentElement.children ).forEach( function( e ) {
				e.classList.remove( 'highlightedItem' );
			});
			el.classList.add( 'highlightedItem' );
			el.firstElementChild.firstElementChild.focus();
			if( ! self.isElementInViewport( el ) ) {
				var scrollOffset =  0;
				if( direction=="prev" )
					scrollOffset = el.offset().top - ( window.innerHeight || document.documentElement.clientHeight ) + el.height() + 15;
				else
					scrollOffset = el.offset().top - 55;
				$('html, body').animate( { scrollTop: scrollOffset }, 200 );
			}
		};


		var highlightedItem = document.getElementsByClassName( 'highlightedItem' )[0];
		if( ! highlightedItem ) {
			if( document.activeElement.classList.contains( 'ifmitem' ) )
				highlight( document.activeElement.parentElement.parentElement );
			else 
				highlight( document.getElementById( 'filetable' ).tBodies[0].firstElementChild );
		} else  {
			var newItem = ( direction=="next" ? highlightedItem.nextElementSibling : highlightedItem.previousElementSibling );
			if( newItem != null )
				highlight( newItem );
		}
	};

	/**
	 * Checks if an element is within the viewport
	 *
	 * @param object el - element object
	 */
	this.isElementInViewport = function( el ) {
		var rect = el.getBoundingClientRect();
		return (
				rect.top >= 80 &&
				rect.left >= 0 &&
				rect.bottom <= ( ( window.innerHeight || document.documentElement.clientHeight ) ) &&
				rect.right <= ( window.innerWidth || document.documentElement.clientWidth )
			   );
	};

	/**
	 * Generates a GUID
	 */
	this.generateGuid = function() {
		var result, i, j;
		result = '';
		for( j = 0; j < 20; j++ ) {
			i = Math.floor( Math.random() * 16 ).toString( 16 ).toUpperCase();
			result = result + i;
		}
		return result;
	};

	/**
	 * Logs a message if debug mode is active
	 *
	 * @param string m - message text
	 */
	this.log = function( m ) {
		if( self.config.debug ) {
			console.log( "IFM (debug): " + m );
		}
	};

	/**
	 * Encodes a string for use in the href attribute of an anchor.
	 *
	 * @param string s - decoded string
	 */
	this.hrefEncode = function( s ) {
		return s
			.replace( /%/g, '%25' )
			.replace( /;/g, '%3B' )
			.replace( /\?/g, '%3F' )
			.replace( /:/g, '%3A' )
			.replace( /@/g, '%40' )
			.replace( /&/g, '%26' )
			.replace( /=/g, '%3D' )
			.replace( /\+/g, '%2B' )
			.replace( /\$/g, '%24' )
			.replace( /,/g, '%2C' )
			.replace( /</g, '%3C' )
			.replace( />/g, '%3E' )
			.replace( /#/g, '%23' )
			.replace( /"/g, '%22' )
			.replace( /{/g, '%7B' )
			.replace( /}/g, '%7D' )
			.replace( /\|/g, '%7C' )
			.replace( /\^/g, '%5E' )
			.replace( /\[/g, '%5B' )
			.replace( /\]/g, '%5D' )
			.replace( /\\/g, '%5C' )
			.replace( /`/g, '%60' )
		;
		// ` <- this comment prevents the vim syntax highlighting from breaking -.-
	};

	/**
	 * Handles the javascript onbeforeunload event
	 *
	 * @param object event - event object
	 */
	this.onbeforeunloadHandler = function( e ) {
		if( document.getElementById( 'waitqueue' ) ) {
			return self.i18n.remaining_tasks;
		}
	};

	/**
	 * Handles the javascript pop states
	 *
	 * @param object event - event object
	 */
	this.historyPopstateHandler = function( e ) {
		var dir = "";
		if( e.state && e.state.dir )
			dir = e.state.dir;
		self.changeDirectory( dir, { pushState: false, absolute: true } );
	};

	/**
	 * Handles keystrokes
	 *
	 * @param object e - event object
	 */
	this.handleKeystrokes = function( e ) {
		var isFormElement = function( el ) {
			do {
				if( self.inArray( el.tagName, ['INPUT', 'TEXTAREA'] ) ) {
					return true;
				}
			} while( ( el == el.parentElement ) !== false );
			return false;
		}

		if( isFormElement( e.target ) ) return;

		// global key events
		switch( e.key ) {
			case '/':
				if (self.config.search) {
					e.preventDefault();
					self.showSearchDialog();
				}
				break;
			case 'g':
				e.preventDefault();
				$('#currentDir').focus();
				return;
				break;
			case 'r':
				if (self.config.showrefresh) {
					e.preventDefault();
					self.refreshFileTable();
				}
				break;
			case 'u':
				if( self.config.upload ) {
					e.preventDefault();
					self.showUploadFileDialog();
				}
				return;
				break;
			case 'o':
				if( self.config.remoteupload ) {
					e.preventDefault();
					self.showRemoteUploadDialog();
				}
				return;
				break;
			case 'a':
				if( self.config.ajaxrequest ) {
					e.preventDefault();
					self.showAjaxRequestDialog();
				}
				return;
				break;
			case 'F':
				if( self.config.createfile ) {
					e.preventDefault();
					self.showFileDialog();
				}
				return;
				break;
			case 'D':
				if( self.config.createdir ) {
					e.preventDefault();
					self.showCreateDirDialog();
				}
				return;
				break;
			case 'h':
			case 'ArrowLeft':
			case 'Backspace':
				e.preventDefault();
				self.changeDirectory( '..' );
				return;
				break;
			case 'j':
			case 'ArrowDown':
				e.preventDefault();
				self.highlightItem('next');
				return;
				break;
			case 'k':
			case 'ArrowUp':
				e.preventDefault();
				self.highlightItem('prev');
				return;
				break;
		}

		// key events which need a highlighted item
		var element = document.getElementsByClassName( 'highlightedItem' )[0];
		if( element )
			item = self.fileCache.find( x => x.guid == element.children[0].children[0].id );
		else
			item = false;

		// Some operations do not work if the highlighted item is the parent
		// directory. In these cases the keybindings are ignored.
		var selectedItems = Array.prototype.slice.call( document.getElementsByClassName( 'selectedItem' ) )
			.map( function( e ) { return self.fileCache.find( x => x.guid === e.children[0].children[0].id ) } );

		switch( e.key ) {
			case 'Delete':
				if( self.config.delete )
					if( selectedItems.length > 0 ) {
						e.preventDefault();
						self.showDeleteDialog( selectedItems );
					} else if( item && item.name !== '..' )
						self.showDeleteDialog( item );
				return;
				break;
			case 'c':
			case 'm':
				if( self.config.copymove ) {
					if( selectedItems.length > 0 ) {
						e.preventDefault();
						self.showCopyMoveDialog( selectedItems );
					} else if( item && item.name !== '..' )
						self.showCopyMoveDialog( item );
				}
				return;
				break;
		}

		if( item )
			switch( e.key ) {
				case 'l':
				case 'ArrowRight':
					e.preventDefault();
					if( item.type == "dir" )
						self.changeDirectory( item.name );
					return;
					break;
				case 'Escape':
					if( element.children[0].children[0] == document.activeElement ) {
						e.preventDefault();
						element.classList.toggle( 'highlightedItem' );
					}
					return;
					break;
				case ' ':
				case 'Enter':
					if( element.children[0].children[0] == document.activeElement ) { 
						if( e.key == 'Enter' && element.classList.contains( 'isDir' ) ) {
							e.preventDefault();
							e.stopPropagation();
							self.changeDirectory( item.name );
						} else if( e.key == ' ' && item.name != ".." ) {
							e.preventDefault();
							e.stopPropagation();
							element.classList.toggle( 'selectedItem' );
						}
					}
					return;
					break;
				case 'e':
					if( self.config.edit && item.eaction == "edit" ) {
						e.preventDefault();
						self.editFile( item.name );
					} else if( self.config.extract && item.eaction == "extract" ) {
						e.preventDefault();
						self.showExtractFileDialog( item.name );
					}
					return;
					break;
				case 'n':
					e.preventDefault();
					if( self.config.rename ) {
						self.showRenameFileDialog( item.name );
					}
					return;
					break;
			}
	};

	/**
	 * Initializes the application
	 */
	this.initLoadConfig = function() {
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "getConfig"
			},
			dataType: "json",
			success: function(d) {
				self.config = d;
				if( self.config.ace_includes ) {
					self.ace = {};
					self.ace.files = self.config.ace_includes.split( '|' ).filter( x => x != "" );
					self.ace.modes = self.ace.files
						.filter( function(f){ if( f.substr(0,5)=="mode-" ) return f; } )
						.map( function(f){ return f.substr(5); } )
					self.ace.modes.unshift( "text" );
				}
				self.log( "configuration loaded" );
				self.initLoadTemplates();
			},
			error: function() {
				throw new Error( self.i18n.load_config_error );
			}
		});
	};
	
	this.initLoadTemplates = function() {
		// load the templates from the backend
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "getTemplates"
			},
			dataType: "json",
			success: function(d) {
				self.templates = d;
				self.log( "templates loaded" );
				self.initLoadI18N();
			},
			error: function() {
				throw new Error( self.i18n.load_template_error );
			}
		});
	};

	this.initLoadI18N = function() {
		// load I18N from the backend
		$.ajax({
			url: self.api,
			type: "POST",
			data: {
				api: "getI18N"
			},
			dataType: "json",
			success: function(d) {
				self.i18n = d;
				self.log( "I18N loaded" );
				self.initApplication();
			},
			error: function() {
				throw new Error( self.i18n.load_text_error );
			}
		});
	};
	
	this.initApplication = function() {
		self.rootElement.innerHTML = Mustache.render(
				self.templates.app,
				{
					showpath: "/",
					config: self.config,
					i18n: self.i18n,
					generic: {
						hasdropdown: (!!self.config.ajaxrequest||!!self.config.remoteupload||!!self.config.auth)
					},
					ftbuttons: function(){
						return ( self.config.edit || self.config.rename || self.config.delete || self.config.zipnload || self.config.extract );
					}
				});

		// bind static buttons
		if (el_r = document.getElementById('refresh'))
			el_r.onclick = function() { self.refreshFileTable(); };
		if (el_s = document.getElementById('search'))
			el_s.onclick = function() { self.showSearchDialog(); };
		//document.getElementById( 'refresh' ).onclick = function() { self.refreshFileTable(); };
		//document.getElementById( 'search' ).onclick = function() { self.showSearchDialog(); };
		if( self.config.createfile )
			document.getElementById( 'createFile' ).onclick = function() { self.showFileDialog(); };
		if( self.config.createdir )
			document.getElementById( 'createDir' ).onclick = function() { self.showCreateDirDialog(); };
		if( self.config.upload )
			document.getElementById( 'upload' ).onclick = function() { self.showUploadFileDialog(); };
		document.getElementById( 'currentDir' ).onkeypress = function( e ) {
			if( e.keyCode == 13 ) {
				e.preventDefault();
				self.changeDirectory( e.target.value, { absolute: true } );
				e.target.blur();
			}
		};
		if( self.config.remoteupload )
			document.getElementById( 'buttonRemoteUpload' ).onclick = function() { self.showRemoteUploadDialog(); };
		if( self.config.ajaxrequest )
			document.getElementById( 'buttonAjaxRequest' ).onclick = function() { self.showAjaxRequestDialog(); };
		if( self.config.upload )
			document.addEventListener( 'dragover', function( e ) {
				if( Array.prototype.indexOf.call(e.dataTransfer.types, "Files") != -1 ) {
					e.preventDefault();
					e.stopPropagation();
					var div = document.getElementById( 'filedropoverlay' );
					div.style.display = 'block';
					div.ondrop = function( e ) {
						e.preventDefault();
						e.stopPropagation();
						var files = Array.from(e.dataTransfer.files);
						var existing_files = files.map(x => x.name).filter(item => self.fileCache.map(x => x.name).includes(item));
						if (existing_files.length > 0 && self.config.confirmoverwrite)
							self.showUploadConfirmOverwrite(files, existing_files);
						else 
							files.forEach(function(file) {
								self.uploadFile(file);
							});
						if( e.target.id == 'filedropoverlay' )
							e.target.style.display = 'none';
						else if( e.target.parentElement.id == 'filedropoverlay' ) {
							e.target.parentElement.style.display = 'none';
						}
					};
					div.ondragleave = function( e ) {
						e.preventDefault();
						e.stopPropagation();
						if( e.target.id == 'filedropoverlay' )
							e.target.style.display = 'none';
					};
				} else {
					var div = document.getElementById( 'filedropoverlay' );
					if( div.style.display == 'block' )
						div.stye.display == 'none';
				}
			});

		// drag and drop of filetable items
		if( self.config.copymove ) {
			var isFile = function(e) { return Array.prototype.indexOf.call(e.dataTransfer.types, "Files") != -1 };
			document.addEventListener( 'dragstart', function( e ) {
				var selectedItems = document.getElementsByClassName( 'selectedItem' );
				var data;
				if( selectedItems.length > 0 ) 
					data = self.fileCache.filter(
							x => self.inArray(
								x.guid,
								[].slice.call( selectedItems ).map( function( e ) { return e.dataset.id; } )
								)
							);
				else 
					data = self.fileCache.find( x => x.guid === e.target.dataset.id );
				e.dataTransfer.setData( 'text/plain', JSON.stringify( data ) );
				var dragImage = document.createElement( 'div' );
				dragImage.style.display = 'inline';
				dragImage.style.padding = '10px';
				dragImage.innerHTML = '<span class="icon icon-folder-open-empty"></span> '+self.i18n.move+' '+( data.length || data.name );
				document.body.appendChild( dragImage );
				setTimeout(function() {
					dragImage.remove();
				});
				e.dataTransfer.setDragImage( dragImage, 0, 0 );
			});
			document.addEventListener( 'dragover', function( e ) { if( ! isFile( e ) && e.target.parentElement.classList.contains( 'isDir' ) ) e.preventDefault(); } );
			document.addEventListener( 'dragenter', function( e ) {
				if( ! isFile( e ) && e.target.tagName == "TD" && e.target.parentElement.classList.contains( 'isDir' ) )
					e.target.parentElement.classList.add( 'highlightedItem' );
			});
			document.addEventListener( 'dragleave', function( e ) {
				if( ! isFile( e ) && e.target.tagName == "TD" && e.target.parentElement.classList.contains( 'isDir' ) )
					e.target.parentElement.classList.remove( 'highlightedItem' );
			});
			document.addEventListener( 'drop', function( e ) {
				if( ! isFile( e ) && e.target.tagName == "TD" && e.target.parentElement.classList.contains( 'isDir' ) ) {
					e.preventDefault();
					e.stopPropagation();
					try {
						var source = JSON.parse( e.dataTransfer.getData( 'text' ) );
						self.log( "source:" );
						self.log( source );
						var destination = self.fileCache.find( x => x.guid === e.target.firstElementChild.id );
						if( ! Array.isArray( source ) )
							source = [source];
						if( source.find( x => x.name === destination.name ) )
							self.showMessage( "Source and destination are equal." );
						else
							self.copyMove( source, destination.name, "move" );
					} catch( e ) {
						self.log( e );
					} finally {
						[].slice.call( document.getElementsByClassName( 'highlightedItem' ) ).forEach( function( e ) {
							e.classList.remove( 'highlightedItem' );
						});
					}
				}
			});
		}
		
		// handle keystrokes
		document.onkeydown = self.handleKeystrokes;

		// handle history manipulation
		window.onpopstate = self.historyPopstateHandler;

		// handle window.onbeforeunload
		window.onbeforeunload = self.onbeforeunloadHandler;

		// load initial file table
		if( window.location.hash ) {
			self.changeDirectory( decodeURIComponent( window.location.hash.substring( 1 ) ) );
		} else {
			this.refreshFileTable();
		}
	};

	this.init = function( id ) {
		self.rootElement = document.getElementById( id );
		this.initLoadConfig();
	};
}

        </script>
f00bar;
	}

	public function getHTMLHeader()
	{
		print '<!DOCTYPE HTML>
		<html>
			<head>
				<title>IFM - improved file manager</title>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, shrink-to-fit=no">';
		$this->getCSS();
		print '</head><body>';
	}

	public function getHTMLFooter()
	{
		print '</body></html>';
	}

	/*
	   main functions
	 */

	private function handleRequest()
	{
		if ($_REQUEST["api"] == "getRealpath") {
			if (isset($_REQUEST["dir"]) && $_REQUEST["dir"] != "")
				$this->jsonResponse(array("realpath" => $this->getValidDir($_REQUEST["dir"])));
			else
				$this->jsonResponse(array("realpath" => ""));
		} elseif ($_REQUEST["api"] == "getFiles") {
			if (isset($_REQUEST["dir"]) && $this->isPathValid($_REQUEST["dir"]))
				$this->getFiles($_REQUEST["dir"]);
			else
				$this->getFiles("");
		} elseif ($_REQUEST["api"] == "getConfig") {
			$this->getConfig();
		} elseif ($_REQUEST["api"] == "getFolders") {
			$this->getFolders($_REQUEST);
		} elseif ($_REQUEST["api"] == "getTemplates") {
			$this->jsonResponse($this->templates);
		} elseif ($_REQUEST["api"] == "getI18N") {
			$this->jsonResponse($this->l);
		} elseif ($_REQUEST["api"] == "logout") {
			unset($_SESSION['ifmauth']);
			session_destroy();
			header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
			exit(0);
		} else {
			if (isset($_REQUEST["dir"]) && $this->isPathValid($_REQUEST["dir"])) {
				$this->chDirIfNecessary($_REQUEST['dir']);
				switch ($_REQUEST["api"]) {
					case "createDir":
						$this->createDir($_REQUEST["dir"], $_REQUEST["dirname"]);
						break;
					case "saveFile":
						$this->saveFile($_REQUEST);
						break;
					case "getContent":
						$this->getContent($_REQUEST);
						break;
					case "delete":
						$this->deleteFiles($_REQUEST);
						break;
					case "rename":
						$this->renameFile($_REQUEST);
						break;
					case "download":
						$this->downloadFile($_REQUEST);
						break;
					case "extract":
						$this->extractFile($_REQUEST);
						break;
					case "upload":
						$this->uploadFile($_REQUEST);
						break;
					case "copyMove":
						$this->copyMove($_REQUEST);
						break;
					case "changePermissions":
						$this->changePermissions($_REQUEST);
						break;
					case "zipnload":
						$this->zipnload($_REQUEST);
						break;
					case "remoteUpload":
						$this->remoteUpload($_REQUEST);
						break;
					case "searchItems":
						$this->searchItems($_REQUEST);
						break;
					case "getFolderTree":
						$this->getFolderTree($_REQUEST);
						break;
					case "createArchive":
						$this->createArchive($_REQUEST);
						break;
					case "proxy":
						$this->downloadFile($_REQUEST, false);
						break;
					default:
						$this->jsonResponse(array("status" => "ERROR", "message" => "Invalid api action given"));
						break;
				}
			} else {
				print $this->jsonResponse(array("status" => "ERROR", "message" => "Invalid working directory"));
			}
		}
		exit(0);
	}

	public function run($mode = "standalone")
	{
		if ($this->checkAuth()) {
			// go to our root_dir
			if (!is_dir(realpath($this->config['root_dir'])) || !is_readable(realpath($this->config['root_dir'])))
				die("Cannot access root_dir.");
			else
				chdir(realpath($this->config['root_dir']));
			$this->mode = $mode;
			if (isset($_REQUEST['api']) || $mode == "api") {
				$this->handleRequest();
			} elseif ($mode == "standalone") {
				$this->getApplication();
			} else {
				$this->getInlineApplication();
			}
		}
	}

	/*
	   api functions
	 */


	private function getFiles($dir)
	{
		$this->chDirIfNecessary($dir);

		unset($files);
		unset($dirs);
		$files = array();
		$dirs = array();

		if ($handle = opendir(".")) {
			while (false !== ($result = readdir($handle))) {
				if ($result == basename($_SERVER['SCRIPT_NAME']) && $this->getScriptRoot() == getcwd()) {
				} elseif (($result == ".htaccess" || $result == ".htpasswd") && $this->config['showhtdocs'] != 1) {
				} elseif ($result == ".") {
				} elseif ($result != ".." && substr($result, 0, 1) == "." && $this->config['showhiddenfiles'] != 1) {
				} else {
					$item = $this->getItemInformation($result);
					if ($item['type'] == "dir") $dirs[] = $item;
					else $files[] = $item;
				}
			}
			closedir($handle);
		}
		usort($dirs, array($this, "sortByName"));
		usort($files, array($this, "sortByName"));

		$this->jsonResponse(array_merge($dirs, $files));
	}

	private function getItemInformation($name)
	{
		$item = array();
		$item["name"] = $name;
		if (is_dir($name)) {
			$item["type"] = "dir";
			if ($name == "..")
				$item["icon"] = "icon icon-up-open";
			else
				$item["icon"] = "icon icon-folder-empty";
		} else {
			$item["type"] = "file";
			if (in_array(substr($name, -7), array(".tar.gz", ".tar.xz")))
				$type = substr($name, -6);
			elseif (substr($name, -8) == ".tar.bz2")
				$type = "tar.bz2";
			else
				$type = substr(strrchr($name, "."), 1);
			$item["icon"] = $this->getTypeIcon($type);
			$item["ext"] = strtolower($type);
			if (!$this->config['disable_mime_detection'])
				$item["mime_type"] = mime_content_type($name);
		}
		if ($this->config['showlastmodified'] == 1) {
			$item["lastmodified"] = filemtime($name);
		}
		if ($this->config['showfilesize'] == 1) {
			if ($item['type'] == "dir") {
				$item['size_raw'] = 0;
				$item['size'] = "";
			} else {
				$item["size_raw"] = filesize($name);
				if ($item["size_raw"] > 1073741824) $item["size"] = round(($item["size_raw"] / 1073741824), 2) . " GB";
				elseif ($item["size_raw"] > 1048576) $item["size"] = round(($item["size_raw"] / 1048576), 2) . " MB";
				elseif ($item["size_raw"] > 1024) $item["size"] = round(($item["size_raw"] / 1024), 2) . " KB";
				else $item["size"] = $item["size_raw"] . " Byte";
			}
		}
		if ($this->config['showpermissions'] > 0) {
			if ($this->config['showpermissions'] == 1) $item["fileperms"] = substr(decoct(fileperms($name)), -3);
			elseif ($this->config['showpermissions'] == 2) $item["fileperms"] = $this->filePermsDecode(fileperms($name));
			if ($item["fileperms"] == "") $item["fileperms"] = " ";
			$item["filepermmode"] = ($this->config['showpermissions'] == 1) ? "short" : "long";
		}
		if ($this->config['showowner'] == 1) {
			if (function_exists("posix_getpwuid") && fileowner($name) !== false) {
				$ownerarr = posix_getpwuid(fileowner($name));
				$item["owner"] = $ownerarr['name'];
			} else $item["owner"] = false;
		}
		if ($this->config['showgroup'] == 1) {
			if (function_exists("posix_getgrgid") && filegroup($name) !== false) {
				$grouparr = posix_getgrgid(filegroup($name));
				$item["group"] = $grouparr['name'];
			} else $item["group"] = false;
		}
		return $item;
	}

	private function getConfig()
	{
		$ret = $this->config;
		$ret['inline'] = ($this->mode == "inline") ? true : false;
		$ret['isDocroot'] = ($this->getRootDir() == $this->getScriptRoot());

		foreach (array("auth_source", "root_dir") as $field) {
			unset($ret[$field]);
		}
		$this->jsonResponse($ret);
	}

	private function getFolders($d)
	{
		if (!isset($d['dir']))
			$d['dir'] = $this->getRootDir();
		if (!$this->isPathValid($d['dir']))
			echo "[]";
		else {
			$ret = array();
			foreach (glob($this->pathCombine($d['dir'], "*"), GLOB_ONLYDIR) as $dir) {
				array_push($ret, array(
					"text" => htmlspecialchars(basename($dir)),
					"lazyLoad" => true,
					"dataAttr" => array("path" => $dir)
				));
			}
			sort($ret);
			if ($this->getScriptRoot() == realpath($d['dir']))
				$ret = array_merge(
					array(
						0 => array(
							"text" => "/ [root]",
							"dataAttr" => array("path" => $this->getRootDir())
						)
					),
					$ret
				);
			$this->jsonResponse($ret);
		}
	}

	private function searchItems($d)
	{
		if ($this->config['search'] != 1) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			return;
		}

		if (strpos($d['pattern'], '/') !== false) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['pattern_error_slashes']));
			exit(1);
		}
		try {
			$results = $this->searchItemsRecursive($d['pattern']);
			$this->jsonResponse($results);
		} catch (Exception $e) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['error'] . " " . $e->getMessage()));
		}
	}

	private function searchItemsRecursive($pattern, $dir = "")
	{
		$items = array();
		$dir = $dir ? $dir : '.';
		foreach (glob($this->pathCombine($dir, $pattern)) as $result) {
			array_push($items, $this->getItemInformation($result));
		}
		foreach (glob($this->pathCombine($dir, '*'), GLOB_ONLYDIR) as $subdir) {
			$items = array_merge($items, $this->searchItemsRecursive($pattern, $subdir));
		}
		return $items;
	}

	private function getFolderTree($d)
	{
		$this->jsonResponse(
			array_merge(
				array(
					0 => array(
						"text" => "/ [root]",
						"nodes" => array(),
						"dataAttributes" => array("path" => $this->getRootDir())
					)
				),
				$this->getFolderTreeRecursive($d['dir'])
			)
		);
	}

	private function getFolderTreeRecursive($start_dir)
	{
		$ret = array();
		$start_dir = realpath($start_dir);
		if ($handle = opendir($start_dir)) {
			while (false !== ($result = readdir($handle))) {
				if (is_dir($this->pathCombine($start_dir, $result)) && $result != "." && $result != "..") {
					array_push(
						$ret,
						array(
							"text" => htmlspecialchars($result),
							"dataAttributes" => array(
								"path" => $this->pathCombine($start_dir, $result)
							),
							"nodes" => $this->getFolderTreeRecursive($this->pathCombine($start_dir, $result))
						)
					);
				}
			}
		}
		sort($ret);
		return $ret;
	}

	private function copyMove($d)
	{
		if ($this->config['copymove'] != 1) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			exit(1);
		}
		if (!isset($d['destination']) || !$this->isPathValid(realpath($d['destination']))) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_dir']));
			exit(1);
		}
		if (!is_array($d['filenames'])) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_params']));
			exit(1);
		}
		if (!in_array($d['action'], array('copy', 'move'))) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_action']));
			exit(1);
		}
		$err = array();
		$errFlag = -1; // -1 -> all errors; 0 -> at least some errors; 1 -> no errors
		foreach ($d['filenames'] as $file) {
			if (!file_exists($file) || $file == ".." || !$this->isFilenameValid($file)) {
				array_push($err, $file);
			}
			if ($d['action'] == "copy") {
				if ($this->xcopy($file, $d['destination']))
					$errFlag = 0;
				else
					array_push($err, $file);
			} elseif ($d['action'] == "move") {
				if (rename($file, $this->pathCombine($d['destination'], basename($file))))
					$errFlag = 0;
				else
					array_push($err, $file);
			}
		}
		$action = ($d['action'] == "copy" ? "copied" : "moved");
		if (empty($err)) {
			$this->jsonResponse(array("status" => "OK", "message" => ($d['action'] == "copy" ? $this->l['copy_success'] : $this->l['move_success']), "errflag" => "1"));
		} else {
			$errmsg = ($d['action'] == "copy" ? $this->l['copy_error'] : $this->l['move_error']) . "<ul>";
			foreach ($err as $item)
				$errmsg .= "<li>" . $item . "</li>";
			$errmsg .= "</ul>";
			$this->jsonResponse(array("status" => "OK", "message" => $errmsg, "flag" => $errFlag));
		}
	}

	// creates a directory
	private function createDir($w, $dn)
	{
		if ($this->config['createdir'] != 1) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			exit(1);
		}
		if ($dn == "")
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_dir']));
		elseif (!$this->isFilenameValid($dn))
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_dir']));
		else {
			if (@mkdir($dn))
				$this->jsonResponse(array("status" => "OK", "message" => $this->l['folder_create_success']));
			else
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['folder_create_error']));
		}
	}

	// save a file
	private function saveFile($d)
	{
		if ((file_exists($this->pathCombine($d['dir'], $d['filename'])) && $this->config['edit'] != 1) || (!file_exists($this->pathCombine($d['dir'], $d['filename'])) && $this->config['createfile'] != 1)) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			exit(1);
		}
		if (isset($d['filename']) && $this->isFilenameValid($d['filename'])) {
			if (isset($d['content'])) {
				// work around magic quotes
				$content = get_magic_quotes_gpc() == 1 ? stripslashes($d['content']) : $d['content'];
				if (@file_put_contents($d['filename'], $content) !== false) {
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_save_success']));
				} else
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_save_error']));
			} else
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_save_error']));
		} else
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
	}

	// gets the content of a file
	// notice: if the content is not JSON encodable it returns an error
	private function getContent(array $d)
	{
		if ($this->config['edit'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['npermissions']));
		else {
			if (isset($d['filename']) && $this->isFilenameAllowed($d['filename']) && file_exists($d['filename']) && is_readable($d['filename'])) {
				$content = @file_get_contents($d['filename']);
				if (function_exists("mb_check_encoding") && !mb_check_encoding($content, "UTF-8"))
					$content = utf8_encode($content);
				$this->jsonResponse(array("status" => "OK", "data" => array("filename" => $d['filename'], "content" => $content)));
			} else $this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_not_found']));
		}
	}

	// deletes a bunch of files or directories
	private function deleteFiles(array $d)
	{
		if ($this->config['delete'] != 1) $this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		else {
			$err = array();
			$errFLAG = -1; // -1 -> no files deleted; 0 -> at least some files deleted; 1 -> all files deleted
			foreach ($d['filenames'] as $file) {
				if ($this->isFilenameAllowed($file)) {
					if (is_dir($file)) {
						$res = $this->rec_rmdir($file);
						if ($res != 0)
							array_push($err, $file);
						else
							$errFLAG = 0;
					} else {
						if (@unlink($file))
							$errFLAG = 0;
						else
							array_push($err, $file);
					}
				} else {
					array_push($err, $file);
				}
			}
			if (empty($err)) {
				$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_delete_success'], "errflag" => "1"));
			} else {
				$errmsg = $this->l['file_delete_error'] . "<ul>";
				foreach ($err as $item)
					$errmsg .= "<li>" . $item . "</li>";
				$errmsg .= "</ul>";
				$this->jsonResponse(array("status" => "ERROR", "message" => $errmsg, "flag" => $errFLAG));
			}
		}
	}

	// renames a file
	private function renameFile(array $d)
	{
		if ($this->config['rename'] != 1) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		} elseif (!$this->isFilenameValid($d['filename'])) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
		} elseif (!$this->isFilenameValid($d['newname'])) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
		} else {
			if (strpos($d['newname'], '/') !== false)
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['filename_slashes']));
			elseif ($this->config['showhtdocs'] != 1 && (substr($d['newname'], 0, 3) == ".ht" || substr($d['filename'], 0, 3) == ".ht"))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			elseif ($this->config['showhiddenfiles'] != 1 && (substr($d['newname'], 0, 1) == "." || substr($d['filename'], 0, 1) == "."))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			else {
				if (@rename($d['filename'], $d['newname']))
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_rename_success']));
				else
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_rename_error']));
			}
		}
	}

	// provides a file for downloading
	private function downloadFile(array $d, $forceDL = true)
	{
		if ($this->config['download'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		elseif (!$this->isFilenameValid($d['filename']))
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
		elseif ($this->config['showhtdocs'] != 1 && (substr($d['filename'], 0, 3) == ".ht" || substr($d['filename'], 0, 3) == ".ht"))
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		elseif ($this->config['showhiddenfiles'] != 1 && (substr($d['filename'], 0, 1) == "." || substr($d['filename'], 0, 1) == "."))
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		else {
			if (!is_file($d['filename']))
				http_response_code(404);
			else
				$this->fileDownload(array("file" => $d['filename'], "forceDL" => $forceDL));
		}
	}

	// extracts a zip-archive
	private function extractFile(array $d)
	{
		$restoreIFM = false;
		$tmpSelfContent = null;
		$tmpSelfChecksum = null;
		if ($this->config['extract'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		else {
			if (!file_exists($d['filename'])) {
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
				exit(1);
			}
			if (!isset($d['targetdir']) || trim($d['targetdir']) == "")
				$d['targetdir'] = "./";
			if (!$this->isPathValid($d['targetdir'])) {
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_dir']));
				exit(1);
			}
			if (!is_dir($d['targetdir']) && !mkdir($d['targetdir'], 0777, true)) {
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['folder_create_error']));
				exit(1);
			}
			if (realpath($d['targetdir']) == substr($this->getScriptRoot(), 0, strlen(realpath($d['targetdir'])))) {
				$tmpSelfContent = tmpfile();
				fwrite($tmpSelfContent, file_get_contents(__FILE__));
				$tmpSelfChecksum = hash_file("sha256", __FILE__);
				$restoreIFM = true;
			}
			if (substr(strtolower($d['filename']), -4) == ".zip") {
				if (!IFMArchive::extractZip($d['filename'], $d['targetdir'])) {
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['extract_error']));
				} else {
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['extract_success']));
				}
			} else {
				if (!IFMArchive::extractTar($d['filename'], $d['targetdir'])) {
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['extract_error']));
				} else {
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['extract_success']));
				}
			}
			if ($restoreIFM) {
				if ($tmpSelfChecksum != hash_file("sha256", __FILE__)) {
					rewind($tmpSelfContent);
					$fh = fopen(__FILE__, "w");
					while (!feof($tmpSelfContent)) {
						fwrite($fh, fread($tmpSelfContent, 8196));
					}
					fclose($fh);
				}
				fclose($tmpSelfContent);
			}
		}
	}

	// uploads a file
	private function uploadFile(array $d)
	{
		if ($this->config['upload'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		elseif (!isset($_FILES['file']))
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_upload_error']));
		else {
			$newfilename = (isset($d["newfilename"]) && $d["newfilename"] != "") ? $d["newfilename"] : $_FILES['file']['name'];
			if (!$this->isFilenameValid($newfilename))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
			else {
				if ($_FILES['file']['tmp_name']) {
					if (is_writable(getcwd())) {
						if (move_uploaded_file($_FILES['file']['tmp_name'], $newfilename))
							$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_upload_success'], "cd" => $d['dir']));
						else
							$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_upload_error']));
					} else
						$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_upload_error']));
				} else
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_not_found']));
			}
		}
	}

	// change permissions of a file
	private function changePermissions(array $d)
	{
		if ($this->config['chmod'] != 1) $this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		elseif (!isset($d["chmod"]) || $d['chmod'] == "") $this->jsonResponse(array("status" => "ERROR", "message" => $this->l['permission_parse_error']));
		elseif (!$this->isPathValid($this->pathCombine($d['dir'], $d['filename']))) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		} else {
			$chmod = $d["chmod"];
			$cmi = true;
			if (!is_numeric($chmod)) {
				$cmi = false;
				$chmod = str_replace(" ", "", $chmod);
				if (strlen($chmod) == 9) {
					$cmi = true;
					$arr = array(substr($chmod, 0, 3), substr($chmod, 3, 3), substr($chmod, 6, 3));
					$chtmp = "0";
					foreach ($arr as $right) {
						$rtmp = 0;
						if (substr($right, 0, 1) == "r") $rtmp = $rtmp + 4;
						elseif (substr($right, 0, 1) <> "-") $cmi = false;
						if (substr($right, 1, 1) == "w") $rtmp = $rtmp + 2;
						elseif (substr($right, 1, 1) <> "-") $cmi = false;
						if (substr($right, 2, 1) == "x") $rtmp = $rtmp + 1;
						elseif (substr($right, 2, 1) <> "-") $cmi = false;
						$chtmp = $chtmp . $rtmp;
					}
					$chmod = intval($chtmp);
				}
			} else $chmod = "0" . $chmod;

			if ($cmi) {
				try {
					chmod($d["filename"], (int)octdec($chmod));
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['permission_change_success']));
				} catch (Exception $e) {
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['permission_change_error']));
				}
			} else $this->jsonResponse(array("status" => "ERROR", "message" => $this->l['permission_parse_error']));
		}
	}

	// zips a directory and provides it for downloading
	// it creates a temporary zip file in the current directory, so it has to be as much space free as the file size is
	private function zipnload(array $d)
	{
		if ($this->config['zipnload'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermission']));
		else {
			if (!file_exists($d['filename']))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['folder_not_found']));
			elseif (!$this->isPathValid($d['filename']))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_dir']));
			elseif ($d['filename'] != "." && !$this->isFilenameValid($d['filename']))
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
			else {
				unset($zip);
				if ($this->isAbsolutePath($this->config['tmp_dir']))
					$dfile = $this->pathCombine($this->config['tmp_dir'], uniqid("ifm-tmp-") . ".zip"); // temporary filename
				else
					$dfile = $this->pathCombine(__DIR__, $this->config['tmp_dir'], uniqid("ifm-tmp-") . ".zip"); // temporary filename

				try {
					IFMArchive::createZip(realpath($d['filename']), $dfile, array($this, 'isFilenameValid'));
					if ($d['filename'] == ".") {
						if (getcwd() == $this->getScriptRoot())
							$d['filename'] = "root";
						else
							$d['filename'] = basename(getcwd());
					}
					$this->fileDownload(array("file" => $dfile, "name" => $d['filename'] . ".zip", "forceDL" => true));
				} catch (Exception $e) {
					echo $this->l['error'] . " " . $e->getMessage();
				} finally {
					if (file_exists($dfile)) @unlink($dfile);
				}
			}
		}
	}

	// uploads a file from an other server using the curl extention
	private function remoteUpload(array $d)
	{
		if ($this->config['remoteupload'] != 1)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
		elseif (!isset($d['method']) || !in_array($d['method'], array("curl", "file")))
			$this->jsonResponse(array("status" => "error", "message" => $this->l['invalid_params']));
		elseif ($d['method'] == "curl" && $this->checkCurl() == false)
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['error'] . " cURL extention not installed."));
		elseif ($d['method'] == "curl" && $this->checkCurl() == true) {
			$filename = (isset($d['filename']) && $d['filename'] != "") ? $d['filename'] : "curl_" . uniqid();
			$ch = curl_init();
			if ($ch) {
				if ($this->isFilenameValid($filename) == false)
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
				elseif (filter_var($d['url'], FILTER_VALIDATE_URL) === false)
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_url']));
				else {
					$fp = fopen($filename, "w");
					if ($fp) {
						if (!curl_setopt($ch, CURLOPT_URL, urldecode($d['url'])) || !curl_setopt($ch, CURLOPT_FILE, $fp) || !curl_setopt($ch, CURLOPT_HEADER, 0) || !curl_exec($ch))
							$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['error'] . " " . curl_error($ch)));
						else {
							$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_upload_success']));
						}
						curl_close($ch);
						fclose($fp);
					} else {
						$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['file_open_error']));
					}
				}
			} else {
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['error'] . " curl init"));
			}
		} elseif ($d['method'] == 'file') {
			$filename = (isset($d['filename']) && $d['filename'] != "") ? $d['filename'] : "curl_" . uniqid();
			if ($this->isFilenameValid($filename) == false)
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
			else {
				try {
					file_put_contents($filename, file_get_contents($d['url']));
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['file_upload_success']));
				} catch (Exception $e) {
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['error'] . " " . $e->getMessage()));
				}
			}
		} else
			$this->jsonResponse(array("status" => "error", "message" => $this->l['invalid_params']));
	}

	private function createArchive($d)
	{
		if ($this->config['createarchive'] != 1) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['nopermissions']));
			return false;
		}
		if (!$this->isFilenameValid($d['archivename'])) {
			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
			return false;
		}
		$filenames = array();
		foreach ($d['filenames'] as $file)
			if (!$this->isFilenameValid($file)) {
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['invalid_filename']));
				exit(1);
			} else
				array_push($filenames, realpath($file));
		switch ($d['format']) {
			case "zip":
				if (IFMArchive::createZip($filenames, $d['archivename']))
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['archive_create_success']));
				else
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['archive_create_error']));
				break;
			case "tar":
			case "tar.gz":
			case "tar.bz2":
				if (IFMArchive::createTar($filenames, $d['archivename'], $d['format']))
					$this->jsonResponse(array("status" => "OK", "message" => $this->l['archive_create_success']));
				else
					$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['archive_create_error']));
				break;
			default:
				$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['archive_invalid_format']));
				break;
		}
	}

	/*
	   help functions
	 */

	private function log($d)
	{
		file_put_contents($this->pathCombine($this->getRootDir(), "debug.ifm.log"), (is_array($d) ? print_r($d, true) . "\n" : $d . "\n"), FILE_APPEND);
	}

	private function jsonResponse($array)
	{
		$this->convertToUTF8($array);
		$json = json_encode($array);
		if ($json === false) {
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					echo ' - No errors';
					break;
				case JSON_ERROR_DEPTH:
					echo ' - Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					echo ' - Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR:
					echo ' - Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					echo ' - Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default:
					echo ' - Unknown error';
					break;
			}

			$this->jsonResponse(array("status" => "ERROR", "message" => $this->l['json_encode_error'] . " " . $err));
		} else {
			echo $json;
		}
	}

	private function convertToUTF8(&$item)
	{
		if (is_array($item))
			array_walk(
				$item,
				array($this, 'convertToUTF8')
			);
		else
			if (function_exists("mb_check_encoding") && !mb_check_encoding($item, "UTF-8"))
			$item = utf8_encode($item);
	}

	function checkAuth()
	{
		if ($this->config['auth'] == 0)
			return true;

		if (isset($_SERVER['HTTP_X_IFM_AUTH']) && !empty($_SERVER['HTTP_X_IFM_AUTH'])) {
			$cred = explode(":", base64_decode(str_replace("Basic ", "", $_SERVER['HTTP_X_IFM_AUTH'])));
			if (count($cred) == 2 && $this->checkCredentials($cred[0], $cred[1]))
				return true;
		}

		if (session_status() !== PHP_SESSION_ACTIVE) {
			if (isset($this->config['session_lifetime']))
				ini_set('session.gc_maxlifetime', $this->config['session_lifetime']);
			if (isset($this->config['force_session_lifetime']) && $this->config['force_session_lifetime']) {
				ini_set('session.gc_divisor', 1);
				ini_set('session.gc_probability', 1);
			}
			session_start();
		}

		if (!isset($_SESSION['ifmauth']) || $_SESSION['ifmauth'] !== true) {
			$login_failed = false;
			if (isset($_POST["inputLogin"]) && isset($_POST["inputPassword"])) {
				if ($this->checkCredentials($_POST["inputLogin"], $_POST["inputPassword"])) {
					$_SESSION['ifmauth'] = true;
				} else {
					$_SESSION['ifmauth'] = false;
					$login_failed = true;
				}
			}

			if (isset($_SESSION['ifmauth']) && $_SESSION['ifmauth'] === true) {
				return true;
			} else {
				if (isset($_POST["api"])) {
					if ($login_failed === true)
						$this->jsonResponse(array("status" => "ERROR", "message" => "authentication failed"));
					else
						$this->jsonResponse(array("status" => "ERROR", "message" => "not authenticated"));
				} else {
					$this->loginForm($login_failed);
				}
				return false;
			}
		} else {
			return true;
		}
	}

	private function checkCredentials($user, $pass)
	{
		list($src, $srcopt) = explode(";", $this->config['auth_source'], 2);
		switch ($src) {
			case "inline":
				list($uname, $hash) = explode(":", $srcopt);
				$htpasswd = new Htpasswd();
				return $htpasswd->verifyPassword($pass, $hash) ? ($uname == $user) : false;
				break;
			case "file":
				if (@file_exists($srcopt) && @is_readable($srcopt)) {
					$htpasswd = new Htpasswd($srcopt);
					return $htpasswd->verify($user, $pass);
				} else {
					trigger_error("IFM: Fatal: Credential file does not exist or is not readable");
					return false;
				}
				break;
			case "ldap":
				$authenticated = false;
				$ldapopts = explode(";", $srcopt);
				if (count($ldapopts) === 3) {
					list($ldap_server, $rootdn, $ufilter) = explode(";", $srcopt);
				} else {
					list($ldap_server, $rootdn) = explode(";", $srcopt);
					$ufilter = false;
				}
				$u = "uid=" . $user . "," . $rootdn;
				if (!$ds = ldap_connect($ldap_server)) {
					trigger_error("Could not reach the ldap server.", E_USER_ERROR);
					return false;
				}
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				if ($ds) {
					$ldbind = @ldap_bind($ds, $u, $pass);
					if ($ldbind) {
						if ($ufilter) {
							if (ldap_count_entries($ds, ldap_search($ds, $rootdn, $ufilter)) > 0) {
								$authenticated = true;
							} else {
								trigger_error("User not allowed.", E_USER_ERROR);
								$authenticated = false;
							}
						} else {
							$authenticated = true;
						}
					} else {
						trigger_error(ldap_error($ds), E_USER_ERROR);
						$authenticated = false;
					}
					ldap_unbind($ds);
				} else
					$authenticated = false;
				return $authenticated;
				break;
		}
		return false;
	}

	private function loginForm($loginFailed = false, $loginMessage = "")
	{
		$err = "";
		if ($loginFailed)
			$err = '<div class="alert alert-danger" role="alert">' . $loginMessage . '</div>';
		$this->getHTMLHeader();
		$html = str_replace("{{error}}", $err, $this->templates['login']);
		$html = str_replace("{{i18n.username}}", $this->l['username'], $html);
		$html = str_replace("{{i18n.password}}", $this->l['password'], $html);
		$html = str_replace("{{i18n.login}}", $this->l['login'], $html);
		print $html;
		$this->getHTMLFooter();
	}

	private function filePermsDecode($perms)
	{
		$oct = str_split(strrev(decoct($perms)), 1);
		$masks = array('---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx');
		return (sprintf(
			'%s %s %s',
			array_key_exists($oct[2], $masks) ? $masks[$oct[2]] : '###',
			array_key_exists($oct[1], $masks) ? $masks[$oct[1]] : '###',
			array_key_exists($oct[0], $masks) ? $masks[$oct[0]] : '###'
		));
	}

	private function isAbsolutePath($path)
	{
		if ($path === null || $path === '')
			return false;
		return $path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0;
	}

	private function getRootDir()
	{
		if ($this->config['root_dir'] == "")
			return realpath($this->getScriptRoot());
		elseif ($this->isAbsolutePath($this->config['root_dir']))
			return realpath($this->config['root_dir']);
		else
			return realpath($this->pathCombine($this->getScriptRoot(), $this->config['root_dir']));
	}

	private function getScriptRoot()
	{
		return (defined('IFM_FILENAME') ? dirname(realpath(IFM_FILENAME)) : dirname(__FILE__));
	}

	private function getValidDir($dir)
	{
		if (!$this->isPathValid($dir) || !is_dir($dir))
			return "";
		else {
			$rpDir = realpath($dir);
			$rpConfig = $this->getRootDir();
			if ($rpConfig == "/")
				return $rpDir;
			elseif ($rpDir == $rpConfig)
				return "";
			else {
				$part = substr($rpDir, strlen($rpConfig));
				$part = (in_array(substr($part, 0, 1), ["/", "\\"])) ? substr($part, 1) : $part;
				return $part;
			}
		}
	}


	private function isPathValid($dir)
	{
		/**
		 * This function is also used to check non-existent paths, but the PHP realpath function returns false for
		 * nonexistent paths. Hence we need to check the path manually in the following lines.
		 */
		$tmp_d = $dir;
		$tmp_missing_parts = array();
		while (realpath($tmp_d) === false) {
			$tmp_i = pathinfo($tmp_d);
			array_push($tmp_missing_parts, $tmp_i['filename']);
			$tmp_d = dirname($tmp_d);
			if ($tmp_d == dirname($tmp_d)) break;
		}
		$rpDir = $this->pathCombine(realpath($tmp_d), implode("/", array_reverse($tmp_missing_parts)));
		$rpConfig = $this->getRootDir();
		if (!is_string($rpDir) || !is_string($rpConfig)) // can happen if open_basedir is in effect
			return false;
		elseif ($rpDir == $rpConfig)
			return true;
		elseif (0 === strpos($rpDir, $rpConfig))
			return true;
		else
			return false;
	}

	private function chDirIfNecessary($d)
	{
		if (substr(getcwd(), strlen($this->getScriptRoot())) != $this->getValidDir($d) && !empty($d)) {
			chdir($d);
		}
	}

	private function getTypeIcon($type)
	{
		$type = strtolower($type);
		switch ($type) {
			case "aac":
			case "aiff":
			case "mid":
			case "mp3":
			case "wav":
				return 'icon icon-file-audio';
				break;
			case "ai":
			case "bmp":
			case "eps":
			case "tiff":
			case "gif":
			case "jpg":
			case "jpeg":
			case "png":
			case "psd":
			case "svg":
				return 'icon icon-file-image';
				break;
			case "avi":
			case "flv":
			case "mp4":
			case "mpg":
			case "mkv":
			case "mpeg":
			case "webm":
			case "wmv":
			case "mov":
				return 'icon icon-file-video';
				break;
			case "c":
			case "cpp":
			case "css":
			case "dat":
			case "h":
			case "html":
			case "java":
			case "js":
			case "php":
			case "py":
			case "sql":
			case "xml":
			case "yml":
			case "json":
				return 'icon icon-file-code';
				break;
			case "doc":
			case "docx":
			case "odf":
			case "odt":
			case "rtf":
				return 'icon icon-file-word';
				break;
			case "txt":
			case "log":
				return 'icon icon-doc-text';
				break;
			case "ods":
			case "xls":
			case "xlsx":
				return 'icon icon-file-excel';
				break;
			case "odp":
			case "ppt":
			case "pptx":
				return 'icon icon-file-powerpoint';
				break;
			case "pdf":
				return 'icon icon-file-pdf';
				break;
			case "tgz":
			case "zip":
			case "tar":
			case "tgz":
			case "tar.gz":
			case "tar.xz":
			case "tar.bz2":
			case "7z":
			case "rar":
				return 'icon icon-file-archive';
			default:
				return 'icon icon-doc';
		}
	}

	private function rec_rmdir($path)
	{
		if (!is_dir($path)) {
			return -1;
		}
		$dir = @opendir($path);
		if (!$dir) {
			return -2;
		}
		while (($entry = @readdir($dir)) !== false) {
			if ($entry == '.' || $entry == '..') continue;
			if (is_dir($path . '/' . $entry)) {
				$res = $this->rec_rmdir($path . '/' . $entry);
				if ($res == -1) {
					@closedir($dir);
					return -2;
				} else if ($res == -2) {
					@closedir($dir);
					return -2;
				} else if ($res == -3) {
					@closedir($dir);
					return -3;
				} else if ($res != 0) {
					@closedir($dir);
					return -2;
				}
			} else if (is_file($path . '/' . $entry) || is_link($path . '/' . $entry)) {
				$res = @unlink($path . '/' . $entry);
				if (!$res) {
					@closedir($dir);
					return -2;
				}
			} else {
				@closedir($dir);
				return -3;
			}
		}
		@closedir($dir);
		$res = @rmdir($path);
		if (!$res) {
			return -2;
		}
		return 0;
	}

	private function xcopy($source, $dest)
	{
		$isDir = is_dir($source);
		if ($isDir)
			$dest = $this->pathCombine($dest, basename($source));
		if (!is_dir($dest))
			mkdir($dest, 0777, true);
		if (is_file($source))
			return copy($source, $this->pathCombine($dest, basename($source)));

		chdir($source);
		foreach (glob('*') as $item)
			$this->xcopy($item, $dest);
		chdir('..');
		return true;
	}

	// combines two parts to a valid path
	private function pathCombine(...$parts)
	{
		$ret = "";
		foreach ($parts as $part)
			if (trim($part) != "")
				$ret .= (empty($ret) ? rtrim($part, "/") : trim($part, '/')) . "/";
		return rtrim($ret, "/");
	}

	// check if filename is allowed
	public function isFilenameValid($f)
	{
		if (!$this->isFilenameAllowed($f))
			return false;
		if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
			// windows-specific limitations
			foreach (array('\\', '/', ':', '*', '?', '"', '<', '>', '|') as $char)
				if (strpos($f, $char) !== false)
					return false;
		} else {
			// *nix-specific limitations
			foreach (array('/', '\0') as $char)
				if (strpos($f, $char) !== false)
					return false;
		}
		// custom limitations
		foreach ($this->config['forbiddenChars'] as $char)
			if (strpos($f, $char) !== false)
				return false;
		return true;
	}

	private function isFilenameAllowed($f)
	{
		foreach ($this->config['forbiddenChars'] as $char)
			if (strpos($f, $char) !== false)
				return false;

		if ($this->config['showhtdocs'] != 1 && substr($f, 0, 3) == ".ht")
			return false;
		elseif ($this->config['showhiddenfiles'] != 1 && substr($f, 0, 1) == ".")
			return false;
		elseif ($this->config['selfoverwrite'] != 1 && getcwd() == $this->getScriptRoot() && $f == basename(__FILE__))
			return false;
		else
			return true;
	}

	// sorting function for file and dir arrays
	private function sortByName($a, $b)
	{
		if (strtolower($a['name']) == strtolower($b['name'])) return 0;
		return (strtolower($a['name']) < strtolower($b['name'])) ? -1 : 1;
	}

	// is cURL extention avaliable?
	private function checkCurl()
	{
		if (
			!function_exists("curl_init") ||
			!function_exists("curl_setopt") ||
			!function_exists("curl_exec") ||
			!function_exists("curl_close")
		) return false;
		else return true;
	}

	private function fileDownload(array $options)
	{
		if (!isset($options['name']) || trim($options['name']) == "")
			$options['name'] = basename($options['file']);

		if (isset($options['forceDL']) && $options['forceDL']) {
			$content_type = "application/octet-stream";
			header('Content-Disposition: attachment; filename="' . $options['name'] . '"');
		} else {
			$content_type = mime_content_type($options['file']);
		}

		// This header was quite some time present, but I don't know why...
		//header( 'Content-Description: File Transfer' );
		header('Content-Type: ' . $content_type);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($options['file']));

		$file_stream = fopen($options['file'], 'rb');
		$stdout_stream = fopen('php://output', 'wb');

		stream_copy_to_stream($file_stream, $stdout_stream);

		fclose($file_stream);
		fclose($stdout_stream);
	}
}

/**
 * =======================================================================
 * Improved File Manager
 * ---------------------
 * License: This project is provided under the terms of the MIT LICENSE
 * http://github.com/misterunknown/ifm/blob/master/LICENSE
 * =======================================================================
 * 
 * archive class
 *
 * This class provides support for various archive types for the IFM. It can
 * create and extract the following formats:
 * 	* zip
 * 	* tar
 * 	* tar.gz
 * 	* tar.bz2
 */

class IFMArchive
{

	/**
	 * Add a folder to an archive
	 */
	private static function addFolder(&$archive, $folder, $offset = 0, $exclude_callback = null)
	{
		if ($offset == 0)
			$offset = strlen(dirname($folder)) + 1;
		$archive->addEmptyDir(substr($folder, $offset));
		$handle = opendir($folder);
		while (false !== $f = readdir($handle)) {
			if ($f != '.' && $f != '..') {
				$filePath = $folder . '/' . $f;
				if (file_exists($filePath) && is_readable($filePath)) {
					if (is_file($filePath)) {
						if (!is_callable($exclude_callback) || $exclude_callback($f))
							$archive->addFile($filePath, substr($filePath, $offset));
					} elseif (is_dir($filePath)) {
						if (is_callable($exclude_callback))
							self::addFolder($archive, $filePath, $offset, $exclude_callback);
						else
							self::addFolder($archive, $filePath, $offset);
					}
				}
			}
		}
		closedir($handle);
	}

	/**
	 * Create a zip file
	 */
	public static function createZip($src, $out, $exclude_callback = null)
	{
		$a = new ZipArchive();
		$a->open($out, ZIPARCHIVE::CREATE);

		if (!is_array($src))
			$src = array($src);

		foreach ($src as $s)
			if (is_dir($s))
				if (is_callable($exclude_callback))
					self::addFolder($a, $s, null, $exclude_callback);
				else
					self::addFolder($a, $s);
			elseif (is_file($s))
				if (!is_callable($exclude_callback) || $exclude_callback($s))
					$a->addFile($s, substr($s, strlen(dirname($s)) + 1));

		try {
			return $a->close();
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Unzip a zip file
	 */
	public static function extractZip($file, $destination = "./")
	{
		if (!file_exists($file))
			return false;
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === true) {
			$zip->extractTo($destination);
			$zip->close();
			return true;
		} else
			return false;
	}

	/**
	 * Creates a tar archive
	 */
	public static function createTar($src, $out, $t)
	{
		$tmpf = substr($out, 0, strlen($out) - strlen($t)) . "tar";
		$a = new PharData($tmpf);

		try {
			if (!is_array($src))
				$src = array($src);

			foreach ($src as $s)
				if (is_dir($s))
					self::addFolder($a, $s);
				elseif (is_file($s))
					$a->addFile($s, substr($s, strlen(dirname($s)) + 1));
			switch ($t) {
				case "tar.gz":
					$a->compress(Phar::GZ);
					@unlink($tmpf);
					break;
				case "tar.bz2":
					$a->compress(Phar::BZ2);
					@unlink($tmpf);
					break;
			}
			return true;
		} catch (Exception $e) {
			@unlink($tmpf);
			return false;
		}
	}

	/**
	 * Extracts a tar archive
	 */
	public static function extractTar($file, $destination = "./")
	{
		if (!file_exists($file))
			return false;
		$tar = new PharData($file);
		try {
			$tar->extractTo($destination, null, true);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
/**
 * htpasswd parser
 */

class Htpasswd
{
	public $users = [];

	public function __construct($filename = "")
	{
		if ($filename)
			$this->load($filename);
	}

	/**
	 * Load a new htpasswd file
	 */
	public function load($filename)
	{
		unset($this->users);
		if (file_exists($filename) && is_readable($filename)) {
			$lines = file($filename);
			foreach ($lines as $line) {
				list($user, $pass) = explode(":", $line);
				$this->users[$user] = trim($pass);
			}
			return true;
		} else
			return false;
	}

	public function getUsers()
	{
		return array_keys($this->users);
	}

	public function userExist($user)
	{
		return isset($this->users[$user]);
	}

	public function verify($user, $pass)
	{
		if (isset($this->users[$user])) {
			return $this->verifyPassword($pass, $this->users[$user]);
		} else {
			return false;
		}
	}

	public function verifyPassword($pass, $hash)
	{
		if (substr($hash, 0, 4) == '$2y$') {
			return password_verify($pass, $hash);
		} elseif (substr($hash, 0, 6) == '$apr1$') {
			$apr1 = new APR1_MD5();
			return $apr1->check($pass, $hash);
		} elseif (substr($hash, 0, 5) == '{SHA}') {
			return base64_encode(sha1($pass, TRUE)) == substr($hash, 5);
		} else { // assume CRYPT
			return crypt($pass, $hash) == $hash;
		}
	}
}

/**
 * APR1_MD5 class
 *
 * Source: https://github.com/whitehat101/apr1-md5/blob/master/src/APR1_MD5.php
 */
class APR1_MD5
{

	const BASE64_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	const APRMD5_ALPHABET = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	// Source/References for core algorithm:
	// http://www.cryptologie.net/article/126/bruteforce-apr1-hashes/
	// http://svn.apache.org/viewvc/apr/apr-util/branches/1.3.x/crypto/apr_md5.c?view=co
	// http://www.php.net/manual/en/function.crypt.php#73619
	// http://httpd.apache.org/docs/2.2/misc/password_encryptions.html
	// Wikipedia

	public static function hash($mdp, $salt = null)
	{
		if (is_null($salt))
			$salt = self::salt();
		$salt = substr($salt, 0, 8);
		$max = strlen($mdp);
		$context = $mdp . '$apr1$' . $salt;
		$binary = pack('H32', md5($mdp . $salt . $mdp));
		for ($i = $max; $i > 0; $i -= 16)
			$context .= substr($binary, 0, min(16, $i));
		for ($i = $max; $i > 0; $i >>= 1)
			$context .= ($i & 1) ? chr(0) : $mdp[0];
		$binary = pack('H32', md5($context));
		for ($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $mdp : $binary;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $mdp;
			$new .= ($i & 1) ? $binary : $mdp;
			$binary = pack('H32', md5($new));
		}
		$hash = '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$hash = $binary[$i] . $binary[$k] . $binary[$j] . $hash;
		}
		$hash = chr(0) . chr(0) . $binary[11] . $hash;
		$hash = strtr(
			strrev(substr(base64_encode($hash), 2)),
			self::BASE64_ALPHABET,
			self::APRMD5_ALPHABET
		);
		return '$apr1$' . $salt . '$' . $hash;
	}

	// 8 character salts are the best. Don't encourage anything but the best.
	public static function salt()
	{
		$alphabet = self::APRMD5_ALPHABET;
		$salt = '';
		for ($i = 0; $i < 8; $i++) {
			$offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
			$salt .= $alphabet[$offset];
		}
		return $salt;
	}

	public static function check($plain, $hash)
	{
		$parts = explode('$', $hash);
		return self::hash($plain, $parts[2]) === $hash;
	}
}
