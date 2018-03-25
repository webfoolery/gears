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
			div.resultBox {text-shadow:none;font-size:0.8em;}
			.cadenceInput {width:200px!important;} */
			
			body {font-family:arial,helvetica,sans-serif;font-size:1em;}
			input.sprockets, input.chainrings {width:90px;}
			table.resultTable {border-collapse:collapse;}
			table.resultTable th, table.resultTable td {border:1px solid #777;}
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
					targetCadence: 0,
					units: metric,
					varDump: function(dumpData, title) {
						// console.log(this);
						console.log(title, (dumpData ? JSON.parse(JSON.stringify(this)) : ''));
					},
					initialise: function() {
						var self = this;
						this.resetTable();
						$("#clearAll").click(function(){self.clearAll()});
						$("#setMyDefaults").click(function(){
							self.setMyDefaults()
							self.recalculate()
						});
						$("input").change(function(){
							if (this.name.indexOf('targetCadence') === 0) self.targetCadence = Number(this.value);
							// else if (this.name.indexOf('chainring') === 0) self.recalculate();
							// else if (this.name.indexOf('sprocket') === 0) self.recalculate();
							// else if (this.name.indexOf('unitToggler') === 0) self.recalculate();
							if (self.debug) self.varDump(false, 'Input changed');
							self.recalculate()
						});
						$("select").change(function(){
							if (this.name.indexOf('tyreDiameter') === 0) self.tyreDiameter = Number(this.value);
							else if (this.name.indexOf('chainringCount') === 0) self.chainringCount = Number(this.value);
							else if (this.name.indexOf('sprocketCount') === 0) self.sprocketCount = Number(this.value);
							else if (this.name.indexOf('wheelDiameter') === 0) self.wheelDiameter = Number(this.value);
							if (self.debug) self.varDump(false, 'Select changed');
							self.recalculate()
						});
						// this.setMyDefaults();
					},
					setMyDefaults: function() {
						// SET UP MY DEFAULT VALUES
						$('[name=wheelDiameter]').val(622);
						$('[name=tyreDiameter]').val(50);
						$('[name=targetCadence]').val(90);
						$('[name=sprocketCount]').val(9);
						$('[name=chainringCount]').val(3);
						var sprockets = [11,12,14,15,17,19,21,24,27]
						var chainrings = [50,39,30];
						this.chainringCount = 3;
						this.sprocketCount = 9;
						for (c = 1; c < 4; c++) $('[name=chainring'+c+']').val(chainrings[c-1]);
						for (s = 1; s < 10; s++) $('[name=sprocket'+s+']').val(sprockets[s-1]);
						if (this.debug) this.varDump(true, 'setMyDefaults complete');
						this.recalculate();
					},
					recalculate: function() {
						this.resetTable();
						this.getChainrings();
						this.getSprockets();
						this.wheelDiameter = Number($('[name=wheelDiameter]').val());
						this.tyreDiameter = Number($('[name=tyreDiameter]').val());
						this.targetCadence = Number($('[name=targetCadence]').val());
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
											output += '<br />ED='+this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio), 'mm');
											output += '<br />GI='+(((this.wheelDiameter + this.tyreDiameter) / 25.4) * ratio).toFixed(0) + 'in';
											if (this.targetCadence) {
												var speed = (((this.wheelDiameter + this.tyreDiameter) * 3.141) * ratio * this.targetCadence * 60) / 1000000;
												output += '<br />'+this.getUnitValue(speed,'km/h')+'@'+this.targetCadence+'rpm';
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
								derailleurCapacityText = derailleurCapacityVal + ' ('+Math.max.apply(Math, this.chainrings)+'-'+Math.min.apply(Math, this.chainrings)+'+'+Math.max.apply(Math, this.sprockets)+'-'+Math.min.apply(Math, this.sprockets)+')';
								$('#derailleurCapacity').text('Derailleur capacity required: '+derailleurCapacityText).show();
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
							var sprocket = Number($(this).val())
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
						$('[name=wheelDiameter]').val(0);
						$('[name=chainringCount]').val(0);
						$('[name=sprocketCount]').val(0);
						$('[name=tyreDiameter]').val(0);
						$('[name=targetCadence]').val('');
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
				<a href="#infoPage">Info</a>
				<a href="#settingsPage">Settings</a>
				<button type="button" id="clearAll">Clear data</button>
				<button type="button" id="setMyDefaults">My settings</button>
				<br />
				<p><label for="chainringCount">Chainrings:</label>
				<input class="chainrings" placeholder="Chainring 1" type="number" name="chainring1" />
				<input class="chainrings" placeholder="Chainring 2" type="number" name="chainring2" />
				<input class="chainrings" placeholder="Chainring 3" type="number" name="chainring3" />
				</p>
				<p>
				<label for="sprocketCount">Sprockets:</label>
				<?php for($x=1;$x<12;$x++) echo '<input placeholder="Sprocket '.$x.'" class="sprockets sprocket'.$x.'" name="sprocket'.$x.'" type="number" />'; ?>
				</p>
				<p>
				<label for="wheelDiameter">Wheel Diameter (mm):</label>
				<select name="wheelDiameter" data-inline="true">
					<option value="0">Select</option>
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
				</p>
				<p>
				<label for="tyreDiameter">Tyre Diameter (mm):</label>
				<select name="tyreDiameter" data-inline="true">
					<option value="0">Select</option>
					<?php foreach(array('18c'=>36,'23c'=>46,'25c'=>50,'28c'=>56,'32c'=>64,'35c'=>70,'47c'=>94) as $tyre=>$diameter) echo '<option value="'.$diameter.'"'.($preselect && $diameter==50 ? ' selected="selected"' : '').'>'.$tyre.'</option>'; ?>
				</select>
				</p>
				<p>
				<label for="targetCadence">Target cadence (rpm):</label>
				<input data-inline="true" data-wrapper-class="cadenceInput" name="targetCadence" placeholder="Target cadence (rpm)" type="number"<?php echo ($preselect ? 'value="90"' : ''); ?> />
				</p>
				<p>
				<label for="unitToggler">Units:</label>
				<input type="radio" name="unitToggler" id="metric" value="metric" checked="checked">
				<label for="metric">Metric</label>
				<input type="radio" name="unitToggler" id="imperial" value="imperial">
				<label for="imperial">Imperial</label>
				</p>
				<p>
				<label for="showColours">Show colours</label>
				<input type="checkbox" id="showColours" name="showColours" checked="checked">
				</p>
				<p id="derailleurCapacity"></p>
				
				<table class="resultTable">
					<tr>
						<th>Summary</th>
						<?php
						for ($chainring=1;$chainring<=$maxChainrings;$chainring++) echo '<th class="chainringHeading chainring'.$chainring.'">Chainring '.$chainring.'</th>';
						?>
					</tr>
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
				</table>
			</div>
		</div>
		
		
		
		<div data-role="page" id="infoPage">
			<div data-role="main" class="ui-content">
				<a href="#settingsPage" class="ui-btn ui-btn-inline ui-icon-gear ui-btn-icon-left ui-mini ui-shadow">Settings</a>
				<a href="#resultPage" class="ui-btn ui-btn-inline ui-icon-grid ui-btn-icon-left ui-mini ui-shadow">Results</a>
				<h1>Gear Ratio Calculator</h1>
				<p>Use this tool to calculate info about your gear ratios. Just enter the tooth count of your chainring/s and gear/s to see the gear ratios available.</p>
				<p>ED - Effective Diameter describes the equivalent diameter that the wheel would be with this gear ratio if the pedals were fixed directly to the wheel</p>
				<p>MD - Metres of Development (aka <a target="_blank" href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development">Relationship to metres of development</a>)  describe the distance travelled for each pedal revolution</p>
				<p>GI - Gear Inches describes gear ratios in terms of the diameter of an equivalent directly driven wheel</p>
				<p>Speed/cadence - optionally add a target cadence to see the speed each gear will achieve at that cadence</p>
				<p>Derailleur capacity - this will tell you what capacity derailleur you should be looking for based upon your crank &amp; cassette. The formula to calculate it is <i>(largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)</i></p>
			</div>
		</div>
	</body>
</html>