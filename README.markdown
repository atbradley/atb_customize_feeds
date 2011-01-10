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
