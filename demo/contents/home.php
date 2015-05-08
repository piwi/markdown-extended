<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$readme_path = $bootstrapGetPath(array(
    dirname(dirname(__DIR__)), 'README.md'
));
$mde_content = \MarkdownExtended\MarkdownExtended::parseSource($readme_path, $parse_options);

$data = array();

$data['content'] = <<<CTT
<nav class="main-nav jumbotron">
    <div class="row">
      <div class="col-sm-12 col-md-6">
        <h3>Version & Manifest</h3>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading-1">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1" title="Info extracted from your package version manifest">
                            <span class="fa fa-caret-down"></span>&nbsp;Full package manifest
                        </a>
                    </h4>
                </div>
                <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
                    <div class="panel-body" id="manifest">
                        <ul class="list_infos"></ul>
                        <p class="credits">Info extracted from your current package's "composer.json" manifest file.</p>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading-2">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2" title="Info extracted from the repository on GitHub.com">
                            <span class="fa fa-caret-down"></span>&nbsp;Current repository info
                        </a>
                    </h4>
                </div>
                <div id="collapse-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2">
                    <div class="panel-body" id="github">
                        <strong>Last commits</strong>
                        <ul id="commits_list"></ul>
                        <strong>Last bugs</strong>
                        <ul id="bugs_list"></ul>
                        <p class="credits">Info requested to the package sources repository on GitHub.com.</p>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-6">
        <div class="small">
            <h3>Sources & Updates</h3>
            <p><a href="http://github.com/piwi/markdown-extended" title="http://github.com/piwi/markdown-extended" class="btn btn-primary"><i class="fa fa-github-alt"></i>&nbsp;See online on GitHub</a></p>
            <p class="">The sources of this application are hosted on <a href="http://github.com">GitHub</a>. To follow sources updates, report a bug or read opened bug tickets and any other information, please see the GitHub website above.</p>
        </div>
        <div class="small" id="menu_socials">
            <div class="addthis_toolbox addthis_default_style addthis_16x16_style">
                <a href="http://github.com/piwi/markdown-extended" target="_blank" title="GitHub">
                    <span class="at16nc at300bs at15nc atGitHub"></span>
                </a>
                <a class="addthis_button_email"></a>
                <a class="addthis_button_print"></a>
                <a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a>
            </div>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=undefined"></script>
        </div>
      </div>
    </div>
</nav>
<hr>
{$mde_content->getBody()}
CTT;

$data['javascript'] = <<<CTT
$(function() {

// list manifest content
    var manifest_url = '../composer.json';
    var manifest_ul = $('#manifest').find('ul');
    getPluginManifest(manifest_url, function(data){
        manifest_ul.append( getNewInfoItem( data.name, 'title' ) );
        if (data.version) {
            manifest_ul.append( getNewInfoItem( data.version, 'version' ) );
        } else if (data.extra["branch-alias"] && data.extra["branch-alias"]["dev-master"]) {
            manifest_ul.append( getNewInfoItem( data.extra["branch-alias"]["dev-master"], 'version' ) );
        }
        manifest_ul.append( getNewInfoItem( data.description, 'description' ) );
        manifest_ul.append( getNewInfoItem( data.license, 'license' ) );
        manifest_ul.append( getNewInfoItem( data.homepage, 'homepage', data.homepage ) );
    });

// list GitHub info
    var github = 'https://api.github.com/repos/{$package['name']}/';
    // commits list
    var github_commits = $('#github').find('#commits_list');
    getGitHubCommits(github, function(data){
        if (data!==undefined && data!==null) {
            $.each(data, function(i,o) {
                if (o!==null && typeof o==='object' && o.commit.message!==undefined && o.commit.message.length)
                    github_commits.append( getNewInfoItem( o.commit.message, (o.commit.committer.date || ''), (o.commit.url || '') ) );
            });
        } else {
            github_commits.append( getNewInfoItem( 'No commit for now.', '' ) );
        }
    });
    // bugs list
    var github_bugs = $('#github').find('#bugs_list');
    getGitHubBugs(github, function(data){
        if (data!==undefined && data!==null) {
            $.each(data, function(i,o) {
                if (o!==null && typeof o==='object' && o.title!==undefined && o.title.length)
                    github_bugs.append( getNewInfoItem( o.title, (o.created_at || ''), (o.html_url || '') ) );
            });
        } else {
            github_bugs.append( getNewInfoItem( 'No opened bug for now.', '' ) );
        }
    });

});
CTT;

return $data;
