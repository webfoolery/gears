<?php
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
		<?php echo ($useJqueryMobile ? '<meta name="viewport" content="width=device-width, initial-scale=1">' : ''); ?>
		<style>
			/* table.resultTable th, table.resultTable td {border:1px solid #777;}
			table.resultTable label {}
			table.resultTable input {width:4em;}
			.unitSwitcher .ui-slider-switch { width: 9em }
			div.resultBox {text-shadow:none;font-size:0.8em;} */
			
			body {font-family:arial,helvetica,sans-serif;font-size:1em;}
			input.sprockets, input.chainrings {width:90px;}
			table.resultTable {border-collapse:collapse;}
			table.resultTable th, table.resultTable td {font-size:.8em;border:1px solid #777;}
			.cadenceInput, .crankLengthInput {width:200px!important;}
			.chainringInput, .sprocketInput {width:120px!important;}
			
			/* @media only screen and (min-width: 980px){
				.ui-page, ui-footer {
					width: 980px !important;
					margin: 0 auto !important;
					position: relative !important;
					border-right: 5px #666 outset !important;
					border-left: 5px #666 outset !important;
				}
			} */
		</style>
		<?php echo ($useJqueryMobile ? '<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">' : ''); ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<?php echo ($useJqueryMobile ? '<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>' : ''); ?>
		<script type="text/javascript">
			$(document).ready(function(){
				gears = {
					debug: <?php echo ($debug ? 'true' : 'false'); ?>,
					chainringCount: 0,
					sprocketCount: 0,
					chainrings: [],
					sprockets: [],
					wheelDiameter: 0,
					tyreDiameter: 0,
					crankLength: 0,
					targetCadence: 0,
					units: 'metric',
					varDump: function(dumpData, title) {
						// console.log(this);
						console.log(title, (dumpData ? JSON.parse(JSON.stringify(this)) : ''));
					},
					initialise: function() {
						var self = this;
						this.resetTable();
						$("#clearAll").click(function(){self.clearAll()});
						$("input").change(function(){
							// if (this.name.indexOf('targetCadence') === 0) self.targetCadence = Number(this.value);
							// else if (this.name.indexOf('chainring') === 0) self.recalculate();
							// else if (this.name.indexOf('sprocket') === 0) self.recalculate();
							// else if (this.name.indexOf('unitToggler') === 0) self.recalculate();
							if (self.debug) self.varDump(false, 'Input changed');
							self.recalculate()
						});
						$("select").change(function(){
							if (this.name.indexOf('preset') === 0) return self.usePreset(this.value);
							// if (this.name.indexOf('tyreDiameter') === 0) self.tyreDiameter = Number(this.value);
							// else if (this.name.indexOf('chainringCount') === 0) self.chainringCount = Number(this.value);
							// else if (this.name.indexOf('sprocketCount') === 0) self.sprocketCount = Number(this.value);
							// else if (this.name.indexOf('wheelDiameter') === 0) self.wheelDiameter = Number(this.value);
							if (self.debug) self.varDump(false, 'Select changed');
							self.recalculate()
						});
					},
					usePreset: function(selectedPreset) {
						if (!selectedPreset) selectedPreset = 'chris';
						var self = this;
						this.clearAll();
						var presets = {
							chris: {
								chainrings: [50,39,30], 
								sprockets: [11,12,14,15,17,19,21,24,27], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							standard09: {
								chainrings: [53,39], 
								sprockets: [12,13,14,15,17,19,21,23,25], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							standard10: {
								chainrings: [53,39], 
								sprockets: [12,13,14,15,16,17,19,21,23,25], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							standard11: {
								chainrings: [53,39], 
								sprockets: [11,12,13,14,15,17,19,21,23,25,28], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							compact09: {
								chainrings: [50,34], 
								sprockets: [12,13,14,15,17,19,21,23,25], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							compact10: {
								chainrings: [50,34], 
								sprockets: [12,13,14,15,16,17,19,21,23,25], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							compact11: {
								chainrings: [50,34], 
								sprockets: [11,12,13,14,15,17,19,21,23,25,28], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							},
							triple09: {
								chainrings: [50,39,30], 
								sprockets: [12,13,14,15,17,19,21,23,25], 
								wheelDiameter: 622, 
								tyreDiameter: 50, 
								crankLength: 170, 
								targetCadence: 90, 
								units: 'imperial'
							}
						};
						// $.each(presets[selectedPreset], function(index, value){
							// self[index] = value;
							// console.log(index,value);
							// $('[name='+index+']').val(value);
						// });
						$('[name=wheelDiameter]').val(presets[selectedPreset]['wheelDiameter']).selectmenu("refresh", true);
						$('[name=tyreDiameter]').val(presets[selectedPreset]['tyreDiameter']).selectmenu("refresh", true);
						$('[name=targetCadence]').val(presets[selectedPreset]['targetCadence']);
						$('[name=crankLength]').val(presets[selectedPreset]['crankLength']);
						for (c = 1; c <= presets[selectedPreset]['chainrings'].length; c++) $('[name=chainring'+c+']').val(presets[selectedPreset]['chainrings'][c-1]);
						for (s = 1; s <= presets[selectedPreset]['sprockets'].length; s++) $('[name=sprocket'+s+']').val(presets[selectedPreset]['sprockets'][s-1]);
						this.recalculate();
					},
					recalculate: function() {
						this.resetTable();
						this.getChainrings();
						this.getSprockets();
						this.wheelDiameter = Number($('[name=wheelDiameter]').val());
						this.tyreDiameter = Number($('[name=tyreDiameter]').val());
						this.targetCadence = Number($('[name=targetCadence]').val());
						this.crankLength = Number($('[name=crankLength]').val());
						if (this.chainrings.length && this.sprockets.length) {
							for (crank=0; crank < this.chainrings.length; crank++) {
								$('th.chainring'+(crank +1)).text(this.chainrings[crank]).show();
								for (sprocket=0; sprocket < this.sprockets.length; sprocket++) {
									$('tr.sprocket'+(sprocket +1)).show();
									$('th.sprocket'+(sprocket +1)).text(this.sprockets[sprocket]);
									$('.chainring'+(crank +1) + 'sprocket'+(sprocket +1)).show();
									if (this.chainrings[crank] && this.sprockets[sprocket]) {
										var ratio = Number(this.chainrings[crank]/this.sprockets[sprocket]).toFixed(2);
										var output = 'Ratio='+ratio + ':1';
										if (this.wheelDiameter) {
											output += '<br />MD='+this.getUnitValue((((this.wheelDiameter + this.tyreDiameter) * 3.141 * ratio)/1000), 'm');
											// output += '<br />ED='+this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio), 'mm');
											// output += '<br />GI='+(((this.wheelDiameter + this.tyreDiameter) / 25.4) * ratio).toFixed(0) + 'in';
											output += '<br />GI='+this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio)/10, 'cm');
											if (this.crankLength) {
												//  ((wheel + tyre radius) / crank length) * gear ratio
												var gr = ((((this.wheelDiameter + this.tyreDiameter) / 2) / this.crankLength) * ratio).toFixed(2);
												output += '<br />GR='+gr;
											}
											if (this.targetCadence) {
												var speed = (((this.wheelDiameter + this.tyreDiameter) * 3.141) * ratio * this.targetCadence * 60) / 1000000;
												output += '<br />'+this.targetCadence+'rpm='+this.getUnitValue(speed,'km/h');
											}
										}
										if ($('#showColours').is(':checked')) $('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).css('background-color','hsl('+this.getHue(ratio)+', 100%, 50%)');
										else $('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).css('background-color','transparent');
										$('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).html(output);
									}
								}
							}
							if (this.sprockets.length >=2) {
								derailleurCapacityVal = Math.max.apply(Math, this.chainrings) - Math.min.apply(Math, this.chainrings) + Math.max.apply(Math, this.sprockets) - Math.min.apply(Math, this.sprockets);
								derailleurCapacityText = '('+Math.max.apply(Math, this.chainrings)+'-'+Math.min.apply(Math, this.chainrings)+')+('+Math.max.apply(Math, this.sprockets)+'-'+Math.min.apply(Math, this.sprockets)+') = '+derailleurCapacityVal;
								$('#derailleurCapacity').text('Derailleur capacity: '+derailleurCapacityText).show();
							}
							$('.resultTable').show();
						}
						else {
							$('#derailleurCapacity').hide()
						}
						if (this.debug) this.varDump(true, 'recalculate complete');
					},
					getUnitValue: function(value, unit) {
						// RECEIVES A METRIC VALUE/UNIT & RETURNS IN IMPERIAL IF REQUIRED
						if ($('[name=unitToggler]:checked').val() == 'metric') return value.toFixed(2) + unit;
						if (unit == 'km/h') return (value * 0.621371192).toFixed(2) + 'mph';
						if (unit == 'm') return (value * 3.2808399).toFixed(2) + 'ft';
						if (unit == 'mm') return (value /25.4).toFixed(2) + 'in';
						if (unit == 'cm') return (value /2.54).toFixed(2) + 'in';
					},
					getHue: function(ratio) {
						return ((5 - ratio) * 20).toFixed(0);
						// return ((5 - ratio) * 50).toFixed(0);
					},
					getChainrings: function() {
						var chainringsTemp = [];
						$('.chainrings').each(function(el){
							var chainring = Number($(this).val());
							if (chainring) chainringsTemp.push(chainring);
						});
						this.chainrings = chainringsTemp;
						this.chainringCount = chainringsTemp.length;
						if (this.debug) this.varDump(true, 'getChainrings complete');
					},
					getSprockets: function() {
						var self = this;
						this.sprockets = [];
						$('.sprockets').each(function(el){
							var sprocket = Number($(this).val());
							if (sprocket) self.sprockets.push(sprocket);
						});
						this.sprocketCount = this.sprockets.length;
						if (this.debug) this.varDump(true, 'getsprockets complete');
					},
					clearAll: function() {
						this.resetTable();
						$('.result').html('').css('background-color', 'transparent');
						$('input.chainrings').val('');
						$('input.sprockets').val('');
						$('[name=wheelDiameter]').val(0).selectmenu("refresh", true);
						$('[name=chainringCount]').val(0);
						$('[name=sprocketCount]').val(0);
						$('[name=tyreDiameter]').val(0).selectmenu("refresh", true);
						$('[name=crankLength]').val('');
						$('[name=targetCadence]').val('');
						// $('[name=presets]').val(0).selectmenu("refresh", true);
						if (this.debug) this.varDump(true, 'clearAll complete');
						this.recalculate();
					},
					resetTable: function() {
						$('.resultTable').hide();
						$('.chainringHeading').hide();
						$('.sprocketHeading').hide();
						$('.result').hide();
					}
				};
				
				
				gears.initialise()
				// setDefaults();
			});

		</script>
	</head>

	<body>
		<div data-role="page" id="resultPage">
			<div data-role="main" class="ui-content">
				<a href="#infoPage" class="ui-btn ui-btn-inline ui-icon-info ui-btn-icon-left ui-mini ui-shadow">Info</a>
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
						</optgroup>
						<optgroup label="Triple chainset">
						<option value="triple09">9spd triple 12-25</option>
						<option value="chris">Chris&#39;s triple 11-27</option>
						</optgroup>
					</select>
				<!--
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="chris">Chris</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="compact09">Compact 9spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="compact10">Compact 10spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="compact11">Compact 11spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="standard09">Standard 9spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="standard10">Standard 10spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="standard11">Standard 11spd</button>
				<button class="preset ui-btn ui-btn-inline ui-btn-icon-left ui-mini ui-shadow" type="button" id="triple09">Triple 9spd</button>
				-->
				
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
					<label for="unitToggler" title="You can select metric or imperial units for the results. This includes recalculating gear INCHES or METRES development into your chosen units, which might seem odd but actually makes sense!">Units:</label>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="radio" name="unitToggler" id="metric" value="metric" checked="checked">
						<label for="metric">Metric</label>
						<input type="radio" name="unitToggler" id="imperial" value="imperial">
						<label for="imperial">Imperial</label>
					</fieldset>
				</div>
				<!--<p>
				<label for="unitToggler">Units:</label>
				<input type="radio" name="unitToggler" id="metric" value="metric" checked="checked">
				<label for="metric">Metric</label>
				<input type="radio" name="unitToggler" id="imperial" value="imperial">
				<label for="imperial">Imperial</label>
				</p>-->
				
				
				<div data-role="fieldcontain">
					<label for="showColours" title="The results table is colour coded by default showing how relatively hard (red) or easy (green) each of the gears is to pedal in. On some displays the colouring makes the text tricky to read so you can toggle the colouring here.">Show colours</label>
					<input type="checkbox" id="showColours" name="showColours" checked="checked">
				</div>
				<p id="derailleurCapacity"></p>
				
				<!--<table data-role="table" data-mode="reflow" class="ui-responsive table-stroke resultTable">-->
				<table class="resultTable portrait">
					<thead>
						<tr>
							<th></th>
							<?php
							for ($chainring=1;$chainring<=$maxChainrings;$chainring++) echo '<th class="chainringHeading chainring'.$chainring.'">Chainring '.$chainring.'</th>';
							?>
						</tr>
					</thead>
					<tbody>
					<?php
					for ($sprocket=1;$sprocket<=$maxSprockets;$sprocket++) {
						echo '<tr class="sprocketHeading sprocket'.$sprocket.'">';
						echo '<th class="sprocket'.$sprocket.'">Sprocket '.$sprocket.'</th>';
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
								echo '<th class="class="sprocketHeading sprocket'.$sprocket.'">Sprocket '.$sprocket.'</th>';
							}
							?>
						</tr>
					</thead>
					<tbody>
					<?php
							for ($chainring=1;$chainring<=$maxChainrings;$chainring++) {
								echo '<tr class="chainringHeading chainring'.$chainring.'">';
								echo '<th class="chainring'.$chainring.'">Chainring '.$chainring.'</th>';
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
		
		
		
		<div data-role="page" id="infoPage">
			<div data-role="main" class="ui-content">
				<a href="#resultPage" class="ui-btn ui-btn-inline ui-icon-grid ui-btn-icon-left ui-mini ui-shadow">Results</a>
				<h1>Gear Ratio Calculator</h1>
				<!--
				<p>Use this tool to calculate info about your gear ratios. Just enter the tooth count of your chainring/s and gear/s to see the gear ratios available.</p>
				<p>ED - Effective Diameter describes the equivalent diameter that the wheel would be with this gear ratio if the pedals were fixed directly to the wheel</p>
				<p>MD - Metres of Development (aka <a target="_blank" href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development">Relationship to metres of development</a>)  describe the distance travelled for each pedal revolution</p>
				<p>GI - Gear Inches describes gear ratios in terms of the diameter of an equivalent directly driven wheel</p>
				<p>Speed/cadence - optionally add a target cadence to see the speed each gear will achieve at that cadence</p>
				<p>Derailleur capacity - this will tell you what capacity derailleur you should be looking for based upon your crank &amp; cassette. The formula to calculate it is <i>(largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)</i></p>
				-->
				<ul>
					<!--<li><b>ED:</b> Effective Diameter describes the equivalent diameter that the wheel would be with this gear ratio if the pedals were fixed directly to the wheel</li>-->
					<li><b>Ratio:</b> Metres of Development (<a href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development" target="_blank" title="Click here for Wikipedias explanation">wiki</a>)  describes the distance travelled for each pedal revolution</li>
					<li><b>MD:</b> Metres of Development (<a href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development" target="_blank" title="Click here for Wikipedias explanation">wiki</a>)  describes the distance travelled for each pedal revolution</li>
					<li><b>GI:</b> Gear Inches (<a href="https://en.wikipedia.org/wiki/Gear_inches" target="_blank" title="Click here for Wikipedias explanation">wiki</a>) also known as Effective Diameter, describes gear ratios in terms of the diameter of an equivalent directly driven wheel. The formula to calculate it is <i>(wheel + tyre diameter in inches) &times; (chainring toothcount &divide; sprocket toothcount)</i></li></li>
					<li><b>GR:</b> Gain Ratio is a Sheldon Brown innovation. Traditional measurements (GI, MD etc.) don't allow for the dis/advantage of pedal arm (crank) length and also make easy comparison of different gearing tricky (46/16 is the same as 53/19 - if the crank lengths are the same) so Sheldon Brown proposed a gear measurement system called <a href="http://sheldonbrown.com/gain.html" target="_blank" title="Click here for Sheldons explanation!">&quot;gain ratio&quot;</a>. It describes the ratio of distance travelled by the bike relative to the radial distance moved of the pedals. His formula is <i>((wheel + tyre radius) &divide; crank length) &times; gear ratio</i>. The benefits of this include:
						<ol>
							<li>Given 2 identically geared/wheeled bikes you can see a numerical representation of the mechanical dis/advantage if they have different crank lengths.</li>
							<li>It's <a href="https://en.wikipedia.org/wiki/Dimensionless_quantity" target="_blank">dimensionless</a>, so whether you supply the measurements in inches, mm or microns the resulting value is the same.</li>
							<li>It&#39;s like a universal language for comparing gearing, all reduced to a single number!</li>
						</ol></li>
					<li><b>Speed/cadence:</b> Optionally add a target cadence to see the speed each gear will achieve at that cadence</li>
					<li><b>Derailleur capacity:</b> This will tell you what capacity derailleur you should be looking for based upon your crank &amp; cassette. The formula to calculate it is <i>(largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)</i></li>
				</ul>
			</div>
		</div>
	</body>
</html>