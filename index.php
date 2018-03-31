<?php

// DEV BRANCH CREATED TO SWAP PHP TO HTML

/* REFERENCES:
TYRE SIZES
	http://www.ctc.org.uk/cyclists-library/components/wheels-tyres/tyre-sizes
SIMILAR TOOL
	http://www.bikecalc.com/gear_ratios
	http://www.whycycle.co.uk/bike-gear-calculator
JQUERY MOBILE
	http://www.w3schools.com/jquerymobile/
	http://demos.jquerymobile.com/1.4.5/forms/
*/


$preselect = false;
$maxChainrings = 3;
$maxSprockets = 11;
$debug = false;
$useJqueryMobile = true;
$useBootstrap = false;
// $preselect = true;
?>
<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="UTF-8">
		<title>Gear ratio calculator</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<?php echo ($useJqueryMobile ? '<meta name="viewport" content="width=device-width, initial-scale=1">' : ''); ?>
		<?php echo ($useJqueryMobile ? '<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">' : ''); ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<?php echo ($useJqueryMobile ? '<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>' : ''); ?>
		<script src="gears.js" type="text/javascript"></script>
	</head>

	<body>
		<noscript>
			<p class="noScript">You appear not to have Javascript enabled so this tool will not work for you!</p>
		</noscript>
		
		<div data-role="page" id="infoPage">
			<div data-role="main" class="ui-content">
				<h1>Bike Gearing Calculator</h1>
				<p><b>Usage:</b> on the calculator page you can enter cassette &amp; chainring sizes, crank length, tyre &amp; wheel diameter etc. Based upon the info you enter you will be able to see the results described below.</p>
				<p>There are several presets saved with common gear configurations (and a couple of my bikes) which can be useful for a quick starting point. If you want me to store your bike data as a preset send me the figures.</p>
				<ul>
					<li><b>Ratio:</b> Gear Ratio describes the rotations of the output gear in relation to rotations from the input gear. A ratio of 3:1 would mean that the wheel would rotate 3 times for each rotation of the chainring.</li>
					<li><b>MD:</b> Metres of Development (<a href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development" target="_blank" title="Click here for Wikipedias explanation">wiki</a>)  describes the distance the bike will travel for each full pedal revolution.<br />The formula is <i>(wheel diameter + tyre diameter) &times; &pi; &times; gear ratio</i></li>
					<li><b>GI:</b> Gear Inches (<a href="https://en.wikipedia.org/wiki/Gear_inches" target="_blank" title="Click here for Wikipedias explanation">wiki</a>) also known as Effective Diameter, describes gear ratios in terms of the diameter of an equivalent directly driven wheel if the pedals were fixed to that wheel (like a Penny Farthing).<br />The formula to calculate it is <i>(wheel + tyre diameter in inches) &times; (chainring toothcount &divide; sprocket toothcount)</i></li></li>
					<li><b>GR:</b> Gain Ratio is a Sheldon Brown innovation. Traditional measurements (GI, MD etc.) don't allow for the dis/advantage of pedal arm (crank) length and also make easy comparison of different gearing tricky (46/16 is the same as 53/19 - if the crank lengths are the same) so Sheldon Brown proposed a gear measurement system called <a href="http://sheldonbrown.com/gain.html" target="_blank" title="Click here for Sheldons explanation!">&quot;gain ratio&quot;</a>. It describes the ratio of distance travelled by the bike relative to the radial distance moved of the pedals.<br />His formula is <i>((wheel + tyre radius) &divide; crank length) &times; gear ratio</i>. The benefits of this include:
						<ol>
							<li>Given 2 identically geared/wheeled bikes you can see a numerical representation of the mechanical dis/advantage if they have different crank lengths.</li>
							<li>It's <a href="https://en.wikipedia.org/wiki/Dimensionless_quantity" target="_blank">dimensionless</a>, so whether you supply the measurements in inches, mm or microns the resulting value is the same.</li>
							<li>It&#39;s like a universal language for comparing gearing, all reduced to a single number!</li>
						</ol></li>
					<li><b>Speed/cadence:</b> Optionally add a target cadence to see the speed each gear will achieve at that cadence.<br />The formula is <i>km/h = ((wheel diameter(mm) + tyre diameter(mm)) &times; &pi;) &times; ratio &times; cadence &times; 60 / 100,000</i></li>
					<li><b>Derailleur capacity:</b> This will tell you what capacity derailleur you should be looking for based upon your crank &amp; cassette.<br />The formula to calculate it is <i>(largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)</i></li>
				</ul>
				<a href="#resultPage" class="ui-btn ui-btn-inline ui-icon-grid ui-btn-icon-left ui-mini ui-shadow">Go to calculator</a>
			</div>
		</div>
		
		
		<div data-role="page" id="resultPage">
			<div data-role="main" class="ui-content">
				<a href="#infoPage" class="ui-btn ui-btn-inline ui-icon-info ui-btn-icon-left ui-mini ui-shadow">Home/Info</a>
				<button class="ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="clearAll">Clear data</button>
					<select name="presets" data-icon="gear" data-inline="true" data-mini="true">
						<option value="0">Load a preset</option>
						<optgroup label="Compact chainset">
						<option value="compact09">9spd compact 12-25</option>
						<option value="compact10">10spd compact 12-25</option>
						<option value="compact11">11spd compact 11-28</option>
						</optgroup>
						<optgroup label="Standard chainset">
						<option value="standard09">9spd standard 12-25</option>
						<option value="standard10">10spd standard 12-25</option>
						<option value="standard11">11spd standard 11-28</option>
						<option value="csAgree">CS Cube Agree</option>
						</optgroup>
						<optgroup label="Triple chainset">
						<option value="triple09">9spd triple 12-25</option>
						<option value="csPeloton">CS Cube Peloton</option>
						</optgroup>
					</select>
				
				<div data-role="fieldcontain">
					<label for="chainring1" title="Chainring &amp; sprocket data is required! Add 1-3 rings and 1-11 sprockets">Chainrings:</label>
					<input data-wrapper-class="chainringInput" class="chainrings" placeholder="Chainring 1" type="number" name="chainring1" id="chainring1" />
					<input data-wrapper-class="chainringInput" class="chainrings" placeholder="Chainring 2" type="number" name="chainring2" id="chainring2" />
					<input data-wrapper-class="chainringInput" class="chainrings" placeholder="Chainring 3" type="number" name="chainring3" id="chainring3" />
				</div>
				<div data-role="fieldcontain">
					<label for="sprocket1" title="Chainring &amp; sprocket data is required! Add 1-3 rings and 1-11 sprockets">Sprockets:</label>
					<?php for($x=1;$x<12;$x++) echo '<input data-wrapper-class="sprocketInput" placeholder="Sprocket '.$x.'" class="sprockets sprocket'.$x.'" name="sprocket'.$x.'" type="number" id="sprocket'.$x.' sprockets" />'; ?>
				</div>
				<div data-role="fieldcontain">
					<label for="wheelDiameter" title="If you add your wheel &amp; tyre diameter your results will include more data such as Metres Development (MD) &amp; Gear Inches (GI)">Wheel Diameter (mm):</label>
					<select name="wheelDiameter" id="wheelDiameter" data-inline="true">
						<option value="0">Select wheel size</option>
						<option value="630">27inch (630mm)</option>
						<option value="622"<?php echo ($preselect ? ' selected="selected"' : ''); ?>>700c/29er (622mm)</option>
						<option value="584">650b/27.5 (584mm)</option>
						<option value="571">650c (571mm)</option>
						<option value="559">26inch (559mm-mtb)</option>
						<option value="547">24inch (547mm-S5)</option>
						<option value="540">24inch (540mm-E6)</option>
						<option value="520">24inch (520mm-Terry)</option>
						<option value="451">20inch (451mm-Recumbent)</option>
						<option value="419">20inch (419mm-Schwinn)</option>
						<option value="406">20inch (406mm-Recumbent)</option>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="tyreDiameter" title="If you add your wheel &amp; tyre diameter your results will include more data such as Metres Development (MD) &amp; Gear Inches (GI)">Tyre Diameter (mm):</label>
					<select name="tyreDiameter" id="tyreDiameter" data-inline="true">
						<option value="0">Select tyre diameter</option>
						<?php foreach(array('18c'=>36,'23c'=>46,'25c'=>50,'28c'=>56,'32c'=>64,'35c'=>70,'47c'=>94) as $tyre=>$diameter) echo '<option value="'.$diameter.'"'.($preselect && $diameter==50 ? ' selected="selected"' : '').'>'.$tyre.'</option>'; ?>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="crankLength" title="This value can drastically alter the way 2 identically geared bikes feel to pedal. See the info page for a more detailed explanation...">Crank length (mm):</label>
					<input data-inline="true" data-wrapper-class="crankLengthInput" name="crankLength" id="crankLength" placeholder="Crank length (mm)" type="number"<?php echo ($preselect ? 'value="170"' : ''); ?> />
				</div>
				<div data-role="fieldcontain">
					<label for="targetCadence" title="If you enter a cadence value here your results will include the speed at this cadence for each of your gear combinations.">Target cadence (rpm):</label>
					<input data-inline="true" data-wrapper-class="cadenceInput" name="targetCadence" id="targetCadence" placeholder="Target cadence (rpm)" type="number"<?php echo ($preselect ? 'value="90"' : ''); ?> />
				</div>
				
				
				<div data-role="fieldcontain">
					<label for="orientationToggler" title="You can display the results in a landscape or portait result table">Orientation:</label>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="radio" name="orientationToggler" id="portrait" value="portrait" checked="checked">
						<label for="portrait">Portrait</label>
						<input type="radio" name="orientationToggler" id="landscape" value="landscape">
						<label for="landscape">Landscape</label>
					</fieldset>
				</div>
				
				<div data-role="fieldcontain">
					<label for="unitToggler" title="You can select metric or imperial units for the results. This includes recalculating gear INCHES or METRES development into your chosen units, which might seem odd but actually makes sense!">Units:</label>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="radio" name="unitToggler" id="metric" value="metric" checked="checked">
						<label for="metric">Metric</label>
						<input type="radio" name="unitToggler" id="imperial" value="imperial">
						<label for="imperial">Imperial</label>
					</fieldset>
				</div>
				
				<div data-role="fieldcontain">
					<label for="colourToggler" title="The results table is colour coded by default showing how relatively hard (red) or easy (green) each of the gears is to pedal in. On some displays the colouring makes the text tricky to read so you can toggle the colouring here.">Colours:</label>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="radio" name="colourToggler" id="colourOn" value="1" checked="checked">
						<label for="colourOn">Coloured</label>
						<input type="radio" name="colourToggler" id="colourOff" value="0">
						<label for="colourOff">Plain</label>
					</fieldset>
				</div>
				
				<p id="derailleurCapacity"></p>
				
				<table class="resultTable portrait">
					<thead>
						<tr>
							<th></th>
						<?php
							for ($chainring=1;$chainring<=$maxChainrings;$chainring++) {
								echo '<th class="chainringHeading chainring'.$chainring.'">Chainring '.$chainring.'</th>';
							}
						?>
						</tr>
					</thead>
					<tbody>
						<?php
							for ($sprocket=1;$sprocket<=$maxSprockets;$sprocket++) {
								echo '<tr>';
								echo '<th class="sprocketHeading sprocket'.$sprocket.'">Sprocket '.$sprocket.'</th>';
								for ($chainring=1;$chainring<=$maxChainrings;$chainring++) {
									echo '<td class="result chainring'.$chainring.'sprocket'.$sprocket.'">chainring'.$chainring.'sprocket'.$sprocket.'</td>';
								}
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				<table class="resultTable landscape">
					<thead>
						<tr>
							<th></th>
						<?php
							for ($sprocket=1;$sprocket<=$maxSprockets;$sprocket++) {
								echo '<th class="sprocketHeading sprocket'.$sprocket.'">Sprocket '.$sprocket.'</th>';
							}
						?>
						</tr>
					</thead>
					<tbody>
						<?php
							for ($chainring=1;$chainring<=$maxChainrings;$chainring++) {
								echo '<tr>';
								echo '<th class="chainringHeading chainring'.$chainring.'">Chainring '.$chainring.'</th>';
								for ($sprocket=1;$sprocket<=$maxSprockets;$sprocket++) {
									echo '<td class="result chainring'.$chainring.'sprocket'.$sprocket.'">chainring'.$chainring.'sprocket'.$sprocket.'</td>';
								}
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		
		
		
	</body>
</html>