<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'atb_customize_feeds';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.9';
$plugin['author'] = 'Adam Bradley';
$plugin['author_uri'] = 'http://www.adamtbradley.com/';
$plugin['description'] = 'Provides customization options for syndication feeds.';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
$plugin['type'] = '1';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
global $atb_cf_preflist;

//Preference list.  Used to populate the preferences table 
//and as defaults if no preferences have been saved.
$atb_cf_preflist = array('atb_cf_afeed' =>
				array('default' => 'atb_atom_feed',
					'title' => 'Atom Feed Form'),
			'atb_cf_rfeed' =>
				array('default' => 'atb_rss_feed',
					'title' => 'RSS Feed Form'),
			'atb_cf_aitem' =>
				array('default' => 'atb_entry_atom',
					'title' => 'Atom Item Form'),
			'atb_cf_ritem' =>
				array('default' => 'atb_entry_rss',
					'title' => 'RSS Item Form'),
			'atb_cf_eform' =>
				array('default' => 'atb_excerpt',
					'title' => 'Excerpt Form'),
			'atb_cf_bform' =>
				array('default' => 'atb_body',
					'title' => 'Body Form'),
			'atb_cf_rns' =>
				array('default' => 'atb_rss_namespace',
					'title' => 'RSS Namespace'),
			'atb_cf_ans' =>
				array('default' => 'atb_atom_namespace',
					'title' => 'Atom Namespace'),
			);

//Define callbacks.
register_callback('atb_cf_setprefs', 'prefs', 'advanced_prefs', 1);
register_callback('atb_rss_header', 'rss_head');
register_callback('atb_atom_header', 'atom_head');
register_callback('atb_atom_item_handler', 'atom_entry');
register_callback('atb_rss_item_handler', 'rss_entry');
register_callback('atb_rss_ns_handler', 'rss_namespace');
register_callback('atb_atom_ns_handler', 'atom_namespace');
function atb_atom_item_handler($event, $step) { return atb_feed_item('atom'); }
function atb_rss_item_handler($event, $step) { return atb_feed_item('rss'); }

//Add preferences to the database, and set their names in $textarray (used for internationalization)
function atb_cf_setprefs($event, $step) {
	global $prefs, $textarray, $atb_cf_preflist;

	foreach ($atb_cf_preflist as $name => $p) {
		//So on the advanced prefs page, we see labels like "Body Form" 
		//instead of "atb_cf_bform".
		$textarray[$name] = $p['title'];
		
		//If our preferences haven't been set yet, add them.
		if (!isset($prefs[$name])) {
			set_pref($name, $p['default'], 'feeds', 1);
		}	
	}	
}

//Called for each item of an RSS/Atom feed.
function atb_feed_item($format = 'rss') {
	global $prefs, $atb_cf_preflist, $thisarticle;

	//Find the form to use for the excerpt and replace the article excerpt with its output.
	$form = isset($prefs['atb_cf_eform']) ? $prefs['atb_cf_eform'] : $atb_cf_preflist['atb_cf_eform']['default'];
	$a = parse(fetch_form($form));
	$thisarticle['excerpt'] = $a ? $a : $thisarticle['excerpt'];


	//Find the form to use for the body and replace the article body with its output.
	$form = isset($prefs['atb_cf_bform']) ? $prefs['atb_cf_bform'] : $atb_cf_preflist['atb_cf_bform']['default'];
	$a = parse(fetch_form($form));
	$thisarticle['body'] = $a ? $a : $thisarticle['body'];
	

	if ($format == 'rss') {
		$form = isset($prefs['atb_cf_ritem']) ? $prefs['atb_cf_ritem'] : $atb_cf_preflist['atb_cf_ritem']['default'];
	} else {
		$form = isset($prefs['atb_cf_aitem']) ? $prefs['atb_cf_aitem'] : $atb_cf_preflist['atb_cf_aitem']['default'];
	}

	//The return value of this callback is added to the feed <item> node.
	return parse(fetch_form($form));
}

//Callback for RSS feeds.  Return value is added to the <channel> node before any <item>s.
function atb_rss_header($event, $step) {
	global $prefs, $atb_cf_preflist;
	$formname =  isset($prefs['atb_cf_rfeed']) ? $prefs['atb_cf_rfeed'] : $atb_cf_preflist['atb_cf_rfeed']['default'];
	$form = fetch_form($formname);
	return parse($form);
}

//Callback for Atom feeds.  Return value is added before any <entry>s.
function atb_atom_header($event, $step) {
	global $prefs, $atb_cf_preflist;
	$formname = isset($prefs['atb_cf_afeed']) ? $prefs['atb_cf_afeed'] : $atb_cf_preflist['atb_cf_afeed']['default'];
	$form = fetch_form($formname);
	return parse($form);
}

//Callbacks for additional XML namespaces.  
function atb_rss_ns_handler($event, $step) {
	global $prefs, $atb_cf_preflist;
	$formname = isset($prefs['atb_cf_rns']) ? $prefs['atb_cf_rns'] : $atb_cf_preflist['atb_cf_rns']['default'];
	$form = fetch_form($formname);
	return parse($form);
}

function atb_atom_ns_handler($event, $step) {
	global $prefs, $atb_cf_preflist;
	$formname = isset($prefs['atb_cf_ans']) ? $prefs['atb_cf_ans'] : $atb_cf_preflist['atb_cf_ans']['default'];
	$form = fetch_form($formname);
	return parse($form);
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
<h1>atb_customize_feeds</h1>
<p><code>atb_customize_feeds</code> allows you to modify Textpattern's syndication feeds by creating a few new forms to be parsed and added to your feeds.</p>

<p>The names of these feeds can be modified on your <a href="index.php?event=prefs&step=advanced_prefs">Advanced Preferences page</a>, if the defaults don't suit you.</p>

<h2>Excerpt Form (default: <code>atb_excerpt</code>)</h2>

<p>The output of this form replaces the article excerpt.  If you're creating excerpts with <code><a href="http://www.wilshireone.com/textpattern-plugins/rss_auto_excerpt">rss_auto_excerpt</a></code>, this form might contain something like this:</p>

<pre>
&lt;txp:rss_auto_excerpt paragraphs="1" /&gt;
</pre>

<h2>Body Form (default: <code>atb_body</code>)</h2>

<p>The output of this form replaces the article excerpt.  It might say something like:</p>

<pre>
&lt;txp:body /&gt;
&lt;p&gt;Read this or comment &lt;txp:permlink&gt;on my website&lt;/txp:permlink&gt;&lt;/p&gt;
</pre>

<h2>RSS Feed Form (default: <code>atb_rss_feed</code>)</h2>

<p>Should contain feed items that will be added to the beginning of the RSS Feed, before any feed items are outputted.  For example:</p>
<pre>&lt;language&gt;en-us&lt;/language&gt;
&lt;docs&gt;http://blogs.law.harvard.edu/tech/rss&lt;/docs&gt;</pre>

<h2>Atom Feed Form (default: <code>atb_atom_feed</code>)</h2>

<p>Should contain feed items that will be added to the beginning of the Atom Feed, before any feed items are outputted.</p>

<h2>RSS Item Form (default: <code>atb_entry_rss</code>)</h2>

<p>Should contain XML nodes which will be added to each &lt;item&gt; of the RSS feed.  Could be used, for example, to add a file <a href="http://cyber.law.harvard.edu/rss/rss.html#ltenclosuregtSubelementOfLtitemgt">&lt;enclosure&gt;</a></p>


<h2>RSS Item Form (default: <code>atb_entry_atom</code>)</h2>

<p>Should contain XML nodes which will be added to each &lt;item&gt; of the RSS feed.  Could be used, for example, to add a file <a href="http://cyber.law.harvard.edu/rss/rss.html#ltenclosuregtSubelementOfLtitemgt">&lt;enclosure&gt;</a></p>


<h2>RSS Item Form (default: <code>atb_entry_atom</code>)</h2>

<p>Should contain XML nodes which will be added to each &lt;entry&gt; of the Atom feed.</p>
# --- END PLUGIN HELP ---
-->
<?php
}
?>