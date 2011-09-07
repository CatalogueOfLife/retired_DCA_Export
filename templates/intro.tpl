<p>This page offers an interface on the application that, in this demo, exports data from the 
Catalogue of Life v1.6 (the 2011 DVD editon) in the 
<a href="http://code.google.com/p/gbif-ecat/wiki/DwCArchive">Darwin Core Archive format</a>.</p>

<p>This service can be used to create a Block level I, II or III download of the Catalogue of Life Dataset. 
This is the so-called &lsquo;naked checklist&rsquo;, plus common names (Block II) and distribution (Block III). 
See the &lsquo;Download and Piping Tools Specifications, Deliverable 2.1&rsquo; (28 April 2011) document 
for more information or <a href="documentation/blocks.rtf">read the block definition</a>.</p>

<p>Select a rank from the popup menu and enter a taxon name to start the export. 
The name should match exactly, wildcards are not allowed. Note that the higher the rank, 
the longer the export process will take.</p>

[downloadComplete]

<form style="margin-top: 30px;" action="[action]" method="get">
<div id="popup">
<select name="rank" style="margin-left: 7px;">
[select]</select>
<input type="text" name="taxon" style="margin: 0 7px;" />
<input type="submit" name="submit" value="Start" />
</div>
<div id="radio" style="margin: 9px 3px;">
<input type="radio" name="block" value="1" id="block1" />
<label for="block1">Block I (&lsquo;Naked checklist&rsquo;)</label><br />
<input type="radio" name="block" value="2" id="block2" />
<label for="block2">Block II (Block I plus common names)</label><br />
<input type="radio" name="block" value="3" id="block3"  />
<label for="block3">Block III (Block II plus distribution)</label><br />
<input type="radio" name="block" value="4" id="block4" checked />
<label for="block4">Block IV (All checklist fields: Block III plus additional data)</label><br />
</div>
</form>
