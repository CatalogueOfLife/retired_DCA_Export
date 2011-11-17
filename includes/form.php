<span id="blah">click me</span>

<div class="ui-widget" id="classification">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<div id="text">
<p><label for="kingdom">Top level group</label> <input id="kingdom"
    name="kingdom" type="text" value="<?php echo $kingdom; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="phylum">Phylum</label> <input id="phylum" name="phylum"
    type="text" value="<?php echo $phylum; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="class">Class</label> <input id="class" name="class"
    type="text" value="<?php echo $class; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="order">Order</label> <input id="order" name="order"
    type="text" value="<?php echo $order; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="superfamily">Superfamily</label> <input id="superfamily"
    name="superfamily" type="text"
    value="<?php echo $superfamily; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="family">Family</label> <input id="family" name="family"
    type="text" value="<?php echo $family; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>

<p><label for="genus">Genus</label> <input id="genus" name="genus"
    type="text" value="<?php echo $genus; ?>">
<span class="showall ui-icon ui-icon-triangle-1-s">show all</span></p>
</div>

<div id="radio">
<input type="radio" name="block" value="1" id="block1" 
    <?php echo ($block == 1 ? 'checked' : ''); ?> />
<label for="block1">Classification only</label>
<input type="radio" name="block" value="2" id="block2" 
    <?php echo ($block == 2 ? 'checked' : ''); ?> />
<label for="block2">Taxonomic index</label>
<input type="radio" name="block" value="3" id="block3" 
    <?php echo (!in_array($block, array(1,2)) ? 'checked' : ''); ?>  />
<label for="block3">Complete data</label>
</div>

<div id="buttons">
<button id="reset">Clear</button>
<button type="submit">Download</button>
</div>

</form>
</div>
